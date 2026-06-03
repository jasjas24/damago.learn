<?php
require_once 'init.php';
require_once 'db.php';
require_once 'host_auth.php';

/** @var string $username */
/** @var string $role */

// Host-Rückkehr per Host-Token (LH 9.4): Existiert keine Host-Session, aber ein gültiger
// Host-Token in der URL, wird die Host-Session aus der DB wiederhergestellt.
if (!isset($_SESSION['quiz_setup']) && !empty($_GET['host_token'])) {
    $lobbyByToken = find_lobby_by_host_token($pdo, $_GET['host_token']);
    if ($lobbyByToken) {
        restore_host_session($lobbyByToken, $_GET['host_token']);
        // Ist das Spiel bereits gestartet, direkt zur Spielansicht.
        if ((int)$lobbyByToken['is_started'] === 1) {
            header("Location: game.php");
            exit;
        }
    }
}

// 1. Prüfen, ob wir über das Host-Setup kommen oder als Spieler per Join
if (isset($_SESSION['quiz_setup'])) {
    // Ansicht für den Host
    $setup = $_SESSION['quiz_setup'];
    $code = $_GET['code'] ?? $_POST['code'] ?? ($setup['code'] ?? null);

    if (!$code) {
        header("Location: setup_lobby.php");
        exit;
    }
    $lobby_id = $setup['lobby_id'] ?? null;
    $is_host = true;
    $hostToken = $setup['host_token'] ?? '';
} elseif (isset($_SESSION['player_lobby_id'])) {
    // Ansicht für den beitretenden Spieler / Gast
    $lobby_id = $_SESSION['player_lobby_id'];
    $is_host = false;

    // Code aus der DB holen für die Anzeige
    $stmtCode = $pdo->prepare("SELECT join_code FROM quiz_lobbies WHERE id = ?");
    $stmtCode->execute([$lobby_id]);
    $code = $stmtCode->fetchColumn();
} else {
    header("Location: dashboard.php");
    exit;
}

// Host-Name und Lobby-Metadaten (Pool, Anzahl, Zeit, Punkte-Modus) laden, für Host und Spieler.
$hostName  = '';
$lobbyMeta = null;
try {
    $stmtHost = $pdo->prepare("SELECT host_name, question_pool, question_count, time_limit, point_mode FROM quiz_lobbies WHERE id = ?");
    $stmtHost->execute([$lobby_id]);
    $lobbyMeta = $stmtHost->fetch(PDO::FETCH_ASSOC);
    $hostName  = (string)($lobbyMeta['host_name'] ?? '');
} catch (PDOException $e) {}

// Lesbare Bezeichnungen für den Punkte-Modus
$pointModeLabels = [
    'all_or_nothing' => 'Ganz oder gar nicht',
    'partial'        => 'Teilpunkte',
    'time_bonus'     => 'Zeitbonus',
];

// 2. NUR DER HOST generiert und mischt die Fragen
if ($is_host && !isset($_SESSION['quiz_questions'])) {
    $pool  = $setup['pool'];
    $count = $setup['count'];

    try {
        $stmt = $pdo->prepare("
            SELECT
                q.id AS question_id,
                q.question_text,
                q.explanation AS general_explanation,
                m.file_name AS image_file,
                a.id AS answer_id,
                a.answer_text,
                a.is_correct,
                a.explanation AS answer_explanation,
                a.sort_order
            FROM questions q
            INNER JOIN question_pools p ON p.id = q.question_pool_id
            INNER JOIN answer_options a ON a.question_id = q.id
            LEFT JOIN media_files m ON m.id = q.image_id
            WHERE p.name = ? AND q.is_active = 1
            ORDER BY q.id ASC, a.sort_order ASC
        ");
        $stmt->execute([$pool]);
        $rawResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $questions = [];
        foreach ($rawResults as $row) {
            $qId = $row['question_id'];
            if (!isset($questions[$qId])) {
                $questions[$qId] = [
                    'id'            => $qId,
                    'question_text' => $row['question_text'],
                    'explanation'   => $row['general_explanation'],
                    'image'         => $row['image_file'] ?? null,
                    'answers'       => []
                ];
            }
            $questions[$qId]['answers'][] = [
                'id'          => $row['answer_id'],
                'text'        => $row['answer_text'],
                'is_correct'  => $row['is_correct'],
                'explanation' => $row['answer_explanation'],
                'sort_order'  => $row['sort_order']
            ];
        }
        $questions = array_values($questions);

        // Fragen mischen und zuschneiden
        shuffle($questions);
        $quizQuestions = array_slice($questions, 0, $count);

        // Antworten mischen (Passiert hier einmalig für alle)
        foreach ($quizQuestions as &$singleQuestion) {
            shuffle($singleQuestion['answers']);
        }
        unset($singleQuestion);

        // 5. In der Session des Hosts verankern (vorläufig der frisch gemischte Stapel)
        $_SESSION['quiz_questions'] = $quizQuestions;
        $_SESSION['current_question_index'] = 0;
        if (!isset($_SESSION['quiz_score'])) { $_SESSION['quiz_score'] = 0; }

        // 6. Fragen-Zuordnung einmalig speichern (nur beim allerersten Mal)
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM lobby_questions WHERE lobby_id = ?");
        $stmtCheck->execute([$lobby_id]);
        if ((int)$stmtCheck->fetchColumn() === 0) {
            $stmtInsertQ = $pdo->prepare("INSERT INTO lobby_questions (lobby_id, question_id, sort_order) VALUES (?, ?, ?)");
            foreach ($quizQuestions as $index => $q) {
                $stmtInsertQ->execute([$lobby_id, $q['id'], $index]);
            }
        }

        // 7. Gemischte Reihenfolge pro Spiel verbindlich festhalten (gleich für ALLE, LH 11.4):
        //    Ist bereits eine Reihenfolge in quiz_data gespeichert, wird diese übernommen
        //    (auch für den Host), so sehen Host und Spieler garantiert dieselbe Reihenfolge.
        //    Sonst wird die frisch gemischte Reihenfolge gespeichert.
        try {
            $stmtQd = $pdo->prepare("SELECT quiz_data FROM quiz_lobbies WHERE id = ?");
            $stmtQd->execute([$lobby_id]);
            $existingQd = $stmtQd->fetchColumn();
            $decodedQd = $existingQd ? json_decode($existingQd, true) : null;

            if (!empty($decodedQd)) {
                // Bereits gespeicherte, verbindliche Reihenfolge auch für den Host nutzen
                $_SESSION['quiz_questions'] = $decodedQd;
            } else {
                $stmtJson = $pdo->prepare("UPDATE quiz_lobbies SET quiz_data = ? WHERE id = ?");
                $stmtJson->execute([json_encode($quizQuestions, JSON_UNESCAPED_UNICODE), $lobby_id]);
            }
        } catch (PDOException $e) {
            // quiz_data-Schreiben/Lesen darf den Spielstart nicht blockieren
        }

    } catch (PDOException $e) {
        die("Fehler beim Vorbereiten der Fragen: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lobby | damago Quizsystem</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body class="auth-page">

    <div class="page-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <?php include_once 'topbar.php'; ?>

    <main class="auth-layout">
        <section class="auth-info">
            <h1><?php echo $is_host ? 'Host-Lobby.' : 'Spieler-Lobby.'; ?></h1>
            <p>
                Teile diesen Code mit den Teilnehmern.
                Sie müssen ihn eingeben, um der Lobby beizutreten.
            </p>

            <div class="info-list lobby-info-list">
                <div class="info-list-code lobby-meta-item"><span class="lobby-meta-label">Teilnahme-Code:</span><span class="join-code-value"><?php echo htmlspecialchars($code); ?></span></div>
                <?php if ($lobbyMeta): ?>
                    <div class="lobby-meta-item"><span class="lobby-meta-label">Fragenpool:</span><span class="lobby-meta-value"><?php echo htmlspecialchars(ucfirst((string)($lobbyMeta['question_pool'] ?? '—'))); ?></span></div>
                    <div class="lobby-meta-item"><span class="lobby-meta-label">Anzahl Fragen:</span><span class="lobby-meta-value"><?php echo (int)($lobbyMeta['question_count'] ?? 0); ?></span></div>
                    <div class="lobby-meta-item"><span class="lobby-meta-label">Zeitlimit:</span><span class="lobby-meta-value"><?php echo (int)($lobbyMeta['time_limit'] ?? 0); ?> Sekunden</span></div>
                    <div class="lobby-meta-item"><span class="lobby-meta-label">Punkte-Modus:</span><span class="lobby-meta-value"><?php echo htmlspecialchars($pointModeLabels[$lobbyMeta['point_mode']] ?? (string)($lobbyMeta['point_mode'] ?? '—')); ?></span></div>
                <?php endif; ?>
            </div>
        </section>

        <section class="auth-card">
            <div class="auth-header">
                <span class="eyebrow">Lobby active</span>
                <h2>Teilnehmer (<span id="player-count">0</span>)</h2>
                <p>Hier siehst du alle verbundenen Spieler:</p>
            </div>

            <ul id="players" class="player-list"></ul>

            <?php if ($is_host): ?>
                <button class="btn btn-primary" onclick="startQuiz()">
                    Quiz für alle starten
                </button>
                <button type="button" class="btn btn-abort" onclick="abortGame()">
                    Spiel abbrechen
                </button>
            <?php else: ?>
                <div class="status-message">Warte darauf, dass der Host das Spiel startet...</div>
            <?php endif; ?>

            <div class="auth-links">
                <a href="dashboard.php">Zurück zum Dashboard</a>
            </div>
        </section>
    </main>

    <script>
        const lobbyId = <?php echo (int)$lobby_id; ?>;
        const isHost = <?php echo $is_host ? 'true' : 'false'; ?>;
        const hostName = <?php echo json_encode($hostName); ?>;
        const hostToken = <?php echo json_encode($hostToken ?? ''); ?>;

        // Holt regelmäßig den Lobby-Stand vom Server und baut die Teilnehmerliste neu auf.
        function updateLobby() {
            fetch('get_lobby_status.php?lobby_id=' + lobbyId)
                .then(response => response.json())
                .then(data => {
                    // Host hat das Spiel abgebrochen, also die Teilnehmer hinausleiten.
                    if (data.is_aborted === 1) {
                        if (!isHost) {
                            alert('Der Host hat das Spiel abgebrochen.');
                            window.location.href = 'dashboard.php';
                        }
                        return;
                    }

                    const list = document.getElementById('players');
                    const count = document.getElementById('player-count');
                    list.innerHTML = '';
                    count.innerText = data.players.length;

                    data.players.forEach(p => {
                        const li = document.createElement('li');
                        li.className = 'player-item';

                        // Avatar-Bild anzeigen (falls vorhanden)
                        if (p.avatar) {
                            const img = document.createElement('img');
                            img.className = 'player-avatar';
                            img.src = '../Uploads/Avatare/' + encodeURIComponent(p.avatar);
                            img.alt = 'Avatar';
                            li.appendChild(img);
                        }

                        const nameSpan = document.createElement('span');
                        nameSpan.className = 'player-name';
                        nameSpan.textContent = p.player_name;
                        li.appendChild(nameSpan);

                        // Den Host in der Teilnehmerliste mit "(Host)" markieren
                        if (hostName && p.player_name === hostName) {
                            const badge = document.createElement('small');
                            badge.className = 'host-badge';
                            badge.textContent = '(Host)';
                            li.appendChild(badge);
                        }

                        // Host darf andere Teilnehmer aus der Lobby entfernen (LH 9.5)
                        if (isHost && p.player_name !== hostName) {
                            const kickBtn = document.createElement('button');
                            kickBtn.type = 'button';
                            kickBtn.className = 'kick-btn';
                            kickBtn.textContent = 'Entfernen';
                            kickBtn.addEventListener('click', function () { kickPlayer(p.player_name); });
                            li.appendChild(kickBtn);
                        }

                        list.appendChild(li);
                    });

                    if (data.is_started === 1) {
                        window.location.href = 'game.php';
                    }
                })
                .catch(err => console.error("Fehler beim Aktualisieren der Lobby:", err));
        }

        // Host bricht das Spiel ab (Autorisierung serverseitig über den Host-Token).
        function abortGame() {
            if (!confirm('Willst du das Spiel wirklich abbrechen? Alle Teilnehmer werden entfernt.')) return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'abort_game.php';
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'host_token';
            input.value = hostToken;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }

        // Teilnehmer aus der Lobby entfernen (Autorisierung serverseitig über den Host-Token).
        function kickPlayer(name) {
            if (!confirm('Willst du ' + name + ' wirklich aus der Lobby entfernen?')) return;
            fetch('kick_player.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'username=' + encodeURIComponent(name) + '&host_token=' + encodeURIComponent(hostToken)
            })
            .then(() => updateLobby())
            .catch(err => console.error("Fehler beim Entfernen:", err));
        }

        // Startet das Spiel für alle und schickt den Host danach in die Spielansicht.
        function startQuiz() {
            console.log("Start-Button geklickt. Sende Anfrage für Lobby-ID:", lobbyId);

            fetch('start_lobby_action.php?lobby_id=' + lobbyId + '&host_token=' + encodeURIComponent(hostToken), {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'game.php';
                } else {
                    alert("Der Server hat das Starten verweigert.");
                }
            })
            .catch(err => console.error("Netzwerkfehler beim Starten des Quiz:", err));
        }

        setInterval(updateLobby, 2000);
        updateLobby();
    </script>
    <?php include_once 'footbar.php'; ?>

</body>
</html>