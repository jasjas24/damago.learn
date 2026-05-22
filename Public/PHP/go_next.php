<?php
require_once 'init.php';
require_once 'db.php';

$lobby_id = $_SESSION['quiz_setup']['lobby_id'] ?? null;

if ($lobby_id) {
    $_SESSION['current_question_index'] = ($_SESSION['current_question_index'] ?? 0) + 1;
    $_SESSION['show_explanation'] = false;
    $_SESSION['last_result'] = null;

    try {
        // Index erhöhen UND show_explanation zurücksetzen!
        $stmt = $pdo->prepare("UPDATE quiz_lobbies SET current_question_index = ?, show_explanation = 0 WHERE id = ?");
        $stmt->execute([$_SESSION['current_question_index'], $lobby_id]);
    } catch (PDOException $e) {
        // Fehlerbehandlung
    }
}

header("Location: game.php");
exit;