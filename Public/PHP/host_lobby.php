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
    // Falls kein Code da ist, leite den User z.B. zurück zum Dashboard oder Setup
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
    // Weder Host noch Spieler? Ab zurück zum Dashboard
    header("Location: dashboard.php");
    exit;
}

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
                a.id AS answer_id,
                a.answer_text,
                a.is_correct,
                a.explanation AS answer_explanation,
                a.sort_order
            FROM questions q
            INNER JOIN question_pools p ON p.id = q.question_pool_id
            INNER JOIN answer_options a ON a.question_id = q.id
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

        shuffle($questions);
        $quizQuestions = array_slice($questions, 0, $count);

        foreach ($quizQuestions as &$singleQuestion) {
            shuffle($singleQuestion['answers']);
        }
        unset($singleQuestion);

        // 5. In der Session des Hosts verankern
        $_SESSION['quiz_questions'] = $quizQuestions;
        $_SESSION['current_question_index'] = 0;
        if (!isset($_SESSION['quiz_score'])) { $_SESSION['quiz_score'] = 0; }

        // 6. NEU: Die Fragen-IDs für die Spieler in die Datenbank eintragen
        // Zuerst schauen, ob sie nicht schon drin sind (Dopplungen vermeiden bei Refresh)
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM lobby_questions WHERE lobby_id = ?");
        $stmtCheck->execute([$lobby_id]);
        
        if ((int)$stmtCheck->fetchColumn() === 0) {
            $stmtInsertQ = $pdo->prepare("INSERT INTO lobby_questions (lobby_id, question_id, sort_order) VALUES (?, ?, ?)");
            foreach ($quizQuestions as $index => $q) {
                $stmtInsertQ->execute([$lobby_id, $q['id'], $index]);
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
    <style>
        .player-list { list-style: none; padding: 0; margin: 20px 0; text-align: left; }
        .player-item { padding: 10px 15px; background: #f4f6f7; margin-bottom: 8px; border-radius: 4px; border-left: 5px solid #3498db; font-size: 1.1rem; }
        .status-message { font-style: italic; color: #7f8c8d; margin-top: 15px; }
    </style>
</head>
<body class="auth-page">

    <?php include_once 'topbar.php'; ?>

    <main class="auth-layout">
        <section class="auth-info">
            <h1><?php echo $is_host ? 'Host-Lobby.' : 'Spieler-Lobby.'; ?></h1>
            <p>
                Teile diesen Code mit den Teilnehmern.
                Sie müssen ihn eingeben, um der Lobby beizutreten.
            </p>

            <div class="info-list">
                <div style="font-size: 1.3rem; font-weight: bold;">Teilnahme-Code: <?php echo htmlspecialchars($code); ?></div>
            </div>
        </section>

        <section class="auth-card">
            <div class="auth-header">
                <span class="eyebrow">Lobby aktiv</span>
                <h2>Teilnehmer (<span id="player-count">0</span>)</h2>
                <p>Hier siehst du alle verbundenen Spieler:</p>
            </div>

            <ul id="players" class="player-list">
                </ul>

            <?php if ($is_host): ?>
                <button class="btn btn-primary" onclick="startQuiz()">
                    Quiz für alle starten
                </button>
            <?php else: ?>
                <div class="status-message">Warte darauf, dass der Host das Spiel startet...</div>
            <?php endif; ?>

            <div class="auth-links">
                <p>Zurück?</p>
                <a href="dashboard.php">Zurück zum Dashboard</a>
            </div>
        </section>
    </main>

    <script>
        const lobbyId = <?php echo (int)$lobby_id; ?>;
        const isHost = <?php echo $is_host ? 'true' : 'false'; ?>;

        function updateLobby() {
            // 1. Spielerliste laden und den Status der Lobby abfragen
            fetch('get_lobby_status.php?lobby_id=' + lobbyId)
                .then(response => response.json())
                .then(data => {
                    // Spielerliste aktualisieren
                    const list = document.getElementById('players');
                    const count = document.getElementById('player-count');
                    list.innerHTML = '';
                    count.innerText = data.players.length;

                    data.players.forEach(p => {
                        const li = document.createElement('li');
                        li.className = 'player-item';
                        li.innerText = p.player_name;
                        list.appendChild(li);
                    });

                    // 2. Wenn das Spiel gestartet wurde, leite zum Spiel weiter
                    if (data.is_started === 1) {
                        window.location.href = 'game.php';
                    }
                })
                .catch(err => console.error("Fehler beim Aktualisieren der Lobby:", err));
        }

        function startQuiz() {
    console.log("Start-Button geklickt. Sende Anfrage für Lobby-ID:", lobbyId);

    // Sende dem Server das Signal, die Lobby auf 'is_started = 1' zu setzen
    fetch('start_lobby_action.php?lobby_id=' + lobbyId, { 
        method: 'POST' 
    })
    .then(response => {
        console.log("Server-Response erhalten:", response);
        return response.json();
    })
    .then(data => {
        console.log("Daten vom Server:", data);
        if (data.success) {
            console.log("Erfolg! Leite weiter zur game.php");
            window.location.href = 'game.php';
        } else {
            alert("Der Server hat das Starten verweigert. Erfolg-Status: " + data.success);
        }
    })
    .catch(err => {
        console.error("Netzwerkfehler beim Starten des Quiz:", err);
        alert("Fehler bei der Netzwerk-Anfrage. Schau in die Browser-Konsole (F12)!");
    });
}

        // Alle 2 Sekunden die Lobby aktualisieren (Polling)
        setInterval(updateLobby, 2000);
        updateLobby(); // Sofort einmal ausführen
    </script>
</body>
</html>