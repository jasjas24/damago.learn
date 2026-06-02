<?php
require_once 'init.php';
require_once 'db.php';

/** @var string $username */
/** @var string $role */

// 1. Prüfen, ob wir über das Host-Setup kommen oder als Spieler per Join
if (isset($_SESSION['quiz_setup'])) {
    // Ansicht für den Host
    $setup = $_SESSION['quiz_setup'];
    $code = $_GET['code'] ?? $_POST['code'] ?? null;

    if (!$code) {
        header("Location: setup_lobby.php");
        exit;
    }
    $lobby_id = $setup['lobby_id'] ?? null;
    $is_host = true;
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

// Host-Name dieser Lobby laden, um den Host in der Teilnehmerliste zu markieren
$hostName = '';
try {
    $stmtHost = $pdo->prepare("SELECT host_name FROM quiz_lobbies WHERE id = ?");
    $stmtHost->execute([$lobby_id]);
    $hostName = (string)$stmtHost->fetchColumn();
} catch (PDOException $e) {}

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

        // 5. In der Session des Hosts verankern
        $_SESSION['quiz_questions'] = $quizQuestions;
        $_SESSION['current_question_index'] = 0;
        if (!isset($_SESSION['quiz_score'])) { $_SESSION['quiz_score'] = 0; }

        // 6. NEU: Die fertig gemischte Fragen- und Antwortstruktur als JSON in der Lobby hinterlegen
        // Damit deine quiz_lobbies-Tabelle das speichern kann, nutzen wir die Spalte 'quiz_data' (falls vorhanden) 
        // oder missbrauchen alternativ ein anderes ungenutztes Textfeld. Am saubersten fügst du deiner Tabelle 
        // `quiz_lobbies` eine TEXT-Spalte namens `quiz_data` hinzu.
        
        // Zuerst schauen, ob für die Tabelle `lobby_questions` die Zuordnung geschrieben werden muss
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM lobby_questions WHERE lobby_id = ?");
        $stmtCheck->execute([$lobby_id]);

        if ((int)$stmtCheck->fetchColumn() === 0) {
            // Die IDs eintragen (wie gewohnt)
            $stmtInsertQ = $pdo->prepare("INSERT INTO lobby_questions (lobby_id, question_id, sort_order) VALUES (?, ?, ?)");
            foreach ($quizQuestions as $index => $q) {
                $stmtInsertQ->execute([$lobby_id, $q['id'], $index]);
            }
            
            // JETZT NEU: Den kompletten gemischten Array-Stapel als JSON in die Lobby-Tabelle jagen!
            // Falls du die Spalte 'quiz_data' noch nicht hast, kannst du sie fix in MariaDB per ALTER TABLE hinzufügen.
            try {
                $stmtJson = $pdo->prepare("UPDATE quiz_lobbies SET quiz_data = ? WHERE id = ?");
                $stmtJson->execute([json_encode($quizQuestions, JSON_UNESCAPED_UNICODE), $lobby_id]);
            } catch (PDOException $e) {
                // Falls die Spalte quiz_data noch fehlt, fangen wir es ab, damit es nicht abstürzt
            }
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

            <div class="info-list">
                <div class="info-list-code">Teilnahme-Code: <?php echo htmlspecialchars($code); ?></div>
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

        function updateLobby() {
            fetch('get_lobby_status.php?lobby_id=' + lobbyId)
                .then(response => response.json())
                .then(data => {
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
                        list.appendChild(li);
                    });

                    if (data.is_started === 1) {
                        window.location.href = 'game.php';
                    }
                })
                .catch(err => console.error("Fehler beim Aktualisieren der Lobby:", err));
        }

        function startQuiz() {
            console.log("Start-Button geklickt. Sende Anfrage für Lobby-ID:", lobbyId);

            fetch('start_lobby_action.php?lobby_id=' + lobbyId, {
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