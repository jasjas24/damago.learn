<?php
require_once 'init.php';
require_once 'db.php';

/** @var string $username */

if (!isset($_SESSION['quiz_questions'])) {
    $lobby_id = $_SESSION['player_lobby_id'] ?? null;
    if ($lobby_id) {
        $stmtLoad = $pdo->prepare("SELECT quiz_data FROM quiz_lobbies WHERE id = ?");
        $stmtLoad->execute([$lobby_id]);
        $json = $stmtLoad->fetchColumn();
        if ($json) {
            $_SESSION['quiz_questions'] = json_decode($json, true);
            $_SESSION['current_question_index'] = 0;
        }
    }
}
// ==========================================
// ABSICHERUNG FÜR MITSPIELER (SESSION BEFÜLLEN)
// ==========================================
$lobby_id = $_SESSION['quiz_setup']['lobby_id'] ?? $_SESSION['player_lobby_id'] ?? null;

// ERWEITERUNG: PointMode aus der DB holen, falls noch nicht in der Session vorhanden
if ($lobby_id && !isset($_SESSION['quiz_setup']['point_mode'])) {
    try {
        $stmtMode = $pdo->prepare("SELECT point_mode FROM quiz_lobbies WHERE id = ?");
        $stmtMode->execute([$lobby_id]);
        $dbMode = $stmtMode->fetchColumn();
        $_SESSION['quiz_setup']['point_mode'] = $dbMode ? $dbMode : 'all_or_nothing';
    } catch (PDOException $e) {}
}
$pointMode = $_SESSION['quiz_setup']['point_mode'] ?? 'all_or_nothing';

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

// ERWEITERUNG: Alle Spieler der Lobby für das Live-Ranking aus der DB holen
$rankingPlayers = [];
if ($lobby_id) {
    try {
        $stmtRank = $pdo->prepare("
            SELECT player_name AS username, points AS score
            FROM lobby_players
            WHERE lobby_id = ?
            ORDER BY points DESC, player_name ASC
        ");
        $stmtRank->execute([$lobby_id]);
        $rankingPlayers = $stmtRank->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $rankingPlayers = [[ 'username' => $currentDisplayName, 'score' => $_SESSION['quiz_score'] ?? 0 ]];
    }
}

if (empty($rankingPlayers)) {
    $rankingPlayers = [[ 'username' => $currentDisplayName, 'score' => $_SESSION['quiz_score'] ?? 0 ]];
}
$timeLimit = $_SESSION['quiz_setup']['time_limit'] ?? 30;
$isHost = isset($_SESSION['quiz_setup']['lobby_id']);

// Anzeige-Wert fuer den Timer:
// - Laufende Frage: volle Zeit ($timeLimit), der Countdown laeuft per JS herunter.
// - Beim Aufloesen / Warten: die Restzeit, die beim Beantworten uebrig war,
//   bleibt stehen (last_remaining_time). Zurueckgesetzt wird erst bei der naechsten Frage.
$timerDisplay = $timeLimit;
if (($showExplanation || $waitingForReveal) && isset($_SESSION['last_remaining_time'])) {
    $timerDisplay = (int) $_SESSION['last_remaining_time'];
}

// ERWEITERUNG: Hole alle IDs der Richtig-Antworten für die JS-Berechnung heraus
$correctAnswersIds = [];
foreach ($answers as $ans) {
    if (intval($ans['is_correct']) === 1) {
        $correctAnswersIds[] = intval($ans['id']);
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quiz spielen | damago Quizsystem</title>
<link rel="stylesheet" href="../CSS/style.css">
<style>
.millionaire-answer.selected { border: 5px solid #dbff0f; }
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
            </div>
            <div class="timer-box">
                <strong id="timer-display"><?php echo $timerDisplay; ?></strong>
            </div>
        </div>

        <section class="question-card">
            <h2><?php echo htmlspecialchars($currentQuestion['question_text']); ?></h2>
        </section>

        <?php if ($waitingForReveal && !$showExplanation): ?>
            <div class="confirm-button-wrapper" style="flex-direction: column; align-items: center; gap: 15px; margin-top: 40px;">
                <div class="loader"></div>
                <button type="button" class="millionaire-answer confirm-button submitted" disabled>
                    <span class="answer-text">Antwort abgegeben. Warte auf Mitspieler...</span>
                </button>
            </div>
        <?php else: ?>
            <form class="millionaire-answers" id="quiz-form" action="next_question.php" method="POST">
                <?php foreach ($answers as $letter => $ans):
                    $inlineStyle = "";
                    if ($showExplanation) {
                        $isCorrect = intval($ans['is_correct']) === 1;
                        $wasSelected = $lastResult && isset($lastResult['chosen_ids']) && is_array($lastResult['chosen_ids']) && in_array($ans['id'], $lastResult['chosen_ids']);
                        // Grundfaerbung bei der Aufloesung: ALLE richtigen Antworten gruen,
                        // ALLE falschen rot - unabhaengig davon, ob sie gewaehlt wurden.
                        if ($isCorrect) {
                            $inlineStyle = "background: rgba(34,197,94,0.20) !important; color: #86efac !important; border: 1px solid rgba(34,197,94,0.50) !important;";
                        } else {
                            $inlineStyle = "background: rgba(239,68,68,0.20) !important; color: #fca5a5 !important; border: 1px solid rgba(239,68,68,0.50) !important;";
                        }
                        if ($wasSelected && $isCorrect) {
                            $inlineStyle .= " border: 4px solid #86efac !important; box-shadow: 0 0 0 5px rgba(34,197,94,0.45) !important;";
                        } elseif ($wasSelected && !$isCorrect) {
                            $inlineStyle .= " border: 4px solid #fca5a5 !important; box-shadow: 0 0 0 5px rgba(239,68,68,0.45) !important;";
                        }
                    }
                ?>
                    <button type="button"
                            class="millionaire-answer"
                            data-id="<?php echo $ans['id']; ?>"
                            style="<?php echo $inlineStyle; ?>"
                            <?php echo $showExplanation ? 'disabled' : ''; ?>>
                        <span class="answer-text"><?php echo htmlspecialchars($ans['text']); ?></span>
                        <?php if (!$showExplanation): ?>
                            <input type="checkbox" class="answer-checkbox" name="selected_answers[]" value="<?php echo $ans['id']; ?>" style="display:none;" id="check-<?php echo $ans['id']; ?>">
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
                    <button type="button" class="millionaire-answer confirm-button" disabled>
                        <span class="answer-text">Warte auf den Host...</span>
                    </button>
                <?php endif; ?>
            </div>

            <section class="question-card" style="margin-top: 28px; border-left: 3px solid rgba(74,133,199,0.70); padding: 18px 20px; text-align: left; background: rgba(255,255,255,0.05); border-radius: var(--radius-md, 14px);">
                <div class="question-label" style="color: #4a85c7; margin-bottom: 10px;">Auflösung &amp; Erklärungen</div>
                <div style="margin-top: 6px; font-weight: 600; color: rgba(255,255,255,0.90);">
                    <strong>Ergebnis:</strong>
                    <?php
                        if (empty($lastResult) || !isset($lastResult['status'])) {
                            echo "<span style='color:rgba(255,255,255,0.60);'>Frage beendet! Schau dir unten die Erklärungen an.</span>";
                        } else {
                            if ($lastResult['status'] === 'correct') {
                                echo "<span style='color:#86efac;'>Genial! Alle richtigen Antworten gefunden! (+".($lastResult['points_earned'] ?? 0)." Punkte)</span>";
                            } elseif ($lastResult['status'] === 'partial') {
                                echo "<span style='color:#fbbf24;'>Teilweise richtig! (+".($lastResult['points_earned'] ?? 0)." Punkte)</span>";
                            } elseif ($lastResult['status'] === 'timeout') {
                                echo "<span style='color:#fca5a5;'>Zeit abgelaufen! (0 Punkte)</span>";
                            } else {
                                echo "<span style='color:#fca5a5;'>Leider falsch! (0 Punkte)</span>";
                            }
                        }
                    ?>
                </div>
                <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.12); margin: 14px 0;">
                <ul style="list-style-type: none; padding-left: 0; display: flex; flex-direction: column; gap: 12px;">
                    <?php foreach ($answers as $ans): ?>
                        <?php if (!empty($ans['explanation'])): ?>
                            <li>
                                <strong style="color: rgba(255,255,255,0.90);"><?php echo intval($ans['is_correct']) === 1 ? '✅' : '❌'; ?> <?php echo htmlspecialchars($ans['text']); ?>:</strong>
                                <p style="margin: 5px 0 0 24px; color: rgba(255,255,255,0.55); font-style: italic; font-size: 14px; line-height: 1.5;"><?php echo htmlspecialchars($ans['explanation']); ?></p>
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
        </div>
        <div class="ranking-list" style="margin-top: 16px;">
            <table class="ranking-table">
                <thead>
                    <tr>
                        <th>Pl.</th>
                        <th>Name</th>
                        <th style="text-align: right;">Punkte</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rankingPlayers as $index => $player): ?>
                        <tr<?php echo ($player['username'] === $currentDisplayName) ? ' class="current-player"' : ''; ?>>
                            <td><?php echo ($index + 1); ?>.</td>
                            <td>
                                <?php echo htmlspecialchars($player['username']); ?>
                                <?php if ($player['username'] === $currentDisplayName) echo '<span class="you-badge">(Du)</span>'; ?>
                            </td>
                            <td class="score-cell"><?php echo $player['score']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </aside>
</main>

<script src="functions.js"></script>

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

        // Antwort bestätigen: roter Rahmen als visuelle Bestätigung vor dem Absenden
        const confirmBtn = document.getElementById('confirm-btn');
        const form = document.getElementById('quiz-form');
        if (confirmBtn && form) {
            confirmBtn.addEventListener('click', function(e) {
                e.preventDefault();
                this.classList.add('submitted');
                this.disabled = true;
                // Verbleibende Zeit mitsenden, damit sie beim Aufloesen angezeigt werden kann
                let rt = document.createElement('input');
                rt.type = 'hidden';
                rt.name = 'remaining_time';
                rt.value = Math.max(0, timeLeft);
                form.appendChild(rt);
                setTimeout(() => form.submit(), 350);
            });
        }

        // COUNTDOWN TIMER FIXED: Übergibt jetzt alle ausgewählten Haken sicher per Form-Submit
        let timeLeft = <?php echo intval($timeLimit); ?>;
        const display = document.getElementById('timer-display');
        const timerBox = document.querySelector('.timer-box');

        const countdown = setInterval(function() {
            timeLeft--;
            if (display) display.textContent = timeLeft;

            // Ab 10 Sekunden pulsieren, ab 5 Sekunden schneller pulsieren + rot.
            // Die CSS-Klassen .pulse und .danger sind in style.css definiert.
            if (timerBox) {
                if (timeLeft <= 5) {
                    timerBox.classList.add('pulse', 'danger');
                } else if (timeLeft <= 10) {
                    timerBox.classList.add('pulse');
                }
            }

            if (timeLeft <= 0) {
                clearInterval(countdown);
                
                // Timeout-Flag hinzufügen, damit PHP den Zeitablauf registriert
                let timeoutInput = document.createElement('input');
                timeoutInput.type = 'hidden';
                timeoutInput.name = 'timeout';
                timeoutInput.value = '1';
                form.appendChild(timeoutInput);

                // Restzeit = 0 mitsenden (Zeit ist abgelaufen)
                let rt = document.createElement('input');
                rt.type = 'hidden';
                rt.name = 'remaining_time';
                rt.value = '0';
                form.appendChild(rt);

                // Formular absenden. Die gesetzten Checkboxen werden automatisch mitgeschickt!
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
<<<<<<< HEAD
=======

<<<<<<< HEAD

=======
>>>>>>> 25febf771d49d9d317772e683aef309304c7c70d
>>>>>>> 5288ae49438c39952c7a75a254b0e80deec7d7c4
})();
</script>
</body>
</html>