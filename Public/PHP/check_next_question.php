<?php
require_once 'init.php';
require_once 'db.php';

header('Content-Type: application/json');

$lobby_id = $_SESSION['quiz_setup']['lobby_id'] ?? $_SESSION['player_lobby_id'] ?? null;
$username = $_SESSION['player_name'] ?? $_SESSION['username'] ?? 'Gast';
$current_local_index = $_SESSION['current_question_index'] ?? 0;
$local_show_explanation = $_SESSION['show_explanation'] ?? false;

if (!$lobby_id) {
    echo json_encode(['success' => false, 'error' => 'Keine Lobby gefunden']);
    exit;
}

// NEU: Zuerst prüfen, ob der Spieler gekickt wurde
try {
    $stmtKick = $pdo->prepare("SELECT COUNT(*) FROM lobby_players WHERE lobby_id = ? AND player_name = ?");
    $stmtKick->execute([$lobby_id, $username]);
    if ($stmtKick->fetchColumn() == 0) {
        // Spieler ist nicht mehr in der Liste -> KICK!
        echo json_encode(['success' => true, 'action' => 'kicked']);
        exit;
    }
} catch (PDOException $e) {
    // Falls DB-Fehler, einfach weitermachen
}

try {
    $stmt = $pdo->prepare("SELECT current_question_index, question_count, show_explanation FROM quiz_lobbies WHERE id = ?");
    $stmt->execute([$lobby_id]);
    $lobby = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($lobby) {
        $dbIndex = (int)$lobby['current_question_index'];
        $maxQuestions = (int)$lobby['question_count'];
        $dbShowExplanation = (int)$lobby['show_explanation'] === 1;

        // Fall 1: Host hat das Spiel komplett beendet
        if ($dbIndex >= $maxQuestions) {
            echo json_encode(['success' => true, 'redirect_to_results' => true]);
            exit;
        }

        // Fall 2: Host hat zur NÄCHSTEN FRAGE weitergeschaltet
        if ($dbIndex > $current_local_index) {
            $_SESSION['current_question_index'] = $dbIndex;
            $_SESSION['show_explanation'] = false;
            $_SESSION['last_result'] = null;
            unset($_SESSION['waiting_for_reveal']); 
            echo json_encode(['success' => true, 'action' => 'reload']);
            exit;
        }

        // Fall 3: Alle haben geantwortet -> AUFLÖSUNG einblenden
        if ($dbShowExplanation && !$local_show_explanation) {
            $_SESSION['show_explanation'] = true;
            unset($_SESSION['waiting_for_reveal']);
            echo json_encode(['success' => true, 'action' => 'reload']);
            exit;
        }

        // Standard-Rückgabe
        echo json_encode([
            'success' => true,
            'action' => 'wait',
            'current_index' => $dbIndex,
            'show_explanation' => $dbShowExplanation
        ]);
        exit;
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}

echo json_encode(['success' => false]);