<?php
require_once 'init.php';
/** @var string $username */
/** @var string $role */

if (!isset($_SESSION['quiz_questions']) || empty($_SESSION['quiz_questions'])) {
    header("Location: setup_lobby.php");
    exit;
}

// 2. Aktuellen Frage-Index aus der Session holen (falls nicht gesetzt, starte bei 0)
if (!isset($_SESSION['current_question_index'])) {
    $_SESSION['current_question_index'] = 0;
}

$currentIndex = $_SESSION['current_question_index'];
$allQuestions = $_SESSION['quiz_questions'];
$totalQuestions = count($allQuestions);

if ($currentIndex >= $totalQuestions) {
    // Weiterleitung zur Auswertung / Ergebnis-Seite
    header("Location: results.php");
    exit;
}

// 4. Die exakt aktuelle Frage und ihre 4 Antworten greifen
$currentQuestion = $allQuestions[$currentIndex];
$answers = $currentQuestion['answers']; 

?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quiz spielen | damago Quizsystem</title>

<link rel="stylesheet" href="../CSS/style.css">


<style>

</style>

</head>
<body class="quiz-play-page">

<header class="topbar">
    <a class="topbar-brand">
        <img src="../damago-logo.png" alt="damago Logo" class="topbar-logo">
    </a>

    <div class="topbar-account">
        <span class="account-name">
            <?php echo htmlspecialchars($username); ?>
        </span>
        <a href="logout.php" class="logout-button">logout</a>
    </div>
</header>

<main class="play-layout">

    <section class="quiz-main">

        <div class="quiz-topline">
            <div>
                <span class="eyebrow">Frage <?php echo $currentIndex; ?> von <?php echo $totalQuestions; ?></span>
                <h1>Quizrunde läuft</h1>
            </div>

            <div class="timer-box">
                <span>Zeit</span>
                <strong><?php echo 'ZEIT'; ?></strong>
            </div>
        </div>

        <section class="question-card">
            <div class="question-label">Aktuelle Frage</div>
            <h2><?php echo htmlspecialchars($currentQuestion['question_text']); ?></h2>
        </section>

        <form class="millionaire-answers" action="#" method="post">
            <?php foreach ($answers as $letter => $text): ?>
                <button type="submit" name="answer" value="<?php echo $letter; ?>" class="millionaire-answer">
                    <span class="answer-letter"><?php echo $letter; ?></span>
                    <span class="answer-text"><?php echo htmlspecialchars($text['text']); ?></span>
                </button>
            <?php endforeach; ?>
        </form>

        <div class="quiz-help">
            <p>Wähle die richtige Antwort aus. Deine Antwort wird direkt gespeichert.</p>
        </div>

    </section>

    <aside class="ranking-panel">
        <div class="ranking-header">
            <span class="eyebrow">Live-Ranking</span>
            <h2>Punktestand</h2>
            <p>Die Teilnehmer mit den meisten Punkten stehen oben.</p>
        </div>

        <div class="ranking-list">
           
        </div>

        <div class="ranking-footer">
            <span>Teilnehmer: <?php echo "PLATZ"; ?></span>
        </div>
    </aside>

</main>
</body>
</html>