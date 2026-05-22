<?php
require_once 'db.php';
header('Content-Type: application/json');

$lobby_id = (int)($_GET['lobby_id'] ?? 0);

if ($lobby_id > 0) {
    try {
        // Status der Lobby holen
        $stmtLobby = $pdo->prepare("SELECT is_started FROM quiz_lobbies WHERE id = ?");
        $stmtLobby->execute([$lobby_id]);
        $is_started = (int)$stmtLobby->fetchColumn();

        // Spieler holen
        $stmtPlayers = $pdo->prepare("SELECT player_name FROM lobby_players WHERE lobby_id = ? ORDER BY joined_at ASC");
        $stmtPlayers->execute([$lobby_id]);
        $players = $stmtPlayers->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'is_started' => $is_started,
            'players' => $players
        ]);
    } catch (PDOException $e) {
        echo json_encode(['is_started' => 0, 'players' => []]);
    }
} else {
    echo json_encode(['is_started' => 0, 'players' => []]);
}