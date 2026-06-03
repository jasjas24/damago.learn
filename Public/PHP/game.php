<?php
require_once 'init.php';
require_once 'db.php';
require_once 'helpers.php';

/** @var string $username */

// 1. Lobby-ID und Host-Status ermitteln
$isHost = isset($_SESSION['quiz_setup']['lobby_id']);
$lobby_id = $_SESSION['quiz_setup']['lobby_id'] ?? $_SESSION['player_lobby_id'] ?? null;
$hostToken = $_SESSION['quiz_setup']['host_token'] ?? ''; // für Host-Aktionen (LH 27.5)

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
                    SELECT q.id AS question_id, q.question_text, q.explanation AS general_explanation,
                           m.file_name AS image_file
                    FROM lobby_questions lq
                    INNER JOIN questions q ON q.id = lq.question_id
                    LEFT JOIN media_files m ON m.id = q.image_id
                    WHERE lq.lobby_id = ?
                    ORDER BY lq.sort_order ASC
                ");
                $stmtLobbyQ->execute([$lobby_id]);
                $questionsRaw = $stmtLobbyQ->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($questionsRaw)) {
                    $quizQuestions = [];
                    $stmtAnswers = $pdo->prepare("SELECT id, answer_text AS text, is_correct, explanation FROM answer_options WHERE question_id = ? ORDER BY sort_order ASC");

                    foreach ($questionsRaw as $q) {
                        $stmtAnswers->execute([$q['question_id']]);
                        // Kein Mischen pro Client (LH 11.4: gleiche Reihenfolge für alle).
                        // Feste Reihenfolge über sort_order; die gemischte Reihenfolge kommt aus quiz_data (Variante A).
                        $answers = $stmtAnswers->fetchAll(PDO::FETCH_ASSOC);

                        $quizQuestions[] = [
                            'id'            => $q['question_id'],
                            'question_text' => $q['question_text'],
                            'explanation'   => $q['general_explanation'],
                            'image'         => $q['image_file'] ?? null,
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
$questionStartedAt = null; // Server-Startzeitpunkt der aktuellen Frage
$elapsedSeconds = 0;       // serverseitig verstrichene Sekunden

if ($lobby_id) {
    try {
        $stmtStatus = $pdo->prepare("SELECT show_explanation, current_question_index, point_mode, time_limit, current_question_started_at, TIMESTAMPDIFF(SECOND, current_question_started_at, NOW()) AS elapsed FROM quiz_lobbies WHERE id = ?");
        $stmtStatus->execute([$lobby_id]);
        $lobbyStatus = $stmtStatus->fetch(PDO::FETCH_ASSOC);
        
        if ($lobbyStatus) {
            $_SESSION['show_explanation'] = (int)$lobbyStatus['show_explanation'] === 1;
            $_SESSION['current_question_index'] = (int)$lobbyStatus['current_question_index'];
            
            // Point-Mode synchronisieren
            $_SESSION['quiz_setup']['point_mode'] = $lobbyStatus['point_mode'] ? $lobbyStatus['point_mode'] : 'all_or_nothing';
            
            // Zeitlimit live aus der DB holen
            if (!empty($lobbyStatus['time_limit'])) {
                $timeLimit = (int)$lobbyStatus['time_limit'];
                $_SESSION['quiz_setup']['time_limit'] = $timeLimit;
            }

            // Server-Startzeitpunkt der aktuellen Frage merken (maßgebliche Zeitquelle)
            if (!empty($lobbyStatus['current_question_started_at'])) {
                $questionStartedAt = $lobbyStatus['current_question_started_at'];
                $elapsedSeconds = (int)$lobbyStatus['elapsed'];
            }

            if ($_SESSION['show_explanation']) {
                unset($_SESSION['waiting_for_reveal']);
            }
        }
    } catch (PDOException $e) {}
}

// Fallback für den Point-Mode, falls DB-Abfrage fehlschlug
$pointMode = $_SESSION['quiz_setup']['point_mode'] ?? 'all_or_nothing';

// Host Plays Status abfragen
$hostPlays = $_SESSION['quiz_setup']['host_plays'] ?? 'yes';

// Falls der Host die Variable noch in der Session hat, nutzen wir die als primäre Quelle
if (isset($_SESSION['quiz_setup']['time_limit'])) {
    $timeLimit = (int)$_SESSION['quiz_setup']['time_limit'];
}

// Anzeige-Startwert des Timers = serverseitig verbleibende Zeit.
// So zeigen auch spät ladende / neu ladende Clients dieselbe Restzeit (Server ist maßgeblich).
$remainingTime = ($questionStartedAt !== null) ? max(0, $timeLimit - $elapsedSeconds) : $timeLimit;

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
            SELECT player_name AS username, points AS score, avatar
            FROM lobby_players
            WHERE lobby_id = ?
        ");
        $stmtRank->execute([$lobby_id]);
        $rawPlayers = $stmtRank->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rawPlayers as $player) {
            $score = (int)$player['score'];
            
            // Wenn auf den Reveal gewartet wird, die vorab berechneten Punkte abziehen
            if ($player['username'] === $currentDisplayName && $waitingForReveal && !$showExplanation && isset($lastResult['points_earned'])) {
                $score -= (int)$lastResult['points_earned'];
            }
            
            $rankingPlayers[] = [
                'username' => $player['username'],
                'score' => $score,
                'avatar' => $player['avatar'] ?? null
            ];
        }

        // Sortierung nach Punkten (absteigend)
        usort($rankingPlayers, function($a, $b) {
            if ($a['score'] === $b['score']) {
                return strcasecmp($a['username'], $b['username']);
            }
            return ($a['score'] < $b['score']) ? 1 : -1;
        });

    } catch (PDOException $e) {
        $rankingPlayers = [[ 'username' => $currentDisplayName, 'score' => $_SESSION['quiz_score'] ?? 0 ]];
    }
}

if (empty($rankingPlayers)) {
    $rankingPlayers = [[ 'username' => $currentDisplayName, 'score' => $_SESSION['quiz_score'] ?? 0 ]];
}

// Antwort-Zähler für die aktuelle Frage (Host-/Beameransicht, LH 9.5): Anfangswert.
// Live-Aktualisierung erfolgt per Polling über check_next_question.php.
$answeredCount = 0;
$totalCount = 0;
if ($lobby_id && !empty($currentQuestion['id'])) {
    try {
        $stmtTotalP = $pdo->prepare("SELECT COUNT(*) FROM lobby_players WHERE lobby_id = ?");
        $stmtTotalP->execute([$lobby_id]);
        $totalCount = (int)$stmtTotalP->fetchColumn();

        $stmtAnsP = $pdo->prepare("SELECT COUNT(*) FROM player_answers WHERE lobby_id = ? AND question_id = ?");
        $stmtAnsP->execute([$lobby_id, $currentQuestion['id']]);
        $answeredCount = (int)$stmtAnsP->fetchColumn();
    } catch (PDOException $e) {}
}
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
<body class="quiz-play-page game-live">

<?php include_once 'topbar.php'; ?>

<?php if ($retryLoading): ?>
    <main class="play-layout play-layout-loading">
        <section class="question-card question-card-loading">
            <div class="loader loader-centered"></div>
            <h2 class="loading-title">Das Spiel startet gleich...</h2>
            <p class="loading-text">Die Fragen werden im Hintergrund vom Server vorbereitet. Bitte warten...</p>
        </section>
    </main>
    <script>
        setTimeout(function() { window.location.reload(); }, 1500);
    </script>
<?php else: ?>

    <main class="play-layout">
        <section class="quiz-main">
            <aside class="quiz-side">
                <div class="quiz-timer-stack">
                    <div class="timer-box">
                        <strong><span id="timer-display"><?php echo $remainingTime; ?></span></strong>
                    </div>
                    <?php if ($isHost): ?>
                        <div class="answer-count-box" id="answer-count-box" title="Teilnehmer, die geantwortet haben">
                            <strong><span id="answered-count"><?php echo $answeredCount; ?></span>/<span id="total-count"><?php echo $totalCount; ?></span></strong>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if ($isHost): ?>
                    <form action="abort_game.php" method="POST" class="abort-form" onsubmit="return confirm('Willst du das Spiel wirklich abbrechen? Alle Teilnehmer werden entfernt.');">
                        <input type="hidden" name="host_token" value="<?php echo htmlspecialchars($hostToken); ?>">
                        <button type="submit" class="btn btn-abort">Spiel abbrechen</button>
                    </form>
                <?php endif; ?>
            </aside>

            <div class="quiz-content">
            <div class="quiz-topline">
                <div>
                    <span class="eyebrow">Frage <?php echo $currentIndex + 1; ?> von <?php echo $totalQuestions; ?></span>
                </div>
            </div>

            <section class="question-card">
                <div class="question-title"><?php echo render_rich_text($currentQuestion['question_text']); ?></div>
                <?php if (!empty($currentQuestion['image'])): ?>
                    <img src="../Uploads/Questions/<?php echo rawurlencode($currentQuestion['image']); ?>"
                         alt="Bild zur Frage" class="question-image">
                <?php endif; ?>
            </section>

            <?php if ($waitingForReveal && !$showExplanation): ?>
                <div class="confirm-button-wrapper confirm-button-wrapper-waiting">
                    <div class="loader"></div>
                    <button type="button" class="millionaire-answer confirm-button submitted" disabled>
                        <span class="answer-text">Antwort abgegeben. Warte auf Mitspieler...</span>
                    </button>
                </div>
            <?php elseif ($isHost && $hostPlays === 'no' && !$showExplanation): ?>
                <!-- Moderatoransicht: Antwortmöglichkeiten nur zur Anzeige (Beamer), ohne Auswahl. -->
                <div class="millionaire-answers">
                    <?php foreach ($answers as $ans): ?>
                        <button type="button" class="millionaire-answer" disabled>
                            <span class="answer-text"><?php echo render_inline_text($ans['text']); ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>

                <div class="confirm-button-wrapper confirm-button-wrapper-waiting">
                    <div class="loader"></div>
                    <button type="button" class="millionaire-answer confirm-button submitted" disabled>
                        <span class="answer-text">Du bist der Moderator. Warte auf die Antworten der Spieler...</span>
                    </button>
                </div>
                <!-- Der Moderator kann die Auflösung jederzeit selbst freischalten und muss nicht
                     auf alle Spieler oder den Timer warten (LH 9.4/9.5). -->
                <div class="confirm-button-wrapper">
                    <form action="host_reveal.php" method="POST" class="reveal-now-form">
                        <input type="hidden" name="host_token" value="<?php echo htmlspecialchars($hostToken); ?>">
                        <button type="submit" class="millionaire-answer confirm-button btn-blue">
                            <span class="answer-text">Auflösung anzeigen</span>
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <form class="millionaire-answers" id="quiz-form" action="next_question.php" method="POST">
                    <?php foreach ($answers as $letter => $ans):
                        $revealClass = "";
                        if ($showExplanation) {
                            $isCorrect = intval($ans['is_correct']) === 1;
                            $wasSelected = $lastResult && isset($lastResult['chosen_ids']) && is_array($lastResult['chosen_ids']) && in_array($ans['id'], $lastResult['chosen_ids']);

                            if ($isCorrect) { $revealClass = "answer-reveal-correct"; } 
                            else { $revealClass = "answer-reveal-wrong"; }
                            
                            if ($wasSelected) { $revealClass .= " answer-reveal-chosen"; }
                        }
                    ?>
                        <button type="button"
                                class="millionaire-answer <?php echo $revealClass; ?>"
                                data-id="<?php echo $ans['id']; ?>"
                                <?php echo $showExplanation ? 'disabled' : ''; ?>>
                            <span class="answer-text"><?php echo render_inline_text($ans['text']); ?></span>
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
                        <?php $isLastQuestion = ($totalQuestions > 0 && $currentIndex >= $totalQuestions - 1); ?>
                        <form action="go_next.php" method="POST" class="next-question-form">
                            <input type="hidden" name="host_token" value="<?php echo htmlspecialchars($hostToken); ?>">
                            <button type="submit" class="millionaire-answer confirm-button <?php echo $isLastQuestion ? 'btn-show-results' : 'btn-green'; ?>">
                                <span class="answer-text"><?php echo $isLastQuestion ? 'Endergebnis anzeigen' : 'Nächste Frage (Host)'; ?></span>
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
                                    echo "<span class='reveal-result-wrong'>Leider falsch! (0 Punkte)</span>";
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
                                    $exClass = $exCorrect ? "reveal-exp-correct" : "reveal-exp-wrong";
                                    if ($exSelected) { $exClass .= $exCorrect ? " reveal-exp-chosen-correct" : " reveal-exp-chosen-wrong"; }
                                ?>
                                <li class="reveal-exp-item <?php echo $exClass; ?>">
                                    <strong class="reveal-exp-strong"><?php echo render_inline_text($ans['text']); ?>:</strong>
                                    <div class="reveal-exp-text"><?php echo render_rich_text($ans['explanation']); ?></div>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endif; ?>
            </div><!-- /.quiz-content -->
        </section>

        <aside class="ranking-panel">
            <div class="ranking-header">
                <span class="eyebrow">Live-Ranking</span>
                <h2>Punktestand</h2>
            </div>
            <div class="ranking-list ranking-list-top-gap">
                <table class="ranking-table">
                    <thead>
                        <tr><th>Pl.</th><th>Name</th><th class="score-head">Punkte</th></tr>
                    </thead>
                    <tbody>
                        <?php 
                        $actualRank = 1; $lastScore = null;
                        foreach ($rankingPlayers as $index => $player): 
                            if ($lastScore !== null && $player['score'] < $lastScore) { $actualRank = $index + 1; }
                        ?>
                            <tr<?php echo ($player['username'] === $currentDisplayName) ? ' class="current-player"' : ''; ?>>
                                <td><?php echo $actualRank; ?>.</td>
                                <td class="ranking-name-cell">
                                    <?php if (!empty($player['avatar'])): ?>
                                        <img src="../Uploads/Avatare/<?php echo rawurlencode($player['avatar']); ?>" alt="" class="ranking-avatar">
                                    <?php endif; ?>
                                    
                                    <?php echo htmlspecialchars($player['username']); ?>
                                    
                                    <?php if ($player['username'] === $currentDisplayName) echo '<span class="you-badge">(Du)</span>'; ?>

                                    <?php if ($isHost && $player['username'] !== $currentDisplayName): ?>
                                        <button type="button" class="kick-btn" data-username="<?php echo htmlspecialchars($player['username']); ?>">
                                            Kick
                                        </button>
                                    <?php endif; ?>
                                </td>
                                <td class="score-cell"><?php echo $player['score']; ?></td>
                            </tr>
                        <?php $lastScore = $player['score']; endforeach; ?>
                    </tbody>
                </table>
            </div>
        </aside>
    </main>

    <script src="/damago/Public/JS/functions.js?v=<?php echo time(); ?>"></script>

    <script src="../JS/vendor/highlight.min.js"></script>
    <script>
        if (window.hljs) { hljs.highlightAll(); }
    </script>

    <script>
        // Kick-Logik
        document.querySelectorAll('.kick-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const username = this.getAttribute('data-username');
                if (confirm('Willst du ' + username + ' wirklich aus dem Spiel werfen?')) {
                    fetch('kick_player.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'username=' + encodeURIComponent(username) + '&host_token=' + encodeURIComponent(<?php echo json_encode($hostToken); ?>)
                    }).then(() => {
                        // Zeile ausblenden
                        this.closest('tr').remove();
                    });
                }
            });
        });
    (function() {
        // 1. ANTWORTEN AUSWÄHLEN
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

        // 2. TIMEOUT LOGIK (GEÄNDERT: LÖST NUN DIREKT DEN CONFIRM-KLICK AUS)
        <?php if (!$showExplanation): ?>
            // Startwert = serverseitig verbleibende Zeit (Server ist maßgeblich, LH 13.4 / 26.3).
            // Der JS-Countdown dient nur der Anzeige und dem automatischen Absenden der aktuellen Auswahl.
            let timeLeft = <?php echo intval($remainingTime); ?>;
            const display = document.getElementById('timer-display');
            const timerBox = document.querySelector('.timer-box');

            <?php if (!$waitingForReveal || ($isHost && $hostPlays === 'no')): ?>
                const countdown = setInterval(function() {
                    timeLeft--;
                    if (display) display.textContent = timeLeft;

                    // Nur optisch (keine Logik): ab 10s pulsieren, ab 5s rot + schneller pulsieren
                    if (timerBox) {
                        if (timeLeft <= 5) {
                            timerBox.classList.remove('pulse');
                            timerBox.classList.add('danger');
                        } else if (timeLeft <= 10) {
                            timerBox.classList.add('pulse');
                        }
                    }

                    if (timeLeft <= 0) {
                        clearInterval(countdown);
                        
                        <?php if (!($isHost && $hostPlays === 'no')): ?>
                            console.log("Zeit abgelaufen. Sende ausgewählte Antworten zur Auswertung...");
                            const form = document.getElementById('quiz-form');
                            const confirmBtn = document.getElementById('confirm-btn');
                            
                            if (confirmBtn) {
                                // Simuliert exakt den echten Klick des Spielers inklusive Animation
                                confirmBtn.click();
                            } else if (form) {
                                form.submit();
                            }
                        <?php endif; ?>
                    }
                }, 1000);
            <?php endif; ?>
        <?php endif; ?>

        // 3. PERMANENTES POLLING FÜR SYNCHRONISATION
        //    Spieler pollen in JEDER Phase (auch während sie noch an der Antwort sitzen),
        //    damit ein Kick oder Spielabbruch SOFORT erkannt wird und nicht erst, wenn der
        //    Timer abläuft. Der moderierende Host pollt während der laufenden Frage.
        <?php if (!$isHost || ($isHost && $hostPlays === 'no' && !$showExplanation)): ?>
            const checkInterval = setInterval(function() {
                fetch('check_next_question.php')
                    .then(response => response.json())
                    .then(data => {
                        // Prüfung auf Kick
                        if (data.kicked === true || data.action === 'kicked') {
    clearInterval(checkInterval);
    alert("Du wurdest aus dem Spiel entfernt.");
    // Wir senden den Spieler zu einem Skript, das die Session löscht
    window.location.href = 'logout.php';
    return;
}

                        // Host hat das Spiel abgebrochen, also hinausleiten.
                        if (data.aborted === true) {
                            clearInterval(checkInterval);
                            alert("Der Host hat das Spiel abgebrochen.");
                            window.location.href = 'dashboard.php';
                            return;
                        }

                        // Antwort-Zähler live aktualisieren (LH 9.5)
                        const answeredEl = document.getElementById('answered-count');
                        if (answeredEl && typeof data.answered !== 'undefined') {
                            answeredEl.textContent = data.answered;
                            const totalEl = document.getElementById('total-count');
                            if (totalEl && typeof data.total !== 'undefined') {
                                totalEl.textContent = data.total;
                            }
                        }

                        // BESTEHENDER CODE:
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
            const revealTimerBox = document.querySelector('.timer-box');
            if (revealTimerBox) { revealTimerBox.classList.add('timer-box-revealed'); }
        <?php endif; ?>
    })();
    </script>

<?php endif; ?>
    <?php include_once 'footbar.php'; ?>

</body>
</html>