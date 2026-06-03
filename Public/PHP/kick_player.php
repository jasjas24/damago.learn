<?php
require_once 'init.php';
require_once 'db.php';
require_once 'host_auth.php';

// Host-Berechtigung über den geheimen Host-Token prüfen (LH 27.5),
// nicht über den Anzeigenamen (bei Gast-Hosts unzuverlässig).
$host_token = $_POST['host_token'] ?? $_SESSION['quiz_setup']['host_token'] ?? null;
$lobby_id   = $_SESSION['quiz_setup']['lobby_id'] ?? null;

// Falls die Host-Session fehlt, per Token wiederherstellen.
if (!$lobby_id && $host_token) {
    $lobbyByToken = find_lobby_by_host_token($pdo, $host_token);
    if ($lobbyByToken) {
        restore_host_session($lobbyByToken, $host_token);
        $lobby_id = (int)$lobbyByToken['id'];
    }
}

if (!verify_host_token($pdo, $lobby_id, $host_token)) {
    http_response_code(403);
    exit('Nur der Host darf Spieler entfernen!');
}

// Den vom Host gewählten Spieler aus der Lobby werfen
$player_to_kick = $_POST['username'] ?? '';
if (!empty($player_to_kick)) {
    $stmt = $pdo->prepare("DELETE FROM lobby_players WHERE lobby_id = ? AND player_name = ?");
    $stmt->execute([$lobby_id, $player_to_kick]);
    echo json_encode(['success' => true]);
}