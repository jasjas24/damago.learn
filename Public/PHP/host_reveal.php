<?php
require_once 'init.php';
require_once 'db.php';
require_once 'host_auth.php';

// Erlaubt dem moderierenden Host, die Auflösung der aktuellen Frage JEDERZEIT manuell
// freizuschalten (LH 9.4/9.5), ohne auf alle Spieler oder den Timer-Watchdog zu warten.
// So kann der Host das Spiel immer aktiv leiten und bleibt nie hängen.

$host_token = $_POST['host_token'] ?? $_SESSION['quiz_setup']['host_token'] ?? null;
$lobby_id   = $_SESSION['quiz_setup']['lobby_id'] ?? null;

// Fehlt die Host-Session (z. B. Seite war zu), per Token wiederherstellen.
if (!$lobby_id && $host_token) {
    $lobbyByToken = find_lobby_by_host_token($pdo, $host_token);
    if ($lobbyByToken) {
        restore_host_session($lobbyByToken, $host_token);
        $lobby_id = (int)$lobbyByToken['id'];
    }
}

// Ohne gültigen Token darf niemand die Auflösung erzwingen.
if (!verify_host_token($pdo, $lobby_id, $host_token)) {
    header("Location: dashboard.php");
    exit;
}

if ($lobby_id) {
    try {
        // Auflösung der aktuellen Frage freischalten. Alle Clients (Host + Spieler) erkennen
        // dies beim nächsten Polling über check_next_question.php und laden die Auflösung.
        $stmt = $pdo->prepare("UPDATE quiz_lobbies SET show_explanation = 1 WHERE id = ?");
        $stmt->execute([$lobby_id]);
        $_SESSION['show_explanation'] = true;
        unset($_SESSION['waiting_for_reveal']);
    } catch (PDOException $e) {
        // Schlägt das Speichern fehl, geht es trotzdem zurück in die Spielansicht.
    }
}

// Zurück in die Spielansicht.
header("Location: game.php");
exit;
