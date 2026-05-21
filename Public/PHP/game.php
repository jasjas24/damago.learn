<?php
require_once 'init.php';
/** @var string $username */
/** @var string $role */

// Befinden wir uns im Auflösungs-Modus?
$showExplanation = isset($_SESSION['show_explanation']) && $_SESSION['show_explanation'] === true;
$lastResult = $_SESSION['last_result'] ?? null;

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

$rankingPlayers = [
    [
        'username' => $_SESSION['username'] ?? 'Gast', 
        'score'    => $_SESSION['quiz_score'] ?? 0
    ]
    // Hier würden später weitere Spieler landen, z.B.:
    // ['username' => 'Max', 'score' => 450],
    ];

    // Sortiert das Ranking automatisch immer vom höchsten zum niedrigsten Punktestand
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
            <h2><?php echo htmlspecialchars($currentQuestion['question_text']); ?></h2>
        </section>

        <form class="millionaire-answers" id="quiz-form" action="next_question.php" method="post">
            <?php foreach ($answers as $letter => $text): ?>
                <button type="submit" name="answer" value="<?php echo $letter; ?>" class="millionaire-answer">
                   
                    <span class="answer-text"><?php echo htmlspecialchars($text['text']); ?></span>
                </button>
            <?php endforeach; ?>
        </form>

        <div class="quiz-help">
            <p>Wähle die richtige Antwort aus. Deine Antwort wird direkt gespeichert.</p>
        </div>

    </section>

    <aside class="ranking-panel">
        <section class="auth-card" style="height: fit-content;">
    <div class="auth-header">
        <span class="eyebrow">Live-Ranking</span>
        <h2>Punktestand</h2>
        <p>Die Teilnehmer mit den meisten Punkten stehen oben.</p>
    </div>

    <div class="ranking-list" style="margin-top: 20px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #eee; color: #666; font-size: 0.9rem;">
                    <th style="padding: 8px;">Pl.</th>
                    <th style="padding: 8px;">Name</th>
                    <th style="padding: 8px; text-align: right;">Punkte</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rankingPlayers as $index => $player): ?>
                    <tr style="border-bottom: 1px solid #f4f4f4; <?php echo ($player['username'] === $username) ? 'font-weight: bold; background-color: #f9f9f9;' : ''; ?>">
                        <td style="padding: 10px 8px;"><?php echo ($index + 1); ?>.</td>
                        <td style="padding: 10px 8px;">
                            <?php echo htmlspecialchars($player['username']); ?>
                            <?php if ($player['username'] === $username) echo ' (Du)'; ?>
                        </td>
                        <td style="padding: 10px 8px; text-align: right; color: #0066cc;">
                            <?php echo $player['score']; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
    </aside>

</main>

<script>
// 1. Übergabe der Zeit von PHP an JavaScript
let timeLeft = <?php echo intval($timeLimit); ?>;
const timerDisplay = document.getElementById('timer-display');
const quizForm = document.getElementById('quiz-form');

// 2. Der Intervall, der jede Sekunde (1000ms) ausgeführt wird
const countdown = setInterval(function() {
    timeLeft--;
    
    // Aktualisiere die Anzeige im HTML
    timerDisplay.textContent = timeLeft;
    

    // 3. Wenn die Zeit abgelaufen ist
    if (timeLeft <= 0) {
        clearInterval(countdown); // Timer stoppen
        
        // Erstelle ein unsichtbares Input-Feld, damit next_question.php weiß, 
        // dass die Zeit abgelaufen ist (keine Antwort ausgewählt)
        let timeoutInput = document.createElement('input');
        timeoutInput.type = 'hidden';
        timeoutInput.name = 'timeout';
        timeoutInput.value = '1';
        quizForm.appendChild(timeoutInput);
        
        // Formular automatisch abschicken!
        quizForm.submit();
    }
}, 1000);
</script>

</body>
</html>