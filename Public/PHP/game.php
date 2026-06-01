<?php
require_once 'init.php';
require_once 'db.php';

/** @var string $username */

// 1. Lobby-ID und Host-Status ermitteln
$isHost = isset($_SESSION['quiz_setup']['lobby_id']);
$lobby_id = $_SESSION['quiz_setup']['lobby_id'] ?? $_SESSION['player_lobby_id'] ?? null;

// 2. Prüfen auf !isset ODER empty, damit leere Arrays neu aus der DB geladen werden
if (!isset($_SESSION['quiz_questions']) || empty($_SESSION['quiz_questions'])) {
    if ($lobby_id) {
        try {
            // Variante A: Versuche die Fragen als JSON aus quiz_lobbies zu laden
            $stmtLoad = $pdo->prepare("SELECT quiz_data FROM quiz_lobbies WHERE id = ?");
            $stmtLoad->execute([$lobby_id]);
            $json = $stmtLoad->fetchColumn();
            
            if ($json) {
                $decoded = json_decode($json, true);
                if (!empty($decoded)) {
                    $_SESSION['quiz_questions'] = $decoded;
                    $_SESSION['current_question_index'] = 0;
                }
            }

            // Variante B: Falls JSON leer ist, ziehe die Fragen relational aus lobby_questions
            if (!isset($_SESSION['quiz_questions']) || empty($_SESSION['quiz_questions'])) {
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
                    $_SESSION['quiz_score'] = $_SESSION['quiz_score'] ?? 0;
                }
            }
        } catch (PDOException $e) {
            // Fehler abfangen
        }
    }
}

// 3. ALLE Einstellungen (PointMode, Status UND Zeitlimit) direkt aus der DB holen
$timeLimit = 30; // Globaler Fallback, falls in der DB nichts steht

if ($lobby_id) {
    try {
        $stmtStatus = $pdo->prepare("SELECT show_explanation, current_question_index, point_mode, time_limit FROM quiz_lobbies WHERE id = ?");
        $stmtStatus->execute([$lobby_id]);
        $lobbyStatus = $stmtStatus->fetch(PDO::FETCH_ASSOC);
        
        if ($lobbyStatus) {
            $_SESSION['show_explanation'] = (int)$lobbyStatus['show_explanation'] === 1;
            $_SESSION['current_question_index'] = (int)$lobbyStatus['current_question_index'];
            
            // Point-Mode synchronisieren
            $_SESSION['quiz_setup']['point_mode'] = $lobbyStatus['point_mode'] ? $lobbyStatus['point_mode'] : 'all_or_nothing';
            
            // NEU: Zeitlimit live aus der DB holen!
            if (!empty($lobbyStatus['time_limit'])) {
                $timeLimit = (int)$lobbyStatus['time_limit'];
                $_SESSION['quiz_setup']['time_limit'] = $timeLimit;
            }

            if ($_SESSION['show_explanation']) {
                unset($_SESSION['waiting_for_reveal']);
            }
        }
    } catch (PDOException $e) {}
}

// Fallback für den Point-Mode, falls DB-Abfrage fehlschlug
$pointMode = $_SESSION['quiz_setup']['point_mode'] ?? 'all_or_nothing';

// HOST PLAYS STATUS ABFRAGEN (NEU)
$hostPlays = $_SESSION['quiz_setup']['host_plays'] ?? 'yes';

// Falls der Host die Variable noch in der Session hat, nutzen wir die als primäre Quelle
if (isset($_SESSION['quiz_setup']['time_limit'])) {
    $timeLimit = (int)$_SESSION['quiz_setup']['time_limit'];
}

$retryLoading = false;
if (!isset($_SESSION['quiz_questions']) || empty($_SESSION['quiz_questions'])) {
    if ($lobby_id && !$isHost) {
        $retryLoading = true;
    } else {
        header("Location: dashboard.php");
        exit;
    }
}

$showExplanation = isset($_SESSION['show_explanation']) && $_SESSION['show_explanation'] === true;
$waitingForReveal = isset($_SESSION['waiting_for_reveal']) && $_SESSION['waiting_for_reveal'] === true;
$lastResult = $_SESSION['last_result'] ?? null;

if (!isset($_SESSION['current_question_index'])) {
    $_SESSION['current_question_index'] = 0;
}

$currentIndex = $_SESSION['current_question_index'];
$allQuestions = $_SESSION['quiz_questions'] ?? [];
$totalQuestions = count($allQuestions);

if (!$retryLoading && $totalQuestions > 0 && $currentIndex >= $totalQuestions) {
    header("Location: results.php");
    exit;
}

$currentQuestion = $allQuestions[$currentIndex] ?? ['question_text' => '', 'answers' => []];
$answers = $currentQuestion['answers'] ?? [];
$currentDisplayName = $_SESSION['player_name'] ?? $username ?? 'Gast';

// Alle Spieler der Lobby für das Live-Ranking aus der DB holen
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

$correctAnswersIds = [];
foreach ($answers as $ans) {
    if (isset($ans['is_correct']) && intval($ans['is_correct']) === 1) {
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
</head>
<body class="quiz-play-page game-live">

<?php include_once 'topbar.php'; ?>

<?php if ($retryLoading): ?>
    <main class="play-layout play-layout-loading">
        <section class="question-card question-card-loading">
            <div class="loader loader-centered"></div>
            <h2 class="loading-title">Das Spiel startet gleich...</h2>
            <p class="loading-text">
                Die Fragen werden im Hintergrund vom Server vorbereitet. Bitte warten...
            </p>
        </section>
    </main>
    <script>
        setTimeout(function() {
            window.location.reload();
        }, 1500);
    </script>
<?php else: ?>

    <main class="play-layout">
        <section class="quiz-main">
            <div class="quiz-topline">
                <div>
                    <span class="eyebrow">Frage <?php echo $currentIndex + 1; ?> von <?php echo $totalQuestions; ?></span>
                </div>
                <div class="timer-box">
                    <strong><span id="timer-display"><?php echo $timeLimit; ?></span></strong>
                </div>
            </div>

            <section class="question-card">
                <h2><?php echo htmlspecialchars($currentQuestion['question_text']); ?></h2>
            </section>

            <?php if ($waitingForReveal && !$showExplanation): ?>
                <div class="confirm-button-wrapper confirm-button-wrapper-waiting">
                    <div class="loader"></div>
                    <button type="button" class="millionaire-answer confirm-button submitted" disabled>
                        <span class="answer-text">Antwort abgegeben. Warte auf Mitspieler...</span>
                    </button>
                </div>
            <?php elseif ($isHost && $hostPlays === 'no' && !$showExplanation): ?>
                <div class="confirm-button-wrapper confirm-button-wrapper-waiting">
                    <div class="loader"></div>
                    <button type="button" class="millionaire-answer confirm-button submitted" disabled>
                        <span class="answer-text">Du bist der Moderator. Warte auf die Antworten der Spieler...</span>
                    </button>
                </div>
            <?php else: ?>
                <form class="millionaire-answers" id="quiz-form" action="next_question.php" method="POST">
                    <?php foreach ($answers as $letter => $ans):
                        $revealClass = "";
                        if ($showExplanation) {
                            $isCorrect = intval($ans['is_correct']) === 1;
                            $wasSelected = $lastResult && isset($lastResult['chosen_ids']) && is_array($lastResult['chosen_ids']) && in_array($ans['id'], $lastResult['chosen_ids']);

                            if ($isCorrect) {
                                $revealClass = "answer-reveal-correct";
                            } else {
                                $revealClass = "answer-reveal-wrong";
                            }
                            if ($wasSelected && $isCorrect) {
                                $revealClass .= " answer-reveal-chosen";
                            } elseif ($wasSelected && !$isCorrect) {
                                $revealClass .= " answer-reveal-chosen";
                            }
                        }
                    ?>
                        <button type="button"
                                class="millionaire-answer <?php echo $revealClass; ?>"
                                data-id="<?php echo $ans['id']; ?>"
                                <?php echo $showExplanation ? 'disabled' : ''; ?>>
                            <span class="answer-text">
                                <?php echo htmlspecialchars($ans['text']); ?>
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

                <section class="question-card reveal-card">
                    <div class="question-label">Auflösung &amp; Erklärungen</div>
                    <div class="reveal-result">
                        <strong>Ergebnis:</strong>
                        <?php
                            if (empty($lastResult) || !isset($lastResult['status'])) {
                                echo "<span class='reveal-result-neutral'>Frage beendet! Schau dir unten die Erklärungen an.</span>";
                            } else {
                                if ($lastResult['status'] === 'correct') {
                                    echo "<span class='reveal-result-correct'>Genial! Alle richtigen Antworten gefunden! (+".($lastResult['points_earned'] ?? 0)." Punkte)</span>";
                                } elseif ($lastResult['status'] === 'partial') {
                                    echo "<span class='reveal-result-partial'>Teilweise richtig! (+".($lastResult['points_earned'] ?? 0)." Punkte)</span>";
                                } elseif ($lastResult['status'] === 'timeout') {
                                    echo "<span class='reveal-result-wrong'>Zeit abgelaufen! (0 Punkte)</span>";
                                } else {
                                    if ($pointMode === 'all_or_nothing') {
                                        echo "<span class='reveal-result-wrong'>Leider falsch oder unvollständig! (0 Punkte im Modus: Ganz oder Gar Nicht)</span>";
                                    } else {
                                        echo "<span class='reveal-result-wrong'>Leider falsch! (0 Punkte)</span>";
                                    }
                                }
                            }
                        ?>
                    </div>
                    <hr class="reveal-divider">
                    <ul class="reveal-explanation-list">
                        <?php foreach ($answers as $ans): ?>
                            <?php if (!empty($ans['explanation'])): ?>
                                <?php
                                    $exCorrect  = intval($ans['is_correct']) === 1;
                                    $exSelected = $lastResult && isset($lastResult['chosen_ids']) && is_array($lastResult['chosen_ids']) && in_array($ans['id'], $lastResult['chosen_ids']);
                                    $exClass = $exCorrect
                                        ? "reveal-exp-correct"
                                        : "reveal-exp-wrong";
                                    if ($exSelected && $exCorrect) {
                                        $exClass .= " reveal-exp-chosen-correct";
                                    } elseif ($exSelected && !$exCorrect) {
                                        $exClass .= " reveal-exp-chosen-wrong";
                                    }
                                ?>
                                <li class="reveal-exp-item <?php echo $exClass; ?>">
                                    <strong class="reveal-exp-strong"><?php echo htmlspecialchars($ans['text']); ?>:</strong>
                                    <p class="reveal-exp-text"><?php echo htmlspecialchars($ans['explanation']); ?></p>
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

    <script src="/damago/Public/JS/functions.js?v=<?php echo time(); ?>"></script>

    <script>
    (function() {
        // 1. ANTWORTEN AUSWÄHLEN (NUR WENN NOCH NICHT ABGEGEBEN)
        <?php if (!$showExplanation && !$waitingForReveal && !($isHost && $hostPlays === 'no')): ?>
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

            const confirmBtn = document.getElementById('confirm-btn');
            const form = document.getElementById('quiz-form');
            if (confirmBtn && form) {
                confirmBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    this.classList.add('submitted');
                    this.disabled = true;
                    setTimeout(() => form.submit(), 350);
                });
            }
        <?php endif; ?>

        // 2. GEMEINSAMER TIMER FÜR SPIELER UND MODERATOR (Sauber deklariert)
        <?php if (!$showExplanation): ?>
            let timeLeft = <?php echo intval($timeLimit); ?>;
            const display = document.getElementById('timer-display');

            <?php if (!$waitingForReveal || ($isHost && $hostPlays === 'no')): ?>
                const countdown = setInterval(function() {
                    timeLeft--;
                    if (display) display.textContent = timeLeft;

                    if (timeLeft <= 0) {
                        clearInterval(countdown);
                        
                        // Das automatische Formular-Submit wird NUR beim aktiven Mitspieler ausgelöst
                        <?php if (!($isHost && $hostPlays === 'no')): ?>
                            console.log("Die Zeit ist abgelaufen! Führe jetzt den Klick aus...");
                            const form = document.getElementById('quiz-form');
                            if (form) {
                                let timeoutInput = document.createElement('input');
                                timeoutInput.type = 'hidden';
                                timeoutInput.name = 'timeout';
                                timeoutInput.value = '1';
                                form.appendChild(timeoutInput);

                                const confirmBtn = document.getElementById('confirm-btn');
                                if (confirmBtn) {
                                    confirmBtn.click();
                                } else {
                                    form.submit();
                                }
                            }
                        <?php endif; ?>
                    }
                }, 1000);
            <?php endif; ?>
        <?php endif; ?>

        // 3. PERMANENTES POLLING FÜR SYNCHRONISATION
        <?php if ($waitingForReveal || ($showExplanation && !$isHost) || ($isHost && $hostPlays === 'no' && !$showExplanation)): ?>
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
            if (timerBox) { timerBox.classList.add('timer-box-revealed'); }
        <?php endif; ?>
    })();
    </script>

<?php endif; ?>
</body>
</html>