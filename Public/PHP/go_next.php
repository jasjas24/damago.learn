<?php
require_once 'init.php';
require_once 'db.php';
require_once 'host_auth.php';

// Holt den Host-Token und die Lobby, damit wir prüfen können, ob hier wirklich der Host weiterschaltet.
$host_token = $_POST['host_token'] ?? $_SESSION['quiz_setup']['host_token'] ?? null;
$lobby_id   = $_SESSION['quiz_setup']['lobby_id'] ?? null;

// Hat der Host keine Session mehr (Seite war zu), stellen wir sie über den Token wieder her.
if (!$lobby_id && $host_token) {
    $lobbyByToken = find_lobby_by_host_token($pdo, $host_token);
    if ($lobbyByToken) {
        restore_host_session($lobbyByToken, $host_token);
        $lobby_id = (int)$lobbyByToken['id'];
    }
}

// Ohne gültigen Token darf niemand weiterschalten, dann zurück aufs Dashboard.
if (!verify_host_token($pdo, $lobby_id, $host_token)) {
    header("Location: dashboard.php");
    exit;
}

if ($lobby_id) {
    // Eine Frage weiter und die alte Auflösung wieder ausblenden.
    $_SESSION['current_question_index'] = ($_SESSION['current_question_index'] ?? 0) + 1;
    $_SESSION['show_explanation'] = false;
    $_SESSION['last_result'] = null;

    try {
        // Neuen Stand speichern und den Startzeitpunkt der Frage neu setzen, damit der Server die Zeit misst.
        $stmt = $pdo->prepare("UPDATE quiz_lobbies SET current_question_index = ?, show_explanation = 0, current_question_started_at = NOW() WHERE id = ?");
        $stmt->execute([$_SESSION['current_question_index'], $lobby_id]);
    } catch (PDOException $e) {
        // Geht das Speichern schief, machen wir trotzdem weiter.
    }
}

// Zurück in die Spielansicht.
header("Location: game.php");
exit;
