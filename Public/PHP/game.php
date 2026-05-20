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
<link rel="stylesheet" href="../CSS/spiel.css">
<link rel="stylesheet" href="css/quiz-style.css">

<style>
/* ==============================
   Anpassungen für volle Bildschirmnutzung
   ============================== */
body.quiz-play-page {
    margin: 0;
    padding: 0;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background: #f4f7fa;
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

.play-layout {
    display: flex;
    flex: 1; /* nimmt gesamte Höhe ein */
    gap: 20px;
    padding: 20px;
    width: 100%;
    max-width: 1400px;
    margin: 0 auto;
    flex-wrap: wrap;
}

.quiz-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    background: #fff;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    position: relative;
}

.quiz-topline {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    position: relative;
}

.quiz-topline .eyebrow {
    font-size: 14px;
    color: #6c757d;
}

.quiz-topline h1 {
    margin: 0;
    font-size: 20px;
}

.timer-box {
    position: absolute;
    top: 0;
    right: 0;
    background: #007bff;
    color: #fff;
    padding: 5px 12px;
    border-radius: 6px;
    text-align: center;
    font-weight: bold;
}

/* Frage mittig */
.question-card h2 {
    text-align: center;
    font-size: 22px;
    margin: 16px 0;
}

/* Antworten 2x2 Raster */
.millionaire-answers {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px 16px;
    width: 100%;
    max-width: 600px;
    margin-top: 20px;
}

.millionaire-answer {
    background: #007bff;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 12px;
    font-size: 16px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    transition: all 0.2s ease;
}

.millionaire-answer:hover {
    background: #0056b3;
    transform: translateY(-2px);
}

.answer-letter {
    font-weight: bold;
    margin-right: 10px;
}

.answer-text {
    flex: 1;
}

/* Ranking rechts */
.ranking-panel {
    flex: 0 0 220px; /* schmaler */
    height: auto;
    max-height: 100vh;
    overflow-y: auto;
    background: #fff;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Responsive Anpassung */
@media (max-width: 1024px) {
    .play-layout {
        flex-direction: column;
    }

    .millionaire-answers {
        grid-template-columns: 1fr; /* Mobil 1 Spalte */
    }

    .ranking-panel {
        flex: 1 1 100%;
        max-height: none;
        margin-top: 20px;
    }
}
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