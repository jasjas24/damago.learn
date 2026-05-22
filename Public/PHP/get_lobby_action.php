<?php
require_once 'db.php';
header('Content-Type: application/json');

$lobby_id = (int)($_GET['lobby_id'] ?? 0);

if ($lobby_id > 0) {
    try {
        $stmt = $pdo->prepare("UPDATE quiz_lobbies SET is_started = 1 WHERE id = ?");
        $stmt->execute([$lobby_id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}