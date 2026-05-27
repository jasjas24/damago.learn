<?php
require_once 'init.php';
require_once 'db.php';
header('Content-Type: application/json');

$lobby_id = $_SESSION['quiz_setup']['lobby_id'] ?? $_SESSION['player_lobby_id'] ?? null;
$current_local_index = $_SESSION['current_question_index'] ?? 0;
$local_show_explanation = $_SESSION['show_explanation'] ?? false;

if (!$lobby_id) {
    echo json_encode(['success' => false, 'error' => 'Keine Lobby gefunden']);
    exit;
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
            unset($_SESSION['waiting_for_reveal']); // Wartezustand löschen
            echo json_encode(['success' => true, 'action' => 'reload']);
            exit;
        }

        // Fall 3: Alle haben geantwortet -> AUFLÖSUNG einblenden (ABER NUR, WENN LOKAL NOCH NICHT AKTIV!)
        if ($dbShowExplanation && !$local_show_explanation) {
            $_SESSION['show_explanation'] = true;
            unset($_SESSION['waiting_for_reveal']); // Zwingend hier löschen!
            
            echo json_encode(['success' => true, 'action' => 'reload']);
            exit;
        }

        // Standard-Rückgabe, wenn sich am Zustand nichts geändert hat (Verhindert das Dauer-Reloading)
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