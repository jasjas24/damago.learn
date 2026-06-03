<?php
require_once 'db.php';
require_once 'host_auth.php';
header('Content-Type: application/json');

$lobby_id   = (int)($_GET['lobby_id'] ?? 0);
$host_token = $_GET['host_token'] ?? $_POST['host_token'] ?? null;

// Nur mit gültigem Host-Token darf ein Spiel gestartet werden (LH 27.5).
if (!verify_host_token($pdo, $lobby_id, $host_token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Keine Host-Berechtigung']);
    exit;
}

if ($lobby_id > 0) {
    try {
        // is_started setzen UND den Startzeitpunkt der ersten Frage serverseitig stempeln,
        // damit der Server die maßgebliche Zeitquelle ist (LH 13.4 / 26.3).
        $stmt = $pdo->prepare("UPDATE quiz_lobbies SET is_started = 1, current_question_started_at = NOW() WHERE id = ?");
        $stmt->execute([$lobby_id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}