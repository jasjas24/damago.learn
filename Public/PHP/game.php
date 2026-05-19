<?php
session_start();

$username = $_SESSION["username"] ?? "Gast";
$role = $_SESSION["role"] ?? "gast";

/*
    Demo-Daten für das Frontend.
    Später kommen Frage, Antworten und Ranking aus der Datenbank.
*/

$questionNumber = 3;
$totalQuestions = 10;
$timeLeft = 24;

$question = "Welche Aussage über HTML ist korrekt?";

$answers = [
    "A" => "HTML ist eine Auszeichnungssprache",
    "B" => "HTML ist eine Datenbank",
    "C" => "HTML ersetzt PHP vollständig",
    "D" => "HTML ist ein Betriebssystem"
];

$ranking = [
    ["name" => "Lisa", "points" => 2450],
    ["name" => "Max", "points" => 2100],
    ["name" => "Gast-4821", "points" => 1750],
    ["name" => "Ben", "points" => 1200],
    ["name" => "Sara", "points" => 900]
];

usort($ranking, function ($a, $b) {
    return $b["points"] <=> $a["points"];
});
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz spielen | damago Quizsystem</title>

    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="../CSS/spiel.css">
    <!-- Quiz CSS hinzufügen -->
    <link rel="stylesheet" href="css/quiz-style.css">
</head>
<body class="quiz-play-page">

    <header class="topbar">
        <a href="dashboard.php" class="topbar-brand">
            <img src="../damago-logo.png" alt="damago Logo" class="topbar-logo">
        </a>

        <div class="topbar-account">
            <span class="account-name">
                <?php echo htmlspecialchars($username); ?>
            </span>

            <a href="logout.php" class="logout-button">
                logout
            </a>
        </div>
    </header>

    <main class="play-layout">

        <section class="quiz-main">

            <div class="quiz-topline">
                <div>
                    <span class="eyebrow">Frage <?php echo $questionNumber; ?> von <?php echo $totalQuestions; ?></span>
                    <h1>Quizrunde läuft</h1>
                </div>

                <div class="timer-box">
                    <span>Zeit</span>
                    <strong><?php echo $timeLeft; ?></strong>
                </div>
            </div>

            <section class="question-card">
                <div class="question-label">
                    Aktuelle Frage
                </div>

                <h2>
                    <?php echo htmlspecialchars($question); ?>
                </h2>
            </section>

            <form class="millionaire-answers" action="#" method="post">

                <button type="submit" name="answer" value="A" class="millionaire-answer">
                    <span class="answer-letter">A</span>
                    <span class="answer-text"><?php echo htmlspecialchars($answers["A"]); ?></span>
                </button>

                <button type="submit" name="answer" value="B" class="millionaire-answer">
                    <span class="answer-letter">B</span>
                    <span class="answer-text"><?php echo htmlspecialchars($answers["B"]); ?></span>
                </button>

                <button type="submit" name="answer" value="C" class="millionaire-answer">
                    <span class="answer-letter">C</span>
                    <span class="answer-text"><?php echo htmlspecialchars($answers["C"]); ?></span>
                </button>

                <button type="submit" name="answer" value="D" class="millionaire-answer">
                    <span class="answer-letter">D</span>
                    <span class="answer-text"><?php echo htmlspecialchars($answers["D"]); ?></span>
                </button>

            </form>

            <div class="quiz-help">
                <p>
                    Wähle die richtige Antwort aus. Deine Antwort wird direkt gespeichert.
                </p>
            </div>

        </section>

        <aside class="ranking-panel">
            <div class="ranking-header">
                <span class="eyebrow">Live-Ranking</span>
                <h2>Punktestand</h2>
                <p>Die Teilnehmer mit den meisten Punkten stehen oben.</p>
            </div>

            <div class="ranking-list">
                <?php foreach ($ranking as $index => $player): ?>
                    <div class="ranking-item <?php echo $index === 0 ? 'rank-first' : ''; ?>">
                        <div class="rank-position">
                            <?php echo $index + 1; ?>
                        </div>

                        <div class="rank-player">
                            <strong><?php echo htmlspecialchars($player["name"]); ?></strong>
                            <span><?php echo htmlspecialchars($player["points"]); ?> Punkte</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="ranking-footer">
                <span>Teilnehmer: <?php echo count($ranking); ?></span>
            </div>
        </aside>

    </main>

</body>
</html>
