<?php
require_once 'init.php';
require_once 'db.php';

header('Content-Type: application/json');

$lobby_id = $_SESSION['quiz_setup']['lobby_id'] ?? $_SESSION['player_lobby_id'] ?? null;
$username = $_SESSION['player_name'] ?? $_SESSION['username'] ?? 'Gast';
$current_local_index = $_SESSION['current_question_index'] ?? 0;
$local_show_explanation = $_SESSION['show_explanation'] ?? false;

// Ist der aktuelle Benutzer der moderierende Host (spielt selbst nicht mit)?
// Ein solcher Host steht NICHT in lobby_players und darf daher nicht über den
// Kick-Check (der auf lobby_players prüft) aus dem Spiel geworfen werden.
$isHost            = isset($_SESSION['quiz_setup']['lobby_id']);
$hostPlays         = $_SESSION['quiz_setup']['host_plays'] ?? 'yes';
$isModeratingHost  = $isHost && $hostPlays === 'no';

if (!$lobby_id) {
    echo json_encode(['success' => false, 'error' => 'Keine Lobby gefunden']);
    exit;
}

else if (!$isModeratingHost) {
    // Nur echte Spieler (inkl. mitspielender Host) prüfen, ob sie noch in der Tabelle stehen.
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM lobby_players WHERE lobby_id = ? AND player_name = ?");
    $stmt->execute([$lobby_id, $username]);
    $exists = $stmt->fetchColumn();

    if ($exists == 0) {
        // Spieler wurde gekickt!
        echo json_encode(['success' => true, 'action' => 'kicked']);
        exit;
    }
}

try {
    $stmt = $pdo->prepare("SELECT current_question_index, question_count, show_explanation, is_aborted, time_limit, current_question_started_at, TIMESTAMPDIFF(SECOND, current_question_started_at, NOW()) AS elapsed FROM quiz_lobbies WHERE id = ?");
    $stmt->execute([$lobby_id]);
    $lobby = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($lobby) {
        // Host hat das Spiel abgebrochen, also die Teilnehmer hinausleiten.
        if ((int)$lobby['is_aborted'] === 1) {
            echo json_encode(['success' => true, 'aborted' => true]);
            exit;
        }

        $dbIndex = (int)$lobby['current_question_index'];
        $maxQuestions = (int)$lobby['question_count'];
        $dbShowExplanation = (int)$lobby['show_explanation'] === 1;

        // SERVER-WATCHDOG (LH 13.3 / 13.4): Ist die Zeit der aktuellen Frage abgelaufen,
        // wird die Auflösung serverseitig erzwungen, auch wenn ein Spieler den Tab geschlossen
        // hat und nichts mehr absendet. So kann das Spiel nicht hängen bleiben.
        $grace = 2;
        $remainingTime = null;
        if ($lobby['current_question_started_at'] !== null) {
            $timeLimit = (int)$lobby['time_limit'];
            $elapsed = (int)$lobby['elapsed'];
            $remainingTime = max(0, $timeLimit - $elapsed);
            if (!$dbShowExplanation && $elapsed > ($timeLimit + $grace)) {
                $pdo->prepare("UPDATE quiz_lobbies SET show_explanation = 1 WHERE id = ?")->execute([$lobby_id]);
                $dbShowExplanation = true;
            }
        }

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

        // Fall 3: Alle haben geantwortet, jetzt die Auflösung einblenden
        if ($dbShowExplanation && !$local_show_explanation) {
            $_SESSION['show_explanation'] = true;
            unset($_SESSION['waiting_for_reveal']);
            echo json_encode(['success' => true, 'action' => 'reload']);
            exit;
        }

        // Antwort-Zähler der aktuellen Frage (Host-/Beameransicht, LH 9.5)
        $answered = 0;
        $stmtTot = $pdo->prepare("SELECT COUNT(*) FROM lobby_players WHERE lobby_id = ?");
        $stmtTot->execute([$lobby_id]);
        $total = (int)$stmtTot->fetchColumn();

        $stmtQid = $pdo->prepare("SELECT question_id FROM lobby_questions WHERE lobby_id = ? AND sort_order = ?");
        $stmtQid->execute([$lobby_id, $dbIndex]);
        $qid = $stmtQid->fetchColumn();
        if ($qid) {
            $stmtAns = $pdo->prepare("SELECT COUNT(*) FROM player_answers WHERE lobby_id = ? AND question_id = ?");
            $stmtAns->execute([$lobby_id, $qid]);
            $answered = (int)$stmtAns->fetchColumn();
        }

        // Standard-Rückgabe
        echo json_encode([
            'success' => true,
            'action' => 'wait',
            'current_index' => $dbIndex,
            'show_explanation' => $dbShowExplanation,
            'remaining_time' => $remainingTime,
            'answered' => $answered,
            'total' => $total
        ]);
        exit;
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}

echo json_encode(['success' => false]);