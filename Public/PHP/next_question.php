<?php
require_once 'init.php';
require_once 'db.php';

/** @var string $username */
// SICHERHEITSMASSNAHME: Username flexibel aus der Session ziehen, falls init.php ihn nicht setzt
$username = $_SESSION['player_name'] ?? $username ?? $_SESSION['username'] ?? 'Gast';

$lobby_id = $_SESSION['quiz_setup']['lobby_id'] ?? $_SESSION['player_lobby_id'] ?? null;
$current_index = $_SESSION['current_question_index'] ?? 0;
$questions = $_SESSION['quiz_questions'] ?? [];
$current_question = $questions[$current_index] ?? null;

if ($lobby_id && $current_question) {
    // Flag: Spieler hat abgegeben und wartet auf die anderen
    $_SESSION['waiting_for_reveal'] = true; 

    try {
        // 1. Die vom Spieler ausgewählten IDs direkt aus dem nativen Checkbox-Array holen (als Integers)
        $chosenIds = isset($_POST['selected_answers']) ? array_map('intval', $_POST['selected_answers']) : [];

        // 1b. SERVER ist maßgebliche Zeitquelle (LH 13.4 / 26.3):
        //     Verstrichene Zeit serverseitig aus dem Fragestart-Zeitstempel berechnen.
        //     Eine verspätete Abgabe (über time_limit + Toleranz) wird wie ein Timeout gewertet.
        $grace = 2; // Sekunden Toleranz gegen Netzwerk-/Anzeigeverzögerung
        $isTimeout = false;
        $remainingTime = null;
        $serverTimeLimit = 0;
        $stmtTime = $pdo->prepare("SELECT time_limit, current_question_started_at, TIMESTAMPDIFF(SECOND, current_question_started_at, NOW()) AS elapsed FROM quiz_lobbies WHERE id = ?");
        $stmtTime->execute([$lobby_id]);
        $timeRow = $stmtTime->fetch(PDO::FETCH_ASSOC);
        if ($timeRow) {
            $serverTimeLimit = (int)$timeRow['time_limit'];
            if ($timeRow['current_question_started_at'] !== null) {
                $elapsed = (int)$timeRow['elapsed'];
                $remainingTime = max(0, $serverTimeLimit - $elapsed);
                $isTimeout = $elapsed > ($serverTimeLimit + $grace);
            }
        }

        // 2. Die wirklich korrekten IDs aus der Session/Frage sammeln
        $correctIds = [];
        if (isset($current_question['answers'])) {
            foreach ($current_question['answers'] as $ans) {
                if (intval($ans['is_correct']) === 1) {
                    $correctIds[] = intval($ans['id']);
                }
            }
        }

        // 3. PUNKTEBERECHNUNG je nach Modus
        $pointMode = $_SESSION['quiz_setup']['point_mode'] ?? null;

        // Für Mitspieler den Punktemodus live aus der DB holen, da sie keine Host-Session haben
        if (empty($pointMode) && $lobby_id) {
            $stmtMode = $pdo->prepare("SELECT point_mode FROM quiz_lobbies WHERE id = ?");
            $stmtMode->execute([$lobby_id]);
            $dbMode = $stmtMode->fetchColumn();
            if ($dbMode) {
                $pointMode = $dbMode;
                $_SESSION['quiz_setup']['point_mode'] = $dbMode; // In Session merken
            }
        }
        if (empty($pointMode)) {
            $pointMode = 'all_or_nothing';
        }

        // 3a. Korrektheit modusunabhängig bestimmen: vollständig richtig / teilweise / falsch.
        $chosenCorrect = 0;
        $chosenWrong   = 0;
        foreach ($chosenIds as $id) {
            if (in_array($id, $correctIds)) { $chosenCorrect++; }
            else { $chosenWrong++; }
        }
        $totalCorrect = count($correctIds);

        $answerCategory = 'wrong';
        if (!$isTimeout && $totalCorrect > 0 && $chosenWrong === 0 && $chosenCorrect > 0) {
            // keine falsche gewählt, mindestens eine richtige (bei genau 1 richtigen = vollständig)
            $answerCategory = ($chosenCorrect === $totalCorrect) ? 'correct' : 'partial';
        }

        // 3b. Punkte je Modus (max. 1000, nie negativ, auf ganze Zahlen gerundet).
        $pointsEarned = 0;
        if ($answerCategory === 'correct') {
            if ($pointMode === 'time_bonus') {
                // Zeitbonus: 500 Grundpunkte + bis zu 500 aus der serverseitigen Restzeit (LH 18.1).
                $ratio = ($remainingTime !== null && $serverTimeLimit > 0) ? ($remainingTime / $serverTimeLimit) : 0;
                $pointsEarned = (int) round(500 + 500 * $ratio);
            } else {
                $pointsEarned = 1000;
            }
        } elseif ($answerCategory === 'partial') {
            if ($pointMode === 'all_or_nothing') {
                $pointsEarned = 0;            // "Ganz oder gar nicht": nur exakt richtig zählt
            } elseif ($pointMode === 'time_bonus') {
                // Teilrichtig im Zeitbonus gibt die halben Zeitpunkte (LH 18.2).
                $ratio = ($remainingTime !== null && $serverTimeLimit > 0) ? ($remainingTime / $serverTimeLimit) : 0;
                $pointsEarned = (int) round((500 + 500 * $ratio) / 2);
            } else {
                $pointsEarned = 500;          // Teilpunkte
            }
        }

        // 4. In DB eintragen, dass dieser Spieler geantwortet hat
        //    (inkl. Korrektheit und erreichter Punkte, Grundlage für die Archiv-Statistik)
        //    is_correct = 1 nur bei VOLLSTÄNDIG richtiger Antwort (modusunabhängig, LH 19).
        $isCorrect = ($answerCategory === 'correct') ? 1 : 0;
        $stmt = $pdo->prepare("INSERT IGNORE INTO player_answers (lobby_id, question_id, player_name, is_correct, points_earned) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$lobby_id, $current_question['id'], $username, $isCorrect, $pointsEarned]);

        // 5. Die erreichten Punkte direkt beim Spieler in der Lobby-Tabelle aufaddieren
        $stmtScore = $pdo->prepare("UPDATE lobby_players SET points = points + ? WHERE lobby_id = ? AND player_name = ?");
        $stmtScore->execute([$pointsEarned, $lobby_id, $username]);

        // 6. Punkte auch in der eigenen Session für die spätere results.php hochzählen
        if (!isset($_SESSION['quiz_score'])) {
            $_SESSION['quiz_score'] = 0;
        }
        $_SESSION['quiz_score'] += $pointsEarned;

        // 7. Status für die Einblendung ermitteln (timeout, correct, partial oder wrong).
        //    An die Korrektheit gekoppelt (nicht an feste Punktwerte), damit auch der
        //    Zeitbonus mit variablen Punkten korrekt als richtig/teilrichtig erscheint.
        $resultStatus = 'wrong';
        if ($isTimeout) {
            $resultStatus = 'timeout';
        } elseif ($answerCategory === 'correct' && $pointsEarned > 0) {
            $resultStatus = 'correct';
        } elseif ($answerCategory === 'partial' && $pointsEarned > 0) {
            $resultStatus = 'partial';
        }

        // Letztes Ergebnis für die Einblendung in der game.php zwischenspeichern
        $_SESSION['last_result'] = [
            'status'        => $resultStatus,
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

        // Wenn alle fertig sind oder die Zeit abgelaufen ist, die Auflösung freischalten
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