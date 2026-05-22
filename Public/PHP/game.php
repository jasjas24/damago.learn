<?php
require_once 'init.php';
require_once 'db.php';

/** @var string $username */

// ==========================================
// ABSICHERUNG FÜR MITSPIELER (SESSION BEFÜLLEN)
// ==========================================
$lobby_id = $_SESSION['quiz_setup']['lobby_id'] ?? $_SESSION['player_lobby_id'] ?? null;

if ((!isset($_SESSION['quiz_questions']) || empty($_SESSION['quiz_questions'])) && $lobby_id) {
    try {
        $stmtLobbyQ = $pdo->prepare("
            SELECT q.id AS question_id, q.question_text, q.explanation AS general_explanation
            FROM lobby_questions lq
            INNER JOIN questions q ON q.id = lq.question_id
            WHERE lq.lobby_id = ?
            ORDER BY lq.sort_order ASC
        ");
        $stmtLobbyQ->execute([$lobby_id]);
        $questionsRaw = $stmtLobbyQ->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($questionsRaw)) {
            $quizQuestions = [];
            $stmtAnswers = $pdo->prepare("SELECT id, answer_text AS text, is_correct, explanation FROM answer_options WHERE question_id = ?");

            foreach ($questionsRaw as $q) {
                $stmtAnswers->execute([$q['question_id']]);
                $answers = $stmtAnswers->fetchAll(PDO::FETCH_ASSOC);
                shuffle($answers);

                $quizQuestions[] = [
                    'id'            => $q['question_id'],
                    'question_text' => $q['question_text'],
                    'explanation'   => $q['general_explanation'],
                    'answers'       => $answers
                ];
            }

            $_SESSION['quiz_questions'] = $quizQuestions;
            $_SESSION['current_question_index'] = 0;
            $_SESSION['quiz_score'] = 0;
            
            if (!isset($_SESSION['quiz_setup']['time_limit'])) {
                $stmtTime = $pdo->prepare("SELECT time_limit FROM quiz_lobbies WHERE id = ?");
                $stmtTime->execute([$lobby_id]);
                $dbTime = $stmtTime->fetchColumn();
                $_SESSION['quiz_setup']['time_limit'] = $dbTime ? (int)$dbTime : 30;
            }
        }
    } catch (PDOException $e) {}
}

// Synchronisiere den show_explanation-Status direkt aus der DB beim Seitenaufruf
if ($lobby_id) {
    try {
        $stmtStatus = $pdo->prepare("SELECT show_explanation, current_question_index FROM quiz_lobbies WHERE id = ?");
        $stmtStatus->execute([$lobby_id]);
        $lobbyStatus = $stmtStatus->fetch(PDO::FETCH_ASSOC);
        if ($lobbyStatus) {
            $_SESSION['show_explanation'] = (int)$lobbyStatus['show_explanation'] === 1;
            $_SESSION['current_question_index'] = (int)$lobbyStatus['current_question_index'];
            
            // NEU & ENTSCHEIDEND: Wenn die DB sagt "Auflösung läuft", 
            // darf der Mitspieler nicht mehr im Wartezustand festsitzen!
            if ($_SESSION['show_explanation']) {
                unset($_SESSION['waiting_for_reveal']);
            }
        }
    } catch (PDOException $e) {}
}

if (!isset($_SESSION['quiz_questions']) || empty($_SESSION['quiz_questions'])) {
    header("Location: setup_lobby.php");
    exit;
}

$showExplanation = isset($_SESSION['show_explanation']) && $_SESSION['show_explanation'] === true;
$waitingForReveal = isset($_SESSION['waiting_for_reveal']) && $_SESSION['waiting_for_reveal'] === true;
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
$currentDisplayName = $_SESSION['player_name'] ?? $username ?? 'Gast';

$rankingPlayers = [[ 'username' => $currentDisplayName, 'score' => $_SESSION['quiz_score'] ?? 0 ]];
$timeLimit = $_SESSION['quiz_setup']['time_limit'] ?? 30;
$isHost = isset($_SESSION['quiz_setup']['lobby_id']);
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quiz spielen | damago Quizsystem</title>
<link rel="stylesheet" href="../CSS/style.css">
<style>
.millionaire-answer.selected { border: 5px solid #dbff0f !important; }
.confirm-button-wrapper { margin-top: 20px; width: 100% !important; display: flex !important; justify-content: center !important; grid-column: 1 / -1 !important; }
.confirm-button-wrapper .confirm-button { width: max-content !important; min-width: 280px; margin: 0 auto !important; }
.next-question-form { width: max-content; margin: 0; }
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

        <?php if ($waitingForReveal && !$showExplanation): ?>
            <div class="confirm-button-wrapper" style="flex-direction: column; align-items: center; gap: 15px; margin-top: 40px;">
                <div class="loader"></div> 
                <button type="button" class="millionaire-answer confirm-button" style="background-color: #28a745; color: #fff; cursor: not-allowed;" disabled>
                    <span class="answer-text">Antwort abgegeben. Warte auf Mitspieler...</span>
                </button>
            </div>
        <?php else: ?>
            <form class="millionaire-answers" id="quiz-form" action="<?php echo $showExplanation ? 'go_next.php' : 'next_question.php'; ?>" method="POST">
                <?php foreach ($answers as $letter => $ans): 
                    $inlineStyle = "";
                    if ($showExplanation) {
                        $isCorrect = intval($ans['is_correct']) === 1;
                        $wasSelected = $lastResult && isset($lastResult['chosen_ids']) && is_array($lastResult['chosen_ids']) && in_array($ans['id'], $lastResult['chosen_ids']);
                        if ($isCorrect) {
                            $inlineStyle = "background-color: #d4edda !important; color: #155724 !important; border-color: #c3e6cb !important;";
                        } elseif ($wasSelected && !$isCorrect) {
                            $inlineStyle = "background-color: #f8d7da !important; color: #721c24 !important; border-color: #f5c6cb !important;";
                        }
                        if ($wasSelected) { $inlineStyle .= " border: 3px solid #ffc107 !important;"; }
                    }
                ?>
                    <button type="<?php echo $showExplanation ? 'submit' : 'button'; ?>" 
                            class="millionaire-answer" 
                            data-id="<?php echo $ans['id']; ?>"
                            style="<?php echo $inlineStyle; ?>"
                            <?php echo $showExplanation ? 'disabled' : ''; ?>>
                        <span class="answer-text"><?php echo htmlspecialchars($ans['text']); ?></span>
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
        <?php endif; ?>

        <?php if ($showExplanation): ?>
            <div class="confirm-button-wrapper">
                <?php if ($isHost): ?>
                    <form action="go_next.php" method="POST" class="next-question-form">
                        <button type="submit" class="millionaire-answer confirm-button btn-green">
                            <span class="answer-text">Nächste Frage (Host)</span>
                        </button>
                    </form>
                <?php else: ?>
                    <button type="button" class="millionaire-answer confirm-button" style="background-color: #666; cursor: not-allowed;" disabled>
                        <span class="answer-text">Warte auf den Host...</span>
                    </button>
                <?php endif; ?>
            </div>

            <section class="question-card" style="margin-top: 30px; border-left: 5px solid #007bff; text-align: left;">
                <div class="question-label" style="color: #007bff;">Auflösung & Erklärungen</div>
                <div style="margin-top: 10px;">
                    <strong>Ergebnis:</strong> 
                    <?php 
                        if (empty($lastResult) || !isset($lastResult['status'])) {
                            echo "<span style='color:#555;'>Frage beendet! Schau dir unten die Erklärungen an.</span>";
                        } else {
                            if ($lastResult['status'] === 'correct') {
                                echo "<span style='color:green;'>Genial! Alle richtigen Antworten gefunden! (+".($lastResult['points_earned'] ?? 0)." Punkte)</span>";
                            } elseif ($lastResult['status'] === 'partial') {
                                echo "<span style='color:orange;'>Teilweise richtig! (+".($lastResult['points_earned'] ?? 0)." Punkte)</span>";
                            } elseif ($lastResult['status'] === 'timeout') {
                                echo "<span style='color:red;'>Zeit abgelaufen! (0 Punkte)</span>";
                            } else {
                                echo "<span style='color:red;'>Leider falsch! (0 Punkte)</span>";
                            }
                        }
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
    // 1. ANTWORTEN AUSWÄHLEN (NUR WENN NOCH NICHT ABGEGEBEN)
    <?php if (!$showExplanation && !$waitingForReveal): ?>
        const answerButtons = document.querySelectorAll('.millionaire-answer:not(.confirm-button)');
        answerButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-id');
                const checkbox = document.getElementById('check-' + targetId);
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                    if (checkbox.checked) { this.classList.add('selected'); } 
                    else { this.classList.remove('selected'); }
                }
            });
        });

        // COUNTDOWN TIMER
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

    // 2. PERMANENTES POLLING FÜR SYNCHRONISATION
    <?php if ($waitingForReveal || ($showExplanation && !$isHost)): ?>
        const currentIdx = <?php echo intval($currentIndex); ?>;
        const checkInterval = setInterval(function() {
            fetch('check_next_question.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.action === 'reload') {
                            clearInterval(checkInterval);
                            window.location.reload();
                        } else if (data.redirect_to_results) {
                            clearInterval(checkInterval);
                            window.location.href = 'results.php';
                        }
                    }
                })
                .catch(err => console.error("Fehler beim Abrufen des Spielstatus:", err));
        }, 1500);
    <?php endif; ?>

    <?php if ($showExplanation): ?>
        const timerBox = document.querySelector('.timer-box');
        if (timerBox) timerBox.style.backgroundColor = '#666';
    <?php endif; ?>
})();
</script>
</body>
</html>