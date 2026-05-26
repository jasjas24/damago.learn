<?php
require_once 'init.php';
require_once 'db.php';

$lobby_id = $_SESSION['quiz_setup']['lobby_id'] ?? $_SESSION['player_lobby_id'] ?? null;
$username = $_SESSION['player_name'] ?? $_SESSION['username'] ?? 'Gast';
$current_index = $_SESSION['current_question_index'] ?? 0;
$questions = $_SESSION['quiz_questions'] ?? [];
$current_question = $questions[$current_index] ?? null;

if ($lobby_id && $current_question) {
    // Flag: Spieler hat abgegeben und wartet auf die anderen
    $_SESSION['waiting_for_reveal'] = true; 

    try {
        // 1. Die vom Spieler ausgewählten IDs direkt aus dem nativen Checkbox-Array holen (als Integers)
        $chosenIds = isset($_POST['selected_answers']) ? array_map('intval', $_POST['selected_answers']) : [];
        $isTimeout = isset($_POST['timeout']) && $_POST['timeout'] == '1';

        // 2. Die wirklich korrekten IDs aus der Session/Frage sammeln
        $correctIds = [];
        if (isset($current_question['answers'])) {
            foreach ($current_question['answers'] as $ans) {
                if (intval($ans['is_correct']) === 1) {
                    $correctIds[] = intval($ans['id']);
                }
            }
        }

        // 3. BOMBEN-SICHERE PUNKTEBERECHNUNG (Ganz oder gar nicht)
        $pointsEarned = 0;
        
        if (!$isTimeout && !empty($correctIds)) {
            // Sortieren, damit die Reihenfolge beim Klicken keine Rolle spielt
            sort($chosenIds);
            sort($correctIds);
            
            // Wenn die Arrays exakt übereinstimmen (gleiche IDs, gleiche Länge)
            if ($chosenIds === $correctIds) {
                $pointsEarned = 1000;
            }
        }

        // 4. In DB eintragen, dass dieser Spieler geantwortet hat
        $stmt = $pdo->prepare("INSERT IGNORE INTO player_answers (lobby_id, question_id, player_name) VALUES (?, ?, ?)");
        $stmt->execute([$lobby_id, $current_question['id'], $username]);

        // 5. Die erreichten Punkte direkt beim Spieler in der Lobby-Tabelle aufaddieren
        $stmtScore = $pdo->prepare("UPDATE lobby_players SET points = points + ? WHERE lobby_id = ? AND player_name = ?");
        $stmtScore->execute([$pointsEarned, $lobby_id, $username]);

        // 6. Punkte auch in der eigenen Session für die spätere results.php hochzählen
        if (!isset($_SESSION['quiz_score'])) {
            $_SESSION['quiz_score'] = 0;
        }
        $_SESSION['quiz_score'] += $pointsEarned;

        // 7. Letztes Ergebnis für die Einblendung in der game.php zwischenspeichern
        $_SESSION['last_result'] = [
            'status'        => $isTimeout ? 'timeout' : (($pointsEarned === 1000) ? 'correct' : 'wrong'),
            'points_earned' => $pointsEarned,
            'chosen_ids'    => $chosenIds
        ];

        // 8. Prüfen, ob alle abgegeben haben
        $stmtPlayers = $pdo->prepare("SELECT COUNT(*) FROM lobby_players WHERE lobby_id = ?");
        $stmtPlayers->execute([$lobby_id]);
        $totalPlayers = (int)$stmtPlayers->fetchColumn();

        $stmtAnswers = $pdo->prepare("SELECT COUNT(*) FROM player_answers WHERE lobby_id = ? AND question_id = ?");
        $stmtAnswers->execute([$lobby_id, $current_question['id']]);
        $answeredPlayers = (int)$stmtAnswers->fetchColumn();

        // Wenn alle fertig sind ODER die Zeit ablief -> Auflösung freischalten
        if ($answeredPlayers >= $totalPlayers || $isTimeout) {
            $stmtUpdate = $pdo->prepare("UPDATE quiz_lobbies SET show_explanation = 1 WHERE id = ?");
            $stmtUpdate->execute([$lobby_id]);
        }

    } catch (PDOException $e) {
        // Optionale Fehlerprotokollierung: error_log($e->getMessage());
    }
}

// Zurück zur game.php leiten
header("Location: game.php");
exit;