<?php
/**
 * Account-basierte Rückkehr eines registrierten Hosts in sein laufendes Spiel (LH 9.4).
 *
 * Anwendungsfall: Browser abgestürzt oder neu angemeldet, der Host hat den Host-Link nicht
 * mehr. Da das Spiel über `quiz_lobbies.host_user_id` mit seinem Konto verknüpft ist, kann
 * er es nach dem Login über das Dashboard fortsetzen.
 *
 * Autorisierung erfolgt über die Konto-Eigentümerschaft (eingeloggter User == host_user_id).
 * Dabei wird ein frischer Host-Token erzeugt und die Host-Session wiederhergestellt.
 */

require_once 'init.php';
require_once 'db.php';
require_once 'host_auth.php';

$userId  = $_SESSION['user_id'] ?? null;
$lobbyId = (int)($_GET['lobby_id'] ?? 0);

if (!$userId || $lobbyId <= 0) {
    header('Location: dashboard.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM quiz_lobbies WHERE id = ?");
$stmt->execute([$lobbyId]);
$lobby = $stmt->fetch(PDO::FETCH_ASSOC);

// Nur der im Konto eingetragene Host darf zurückkehren.
if (!$lobby || (int)$lobby['host_user_id'] !== (int)$userId) {
    $_SESSION['dash_message'] = 'Dieses Spiel existiert nicht mehr.';
    header('Location: dashboard.php');
    exit;
}

// Abgebrochene oder bereits vollständig durchgespielte Spiele lassen sich nicht fortsetzen.
if ((int)$lobby['is_aborted'] === 1
    || (int)$lobby['current_question_index'] >= (int)$lobby['question_count']) {
    $_SESSION['dash_message'] = 'Dieses Spiel existiert nicht mehr.';
    header('Location: dashboard.php');
    exit;
}

// Frischen Host-Token erzeugen, Hash speichern und Host-Session wiederherstellen.
$newToken = generate_host_token();
$upd = $pdo->prepare("UPDATE quiz_lobbies SET host_token_hash = ? WHERE id = ?");
$upd->execute([host_token_hash($newToken), $lobbyId]);
$lobby['host_token_hash'] = host_token_hash($newToken);

restore_host_session($lobby, $newToken);

// Läuft das Spiel bereits, direkt in die Spielansicht, sonst zurück in die Lobby.
if ((int)$lobby['is_started'] === 1) {
    header('Location: game.php');
} else {
    header('Location: host_lobby.php?code=' . urlencode($lobby['join_code']) . '&host_token=' . urlencode($newToken));
}
exit;
