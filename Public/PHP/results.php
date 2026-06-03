<?php
require_once 'init.php';
require_once 'db.php';
require_once 'game_results.php';

/** @var string $username */

$lobby_id = $_SESSION['quiz_setup']['lobby_id'] ?? $_SESSION['player_lobby_id'] ?? null;

if (!$lobby_id) {
    header("Location: dashboard.php");
    exit;
}

// Eigenen Namen so ermitteln, wie er in lobby_players steht (player_name), damit sich der
// Spieler im finalen Ranking wiederfindet (Rang, Punkte und "(Du)"-Markierung). Bei Gästen
// ist $_SESSION['username'] nur "Gast", der echte Name steht in player_name.
$currentDisplayName = $_SESSION['player_name'] ?? $_SESSION['username'] ?? $username ?? 'Gast';

$rankingPlayers = [];
$totalPlayers = 0;
$totalQuestions = 0;
$currentPlayerRank = null;
$currentPlayerScore = 0;
$winnerName = '';
$winnerScore = 0;
$winnerAvatar = '';
$winners = [];
$quizFinished = true;

try {
    $stmtLobby = $pdo->prepare("
        SELECT current_question_index, quiz_data
        FROM quiz_lobbies
        WHERE id = ?
    ");
    $stmtLobby->execute([$lobby_id]);
    $lobby = $stmtLobby->fetch(PDO::FETCH_ASSOC);

    if (!$lobby) {
        header("Location: dashboard.php");
        exit;
    }

    if (!empty($lobby['quiz_data'])) {
        $quizData = json_decode($lobby['quiz_data'], true);

        if (is_array($quizData)) {
            $totalQuestions = count($quizData);
        }
    }

    if ($totalQuestions === 0 && isset($_SESSION['quiz_questions']) && is_array($_SESSION['quiz_questions'])) {
        $totalQuestions = count($_SESSION['quiz_questions']);
    }

    if ($totalQuestions > 0 && (int)$lobby['current_question_index'] < $totalQuestions) {
        $quizFinished = false;
    }

    $stmtRank = $pdo->prepare("
        SELECT player_name AS username, points AS score, avatar
        FROM lobby_players
        WHERE lobby_id = ?
        ORDER BY points DESC, player_name ASC
    ");
    $stmtRank->execute([$lobby_id]);
    $rankingPlayers = $stmtRank->fetchAll(PDO::FETCH_ASSOC);

    $totalPlayers = count($rankingPlayers);

    // Standard-Competition-Ranking (LH 20): gleiche Punktzahl = gleicher Platz,
    // danach wird der Platz entsprechend übersprungen (z. B. 1,1,3,4).
    $rankCounter = 0;
    $prevScore   = null;
    foreach ($rankingPlayers as $index => $player) {
        $playerName = $player['username'] ?? '';
        $playerScore = (int)($player['score'] ?? 0);

        if ($prevScore === null || $playerScore < $prevScore) {
            $rankCounter = $index + 1;
        }
        $prevScore = $playerScore;
        $rankingPlayers[$index]['rank'] = $rankCounter;

        if ($index === 0) {
            $winnerName = $playerName;
            $winnerScore = $playerScore;
            $winnerAvatar = $player['avatar'] ?? '';
        }

        if ($playerName === $currentDisplayName) {
            $currentPlayerRank = $rankCounter;
            $currentPlayerScore = $playerScore;
        }
    }

    // Alle Spieler mit der höchsten Punktzahl sind Gewinner (Punktgleichstand an der Spitze).
    // Liste ist nach Punkten absteigend sortiert, also sammeln wir die führenden gleichen Werte.
    if (!empty($rankingPlayers)) {
        $topScore = (int)($rankingPlayers[0]['score'] ?? 0);
        foreach ($rankingPlayers as $p) {
            if ((int)($p['score'] ?? 0) === $topScore) {
                $winners[] = ['name' => $p['username'] ?? '', 'avatar' => $p['avatar'] ?? ''];
            } else {
                break;
            }
        }
    }
} catch (PDOException $e) {
    $rankingPlayers = [];
    $totalPlayers = 0;
}

// Beendetes Spiel: Ergebnisse dauerhaft sichern (idempotent, LH 21.1).
if ($quizFinished && !empty($rankingPlayers)) {
    save_game_results($pdo, $lobby_id);
}

// Kurzes Kürzel, um Text sicher auszugeben (HTML-Sonderzeichen werden escaped).
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz-Ergebnis | damago Quizsystem</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body class="quiz-play-page">

<?php include_once 'topbar.php'; ?>

<main class="play-layout">
    <section class="quiz-main">
        <div class="quiz-topline">
            <div>
                <span class="eyebrow">Quiz beendet</span>
            </div>
        </div>

        <section class="question-card">
            <?php if (!$quizFinished): ?>
                <h2>Das Quiz ist noch nicht vollständig beendet.</h2>
            <?php elseif (!empty($winners)): ?>
                <?php if (count($winners) === 1): ?>
                    <h2 class="winner-heading">
                        <span>Gewinner:</span>
                        <?php if (!empty($winners[0]['avatar'])): ?>
                            <img src="../Uploads/Avatare/<?php echo rawurlencode($winners[0]['avatar']); ?>" alt="" class="winner-avatar">
                        <?php endif; ?>
                        <span><?php echo e($winners[0]['name']); ?></span>
                    </h2>
                <?php else: ?>
                    <h2 class="winner-heading winner-heading-multi"><?php echo count($winners); ?> Gewinner (Gleichstand):</h2>
                    <ul class="winner-list">
                        <?php foreach ($winners as $w): ?>
                            <li class="winner-entry">
                                <?php if (!empty($w['avatar'])): ?>
                                    <img src="../Uploads/Avatare/<?php echo rawurlencode($w['avatar']); ?>" alt="" class="winner-avatar">
                                <?php endif; ?>
                                <span><?php echo e($w['name']); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            <?php else: ?>
                <h2>Keine Ergebnisse gefunden.</h2>
            <?php endif; ?>
        </section>

        <section class="statistics-card">
            <div class="stat-row">
                <span class="stat-title">Dein Ergebnis</span>
                <div class="stat-values">
                    <div class="stat-metric">
                        <span class="stat-label">Platz</span>
                        <span class="stat-num"><?php echo $currentPlayerRank !== null ? (int)$currentPlayerRank : '-'; ?></span>
                    </div>
                    <div class="stat-metric">
                        <span class="stat-label">Punkte</span>
                        <span class="stat-num"><?php echo (int)$currentPlayerScore; ?></span>
                    </div>
                </div>
            </div>

            <div class="stat-row">
                <span class="stat-title">Quiz-Übersicht</span>
                <div class="stat-values">
                    <div class="stat-metric">
                        <span class="stat-label">Teilnehmer</span>
                        <span class="stat-num"><?php echo (int)$totalPlayers; ?></span>
                    </div>
                    <div class="stat-metric">
                        <span class="stat-label">Fragen</span>
                        <span class="stat-num"><?php echo (int)$totalQuestions; ?></span>
                    </div>
                    <div class="stat-metric">
                        <span class="stat-label">Gewinnerpunkte</span>
                        <span class="stat-num"><?php echo (int)$winnerScore; ?></span>
                    </div>
                </div>
            </div>
        </section>

        <div class="confirm-button-wrapper">
            <a href="dashboard.php" class="back-button">Zurück zum Dashboard</a>
        </div>
    </section>

    <aside class="ranking-panel">
        <div class="ranking-header">
            <span class="eyebrow">Finales Ranking</span>
            <h2>Endstand</h2>
        </div>

        <div class="ranking-list">
            <table class="ranking-table">
                <thead>
                    <tr>
                        <th>Pl.</th>
                        <th>Name</th>
                        <th>Punkte</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rankingPlayers)): ?>
                        <tr>
                            <td colspan="3">Keine Spielergebnisse vorhanden.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rankingPlayers as $index => $player): ?>
                            <?php
                                $playerName = $player['username'] ?? 'Unbekannt';
                                $playerScore = (int)($player['score'] ?? 0);
                                $isCurrentPlayer = $playerName === $currentDisplayName;
                            ?>
                            <tr<?php echo $isCurrentPlayer ? ' class="current-player"' : ''; ?>>
                                <td><?php echo (int)($player['rank'] ?? ($index + 1)); ?>.</td>
                                <td>
                                    <span class="ranking-name-cell">
                                        <?php if (!empty($player['avatar'])): ?>
                                            <img src="../Uploads/Avatare/<?php echo rawurlencode($player['avatar']); ?>" alt="" class="ranking-avatar">
                                        <?php endif; ?>
                                        <?php echo e($playerName); ?>
                                        <?php if ($isCurrentPlayer): ?>
                                            <span class="you-badge">(Du)</span>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td class="score-cell"><?php echo $playerScore; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </aside>
</main>

    <?php include_once 'footbar.php'; ?>

</body>
</html>