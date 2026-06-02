<?php
require_once 'init.php';
require_once 'db.php';

// WICHTIG: Prüfen, ob der User wirklich Host der Lobby ist
$lobby_id = $_SESSION['quiz_setup']['lobby_id'] ?? null;

if (!$lobby_id) {
    exit('Keine Berechtigung');
}

// Wir holen uns den Host aus der DB, um sicher zu gehen
$stmt = $pdo->prepare("SELECT host_name FROM quiz_lobbies WHERE id = ?");
$stmt->execute([$lobby_id]);
$host = $stmt->fetchColumn();

// Vergleich: Ist der aktuell eingeloggte User der Host der Lobby?
// Falls der Host "Gast" ist, müssen wir hier ggf. die Session-ID statt des Namens vergleichen
if ($_SESSION['username'] !== $host) {
    exit('Nur der Host darf Spieler kicken!');
}

$player_to_kick = $_POST['username'] ?? '';
if (!empty($player_to_kick)) {
    $stmt = $pdo->prepare("DELETE FROM lobby_players WHERE lobby_id = ? AND player_name = ?");
    $stmt->execute([$lobby_id, $player_to_kick]);
    echo json_encode(['success' => true]);
}