<?php
require_once 'init.php';
require_once 'db.php';

/** @var string $username */

// ==========================================
// NEU: ABSICHERUNG FÜR MITSPIELER (SESSION BEFÜLLEN)
// ==========================================
$lobby_id = $_SESSION['quiz_setup']['lobby_id'] ?? $_SESSION['player_lobby_id'] ?? null;

// Falls die Fragen in der Session fehlen, versuchen wir sie aus der DB nachzuladen
if ((!isset($_SESSION['quiz_questions']) || empty($_SESSION['quiz_questions'])) && $lobby_id) {
    try {
        // 1. Fragen-IDs holen, die der Host in 'lobby_questions' hinterlegt hat
        $stmtLobbyQ = $pdo->prepare("
            SELECT q.id AS question_id, q.question_text, q.explanation AS general_explanation
            FROM lobby_questions lq
            INNER JOIN questions q ON q.id = lq.question_id
            WHERE lq.lobby_id = ?
            ORDER BY lq.sort_order ASC
        ");
        $stmtLobbyQ->execute([$lobby_id]);
        $questionsRaw = $stmtLobbyQ->fetchAll(PDO::FETCH_ASSOC);

        // Wenn Fragen in der DB existieren, bauen wir die Session für den Spieler auf
        if (!empty($questionsRaw)) {
            $quizQuestions = [];
            $stmtAnswers = $pdo->prepare("
                SELECT id, answer_text AS text, is_correct, explanation 
                FROM answer_options 
                WHERE question_id = ?
            ");

            foreach ($questionsRaw as $q) {
                $stmtAnswers->execute([$q['question_id']]);
                $answers = $stmtAnswers->fetchAll(PDO::FETCH_ASSOC);

                // Antworten für den Spieler mischen (Verhindert Abschreiben)
                shuffle($answers);

                $quizQuestions[] = [
                    'id'            => $q['question_id'],
                    'question_text' => $q['question_text'],
                    'explanation'   => $q['general_explanation'],
                    'answers'       => $answers
                ];
            }

            // Session für den Mitspieler initialisieren
            $_SESSION['quiz_questions'] = $quizQuestions;
            $_SESSION['current_question_index'] = 0;
            $_SESSION['quiz_score'] = 0;
            
            // Falls das Zeitlimit in der Session fehlt, aus der Lobby-Tabelle holen
            if (!isset($_SESSION['quiz_setup']['time_limit'])) {
                $stmtTime = $pdo->prepare("SELECT time_limit FROM quiz_lobbies WHERE id = ?");
                $stmtTime->execute([$lobby_id]);
                $dbTime = $stmtTime->fetchColumn();
                $_SESSION['quiz_setup']['time_limit'] = $dbTime ? (int)$dbTime : 30;
            }
        }
    } catch (PDOException $e) {
        // Falls was schiefgeht, greift unten der originale Türsteher
    }
}

// ==========================================
// ORIGINALER TÜRSTEHER (Jetzt modifiziert/abgesichert)
// ==========================================
if (!isset($_SESSION['quiz_questions']) || empty($_SESSION['quiz_questions'])) {
    header("Location: setup_lobby.php");
    exit;
}

// Befinden wir uns im Auflösungs-Modus?
$showExplanation = isset($_SESSION['show_explanation']) && $_SESSION['show_explanation'] === true;
$lastResult = $_SESSION['last_result'] ?? null;

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

// Dynamischer Name, falls Mitspieler (die haben oft 'player_name' statt 'username')
$currentDisplayName = $_SESSION['player_name'] ?? $username ?? 'Gast';

$rankingPlayers = [
    [
        'username' => $currentDisplayName, 
        'score'    => $_SESSION['quiz_score'] ?? 0
    ]
];

usort($rankingPlayers, function($a, $b) {
    return $b['score'] <=> $a['score'];
});

$timeLimit = $_SESSION['quiz_setup']['time_limit'] ?? 30;
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quiz spielen | damago Quizsystem</title>
<link rel="stylesheet" href="../CSS/style.css">

<style>
.millionaire-answer.selected {
    border: 5px solid #dbff0f !important;
}

.confirm-button-wrapper {
    margin-top: 20px;
    width: 100% !important;
    display: flex !important;
    justify-content: center !important;
    grid-column: 1 / -1 !important; 
}

.confirm-button-wrapper .confirm-button {
    width: max-content !important;
    min-width: 280px;
    margin: 0 auto !important;
}

.next-question-form {
    width: max-content;
    margin: 0;
}
</style>
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
            <h2><?php echo htmlspecialchars($currentQuestion['question_text']); ?></h2>
        </section>

        <form class="millionaire-answers" id="quiz-form" action="<?php echo $showExplanation ? 'go_next.php' : 'next_question.php'; ?>" method="POST">
            
            <?php foreach ($answers as $letter => $ans): 
                $inlineStyle = "";
                
                if ($showExplanation) {
                    $isCorrect = intval($ans['is_correct']) === 1;
                    $wasSelected = $lastResult && is_array($lastResult['chosen_ids']) && in_array($ans['id'], $lastResult['chosen_ids']);
                    
                    if ($isCorrect) {
                        $inlineStyle = "background-color: #d4edda !important; color: #155724 !important; border-color: #c3e6cb !important;";
                    } elseif ($wasSelected && !$isCorrect) {
                        $inlineStyle = "background-color: #f8d7da !important; color: #721c24 !important; border-color: #f5c6cb !important;";
                    }
                    
                    if ($wasSelected) {
                        $inlineStyle .= " border: 3px solid #ffc107 !important;";
                    }
                }
            ?>
                <button type="<?php echo $showExplanation ? 'submit' : 'button'; ?>" 
                        class="millionaire-answer" 
                        data-id="<?php echo $ans['id']; ?>"
                        style="<?php echo $inlineStyle; ?>"
                        <?php echo $showExplanation ? 'disabled' : ''; ?>>
                    <span class="answer-text">
                        <?php echo htmlspecialchars($ans['text']); ?>
                    </span>
                    
                    <?php if (!$showExplanation): ?>
                        <input type="checkbox" name="selected_answers[]" value="<?php echo $ans['id']; ?>" style="display:none;" id="check-<?php echo $ans['id']; ?>">
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

            <section class="question-card" style="margin-top: 30px; border-left: 5px solid #007bff; text-align: left;">
                <div class="question-label" style="color: #007bff;">Auflösung & Erklärungen</div>
                <div style="margin-top: 10px;">
                    <strong>Ergebnis:</strong> 
                    <?php 
                        if ($lastResult['status'] === 'correct') echo "<span style='color:green;'>Genial! Alle richtigen Antworten gefunden! (+".$lastResult['points_earned']." Punkte)</span>";
                        elseif ($lastResult['status'] === 'partial') echo "<span style='color:orange;'>Teilweise richtig! (+".$lastResult['points_earned']." Punkte)</span>";
                        elseif ($lastResult['status'] === 'timeout') echo "<span style='color:red;'>Zeit abgelaufen! (0 Punkte)</span>";
                        else echo "<span style='color:red;'>Leider falsch! (0 Punkte)</span>";
                    ?>
                </div>
                <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;">
                <ul style="list-style-type: none; padding-left: 0; display: flex; flex-direction: column; gap: 12px;">
                    <?php foreach ($answers as $ans): ?>
                        <?php if (!empty($ans['explanation'])): ?>
                            <li>
                                <strong><?php echo intval($ans['is_correct']) === 1 ? '✅' : '❌'; ?> <?php echo htmlspecialchars($ans['text']); ?>:</strong>
                                <p style="margin: 4px 0 0 24px; color: #555; font-style: italic;"><?php echo htmlspecialchars($ans['explanation']); ?></p>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

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
                            <tr style="border-bottom: 1px solid #f4f4f4; <?php echo ($player['username'] === $currentDisplayName) ? 'font-weight: bold; background-color: #f9f9f9;' : ''; ?>">
                                <td style="padding: 10px 8px;"><?php echo ($index + 1); ?>.</td>
                                <td style="padding: 10px 8px;">
                                    <?php echo htmlspecialchars($player['username']); ?>
                                    <?php if ($player['username'] === $currentDisplayName) echo ' (Du)'; ?>
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
(function() {
    <?php if (!$showExplanation): ?>
        const answerButtons = document.querySelectorAll('.millionaire-answer:not(.confirm-button)');
        answerButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-id');
                const checkbox = document.getElementById('check-' + targetId);
                
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                    if (checkbox.checked) {
                        this.classList.add('selected');
                    } else {
                        this.classList.remove('selected');
                    }
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
    <?php else: ?>
        const timerBox = document.querySelector('.timer-box');
        if (timerBox) timerBox.style.backgroundColor = '#666';
    <?php endif; ?>
})();
</script>
</body>
</html>