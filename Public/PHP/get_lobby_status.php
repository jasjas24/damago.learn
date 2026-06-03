<?php
require_once 'db.php';
header('Content-Type: application/json');

// Gibt der Lobby per JSON den aktuellen Stand und die Spielerliste zurück, damit sie sich laufend aktualisiert.
$lobby_id = (int)($_GET['lobby_id'] ?? 0);

if ($lobby_id > 0) {
    try {
        // Status der Lobby holen (inkl. Abbruch-Status)
        $stmtLobby = $pdo->prepare("SELECT is_started, is_aborted FROM quiz_lobbies WHERE id = ?");
        $stmtLobby->execute([$lobby_id]);
        $lobbyRow = $stmtLobby->fetch(PDO::FETCH_ASSOC);
        $is_started = (int)($lobbyRow['is_started'] ?? 0);
        $is_aborted = (int)($lobbyRow['is_aborted'] ?? 0);

        // Spieler holen
        $stmtPlayers = $pdo->prepare("SELECT player_name, avatar FROM lobby_players WHERE lobby_id = ? ORDER BY joined_at ASC");
        $stmtPlayers->execute([$lobby_id]);
        $players = $stmtPlayers->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'is_started' => $is_started,
            'is_aborted' => $is_aborted,
            'players' => $players
        ]);
    } catch (PDOException $e) {
        echo json_encode(['is_started' => 0, 'players' => []]);
    }
} else {
    echo json_encode(['is_started' => 0, 'players' => []]);
}