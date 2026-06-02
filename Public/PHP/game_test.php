<?php
require_once 'init.php';
require_once 'helpers.php';
/** @var string $username */
/** @var string $role */

$showExplanation = isset($_SESSION['show_explanation']) && $_SESSION['show_explanation'] === true;
$lastResult = $_SESSION['last_result'] ?? null;

if (!isset($_SESSION['quiz_questions']) || empty($_SESSION['quiz_questions'])) {
    header("Location: setup_lobby.php");
    exit;
}

if (!isset($_SESSION['current_question_index'])) {
    $_SESSION['current_question_index'] = 0;
}

$currentIndex = $_SESSION['current_question_index'];
$allQuestions = $_SESSION['quiz_questions'];
$totalQuestions = count($allQuestions);

if ($currentIndex >= $totalQuestions) {
    header("Location: results.php");
    exit;
}

$currentQuestion = $allQuestions[$currentIndex];
$answers = $currentQuestion['answers'];

$rankingPlayers = [
    [
        'username' => $_SESSION['username'] ?? 'Gast',
        'score'    => $_SESSION['quiz_score'] ?? 0
    ]
];

usort($rankingPlayers, function($a, $b) {
    return $b['score'] <=> $a['score'];
});

$timeLimit = $_SESSION['quiz_setup']['time_limit'];
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quiz spielen | damago Quizsystem</title>
<link rel="stylesheet" href="../CSS/style.css">
<link rel="stylesheet" href="../JS/vendor/highlight-theme.min.css">
</head>
<body class="quiz-play-page">

<?php include_once 'topbar.php'; ?>

<main class="play-layout">
    <section class="quiz-main">
        <div class="quiz-topline">
            <div>
                <span class="eyebrow">Frage <?php echo $currentIndex + 1; ?> von <?php echo $totalQuestions; ?></span>
                <h1>Quizrunde läuft</h1>
            </div>
            <div class="timer-box">
                <span>Zeit:</span>
                <strong><span id="timer-display"><?php echo $timeLimit; ?></span></strong>
            </div>
        </div>

        <section class="question-card">
            <div class="question-label">Aktuelle Frage</div>
            <div class="question-title"><?php echo render_rich_text($currentQuestion['question_text']); ?></div>
        </section>

        <form class="millionaire-answers" id="quiz-form" action="<?php echo $showExplanation ? 'go_next.php' : 'next_question.php'; ?>" method="POST">
            <?php foreach ($answers as $letter => $ans): ?>
                <?php $inlineStyle = ""; ?>
                <button type="<?php echo $showExplanation ? 'submit' : 'button'; ?>"
                        class="millionaire-answer <?php echo $showExplanation ? '' : ''; ?>"
                        data-id="<?php echo $ans['id']; ?>"
                        <?php echo $showExplanation ? 'disabled' : ''; ?>>
                    <span class="answer-text">
                        <?php echo render_inline_text($ans['text']); ?>
                    </span>
                    <?php if (!$showExplanation): ?>
                        <input type="checkbox" class="answer-checkbox" name="selected_answers[]" value="<?php echo $ans['id']; ?>" id="check-<?php echo $ans['id']; ?>">
                    <?php endif; ?>
                </button>
            <?php endforeach; ?>

            <div class="confirm-button-wrapper">
                <?php if (!$showExplanation): ?>
                    <button type="submit" id="confirm-btn" class="millionaire-answer confirm-button btn-blue">
                        <span class="answer-text">Antwort bestätigen</span>
                    </button>
                <?php endif; ?>
            </div>
        </form>

        <?php if ($showExplanation): ?>
            <div class="confirm-button-wrapper">
                <form action="go_next.php" method="POST" class="next-question-form">
                    <button type="submit" class="millionaire-answer confirm-button btn-green">
                        <span class="answer-text">Nächste Frage</span>
                    </button>
                </form>
            </div>

            <section class="question-card explanation-card">
                <div class="question-label">Auflösung & Erklärungen</div>
                <div>
                    <strong>Ergebnis:</strong>
                    <?php
                        if ($lastResult['status'] === 'correct') echo "<span class='correct-text'>Genial! Alle richtigen Antworten gefunden! (+".$lastResult['points_earned']." Punkte)</span>";
                        elseif ($lastResult['status'] === 'partial') echo "<span class='partial-text'>Teilweise richtig! (+".$lastResult['points_earned']." Punkte)</span>";
                        elseif ($lastResult['status'] === 'timeout') echo "<span class='timeout-text'>Zeit abgelaufen! (0 Punkte)</span>";
                        else echo "<span class='wrong-text'>Leider falsch! (0 Punkte)</span>";
                    ?>
                </div>
                <ul class="explanation-list">
                    <?php foreach ($answers as $ans): ?>
                        <?php if (!empty($ans['explanation'])): ?>
                            <li>
                                <strong><?php echo intval($ans['is_correct']) === 1 ? '✅' : '❌'; ?> <?php echo render_inline_text($ans['text']); ?>:</strong>
                                <div><?php echo render_rich_text($ans['explanation']); ?></div>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

    </section>

    <aside class="ranking-panel">
        <div class="ranking-header">
            <span class="eyebrow">Live-Ranking</span>
            <h2>Punktestand</h2>
            <p>Die Teilnehmer mit den meisten Punkten stehen oben.</p>
        </div>
        <div class="ranking-list ranking-list-top-gap">
            <table class="ranking-table">
                <thead>
                    <tr>
                        <th>Pl.</th>
                        <th>Name</th>
                        <th class="score-head">Punkte</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rankingPlayers as $index => $player): ?>
                        <tr<?php echo ($player['username'] === $username) ? ' class="current-player"' : ''; ?>>
                            <td><?php echo ($index + 1); ?>.</td>
                            <td>
                                <?php echo htmlspecialchars($player['username']); ?>
                                <?php if ($player['username'] === $username) echo '<span class="you-badge">(Du)</span>'; ?>
                            </td>
                            <td class="score-cell"><?php echo $player['score']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </aside>
</main>

<script src="../JS/vendor/highlight.min.js"></script>
<script>
    if (window.hljs) { hljs.highlightAll(); }
</script>

<script>
(function() {
    <?php if (!$showExplanation): ?>
        const answerButtons = document.querySelectorAll('.millionaire-answer:not(.confirm-button)');
        answerButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-id');
                const checkbox = document.getElementById('check-' + targetId);
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                    this.classList.toggle('selected', checkbox.checked);
                }
            });
        });

        let timeLeft = <?php echo intval($timeLimit); ?>;
        const display = document.getElementById('timer-display');
        const form = document.getElementById('quiz-form');

        const countdown = setInterval(function() {
            timeLeft--;
            if (display) display.textContent = timeLeft;
            if (timeLeft <= 0) {
                clearInterval(countdown);
                let timeoutInput = document.createElement('input');
                timeoutInput.type = 'hidden';
                timeoutInput.name = 'timeout';
                timeoutInput.value = '1';
                form.appendChild(timeoutInput);
                form.action = 'next_question.php';
                form.submit();
            }
        }, 1000);
    <?php endif; ?>
})();
</script>
</body>
</html>
