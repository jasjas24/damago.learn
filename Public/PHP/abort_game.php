<?php
/**
 * Host bricht ein Spiel ab (LH 9.5), funktioniert aus der Lobby und im laufenden Spiel.
 * Autorisierung ausschließlich über den geheimen Host-Token.
 * Setzt quiz_lobbies.is_aborted = 1; die Teilnehmer werden über ihr Polling hinausgeleitet.
 */

require_once 'init.php';
require_once 'db.php';
require_once 'host_auth.php';

$host_token = $_POST['host_token'] ?? $_SESSION['quiz_setup']['host_token'] ?? null;
$lobby_id   = $_SESSION['quiz_setup']['lobby_id'] ?? null;

// Falls die Host-Session fehlt, per Token wiederherstellen.
if (!$lobby_id && $host_token) {
    $lobbyByToken = find_lobby_by_host_token($pdo, $host_token);
    if ($lobbyByToken) {
        $lobby_id = (int)$lobbyByToken['id'];
    }
}

if (!verify_host_token($pdo, $lobby_id, $host_token)) {
    header("Location: dashboard.php");
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE quiz_lobbies SET is_aborted = 1 WHERE id = ?");
    $stmt->execute([$lobby_id]);
} catch (PDOException $e) {
    // Abbruch darf nicht hängen bleiben; im Fehlerfall trotzdem zum Dashboard.
}

// Quiz-bezogene Session-Daten des Hosts aufräumen (er bleibt eingeloggt).
unset(
    $_SESSION['quiz_setup'],
    $_SESSION['quiz_questions'],
    $_SESSION['current_question_index'],
    $_SESSION['quiz_score'],
    $_SESSION['show_explanation'],
    $_SESSION['waiting_for_reveal'],
    $_SESSION['last_result']
);

header("Location: dashboard.php");
exit;
