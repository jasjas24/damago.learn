<?php
require_once 'init.php';
require_once 'db.php';

/** @var string $username */

$lobby_id = $_SESSION['quiz_setup']['lobby_id'] ?? $_SESSION['player_lobby_id'] ?? null;

if (!$lobby_id) {
    header("Location: dashboard.php");
    exit;
}

$currentDisplayName = $_SESSION['username'] ?? $username ?? 'Gast';

$rankingPlayers = [];
$totalPlayers = 0;
$totalQuestions = 0;
$currentPlayerRank = null;
$currentPlayerScore = 0;
$winnerName = '';
$winnerScore = 0;
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
        SELECT player_name AS username, points AS score
        FROM lobby_players
        WHERE lobby_id = ?
        ORDER BY points DESC, player_name ASC
    ");
    $stmtRank->execute([$lobby_id]);
    $rankingPlayers = $stmtRank->fetchAll(PDO::FETCH_ASSOC);

    $totalPlayers = count($rankingPlayers);

    foreach ($rankingPlayers as $index => $player) {
        $playerName = $player['username'] ?? '';
        $playerScore = (int)($player['score'] ?? 0);

        if ($index === 0) {
            $winnerName = $playerName;
            $winnerScore = $playerScore;
        }

        if ($playerName === $currentDisplayName) {
            $currentPlayerRank = $index + 1;
            $currentPlayerScore = $playerScore;
        }
    }
} catch (PDOException $e) {
    $rankingPlayers = [];
    $totalPlayers = 0;
}

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
            <?php elseif (!empty($winnerName)): ?>
                <h2>Gewinner: <?php echo e($winnerName); ?></h2>
            <?php else: ?>
                <h2>Keine Ergebnisse gefunden.</h2>
            <?php endif; ?>
        </section>

        <section class="statistics-card">
            <div class="stat-row">
                <span class="stat-title">Dein Ergebnis</span>
                <div class="stat-values">
                    <span>
                        Platz:
                        <?php echo $currentPlayerRank !== null ? (int)$currentPlayerRank : '-'; ?>
                    </span>
                    <span>
                        Punkte:
                        <?php echo (int)$currentPlayerScore; ?>
                    </span>
                </div>
            </div>

            <div class="stat-row">
                <span class="stat-title">Quiz-Übersicht</span>
                <div class="stat-values">
                    <span>
                        Teilnehmer:
                        <?php echo (int)$totalPlayers; ?>
                    </span>
                    <span>
                        Fragen:
                        <?php echo (int)$totalQuestions; ?>
                    </span>
                    <span>
                        Gewinnerpunkte:
                        <?php echo (int)$winnerScore; ?>
                    </span>
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
                                <td><?php echo $index + 1; ?>.</td>
                                <td>
                                    <?php echo e($playerName); ?>
                                    <?php if ($isCurrentPlayer): ?>
                                        <span class="you-badge">(Du)</span>
                                    <?php endif; ?>
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

</body>
</html>