<?php
require_once 'init.php';
require_once 'db.php';

$lobby_id = $_SESSION['quiz_setup']['lobby_id'] ?? $_SESSION['player_lobby_id'] ?? null;
$username = $_SESSION['player_name'] ?? $_SESSION['username'] ?? 'Gast';
$current_index = $_SESSION['current_question_index'] ?? 0;
$questions = $_SESSION['quiz_questions'] ?? [];
$current_question = $questions[$current_index] ?? null;

if ($lobby_id && $current_question) {
    // 1. Deine bestehende Punkte-Auswertung hier machen ...
    // (Lass deine Berechnungen für $_SESSION['last_result'] und $_SESSION['quiz_score'] genau so laufen wie vorher!)
    
    // Angenommen hier ist deine Punkteberechnung...
    $_SESSION['waiting_for_reveal'] = true; // Flag: Spieler hat abgegeben und wartet

    try {
        // 2. In DB eintragen, dass dieser Spieler geantwortet hat
        $stmt = $pdo->prepare("INSERT IGNORE INTO player_answers (lobby_id, question_id, player_name) VALUES (?, ?, ?)");
        $stmt->execute([$lobby_id, $current_question['id'], $username]);

        // 3. Prüfen, ob alle abgegeben haben oder ob es ein Timeout war
        $is_timeout = isset($_POST['timeout']) && $_POST['timeout'] == '1';

        // Spieler zählen
        $stmtPlayers = $pdo->prepare("SELECT COUNT(*) FROM lobby_players WHERE lobby_id = ?");
        $stmtPlayers->execute([$lobby_id]);
        $totalPlayers = (int)$stmtPlayers->fetchColumn();

        // Antworten zählen
        $stmtAnswers = $pdo->prepare("SELECT COUNT(*) FROM player_answers WHERE lobby_id = ? AND question_id = ?");
        $stmtAnswers->execute([$lobby_id, $current_question['id']]);
        $answeredPlayers = (int)$stmtAnswers->fetchColumn();

        // Wenn alle fertig sind ODER die Zeit ablief -> Auflösung für alle freischalten!
        if ($answeredPlayers >= $totalPlayers || $is_timeout) {
            $stmtUpdate = $pdo->prepare("UPDATE quiz_lobbies SET show_explanation = 1 WHERE id = ?");
            $stmtUpdate->execute([$lobby_id]);
        }

    } catch (PDOException $e) {
        // Fehlerhandling
    }
}

// WICHTIG: Zurück zur game.php. Die game.php entscheidet per Polling, wann die Auflösung sichtbar wird!
header("Location: game.php");
exit;