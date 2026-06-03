<?php
/**
 * Dauerhafter Ergebnis-Datensatz pro Spiel (LH 21.1).
 *
 * Schreibt für ein beendetes Spiel je Teilnehmer einen Datensatz in `game_results`:
 * Thema/Pool, Anzahl vollständig richtiger Fragen, Endpunktzahl, erreichter Platz,
 * Teilnehmerzahl und Datum/Uhrzeit.
 *
 * Idempotent: Dank UNIQUE(lobby_id, player_name) + INSERT IGNORE kann die Funktion
 * gefahrlos mehrfach (z. B. von jedem Aufruf der results.php) aufgerufen werden,
 * ohne Duplikate oder Race-Conditions.
 */

if (!function_exists('save_game_results')) {
    function save_game_results(PDO $pdo, $lobby_id): void
    {
        if (empty($lobby_id)) {
            return;
        }

        try {
            // Bereits gespeichert? Dann nichts tun.
            $check = $pdo->prepare("SELECT COUNT(*) FROM game_results WHERE lobby_id = ?");
            $check->execute([$lobby_id]);
            if ((int)$check->fetchColumn() > 0) {
                return;
            }

            // Lobby-Metadaten
            $stmtLobby = $pdo->prepare("SELECT question_pool, question_count, point_mode FROM quiz_lobbies WHERE id = ?");
            $stmtLobby->execute([$lobby_id]);
            $lobby = $stmtLobby->fetch(PDO::FETCH_ASSOC);
            if (!$lobby) {
                return;
            }

            // Teilnehmer nach Punkten absteigend (für Platzberechnung); user_id direkt aus
            // lobby_players (eindeutig pro Konto, kein unsicherer Namens-Lookup).
            $stmtPlayers = $pdo->prepare("SELECT player_name, points, user_id FROM lobby_players WHERE lobby_id = ? ORDER BY points DESC, player_name ASC");
            $stmtPlayers->execute([$lobby_id]);
            $players = $stmtPlayers->fetchAll(PDO::FETCH_ASSOC);
            if (!$players) {
                return;
            }

            $totalPlayers   = count($players);
            $totalQuestions = (int)$lobby['question_count'];

            $stmtCorrect = $pdo->prepare("SELECT COALESCE(SUM(is_correct), 0) FROM player_answers WHERE lobby_id = ? AND player_name = ?");
            $insert = $pdo->prepare("
                INSERT IGNORE INTO game_results
                    (lobby_id, user_id, player_name, question_pool, total_questions, correct_count, score, rank_position, total_players, score_mode, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");

            // Standard-Competition-Ranking (LH 20): gleiche Punkte = gleicher Platz.
            $rank = 0;
            $prevScore = null;
            foreach ($players as $i => $p) {
                $score = (int)$p['points'];
                if ($prevScore === null || $score < $prevScore) {
                    $rank = $i + 1;
                }
                $prevScore = $score;

                $stmtCorrect->execute([$lobby_id, $p['player_name']]);
                $correct = (int)$stmtCorrect->fetchColumn();

                // Registrierter Nutzer bekommt die echte user_id aus lobby_players, ein Gast bleibt NULL.
                $uid = isset($p['user_id']) && $p['user_id'] !== null ? (int)$p['user_id'] : null;

                $insert->execute([
                    $lobby_id,
                    $uid,
                    $p['player_name'],
                    $lobby['question_pool'],
                    $totalQuestions,
                    $correct,
                    $score,
                    $rank,
                    $totalPlayers,
                    $lobby['point_mode'],
                ]);
            }
        } catch (PDOException $e) {
            // Persistenz darf die Ergebnisanzeige nie blockieren.
        }
    }
}
