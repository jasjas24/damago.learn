<?php
require_once 'init.php';
require_once 'db.php';

// Nur der Host darf kicken
if (!isset($_SESSION['quiz_setup']['lobby_id'])) {
    exit('Unauthorized');
}

$lobby_id = $_SESSION['quiz_setup']['lobby_id'];
$player_to_kick = $_POST['username'] ?? '';

if (!empty($player_to_kick)) {
    $stmt = $pdo->prepare("DELETE FROM lobby_players WHERE lobby_id = ? AND player_name = ?");
    $stmt->execute([$lobby_id, $player_to_kick]);
    echo json_encode(['success' => true]);
}