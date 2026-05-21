<?php
require_once 'init.php';
require_once 'db.php';

/** @var string $username */
/** @var string $role */
$setup = $_SESSION['quiz_setup'];

$pool  = $setup['pool'];
$count = $setup['count'];
$time  = $setup['time_limit'];
$mode  = $setup['point_mode'];
$host  = $setup['host_plays'];
$code  = $setup['code'];

if (!isset($_SESSION['quiz_questions'])) {
    try {
        // 1. SQL-Abfrage ausführen (Fragen + Antworten holen)
        $stmt = $pdo->prepare("
            SELECT 
                q.id AS question_id,
                q.question_text,
                q.explanation AS general_explanation,
                a.id AS answer_id,
                a.answer_text,
                a.is_correct,
                a.explanation AS answer_explanation,
                a.sort_order
            FROM questions q
            INNER JOIN question_pools p ON p.id = q.question_pool_id
            INNER JOIN answer_options a ON a.question_id = q.id
            WHERE p.name = ? AND q.is_active = 1
            ORDER BY q.id ASC, a.sort_order ASC
        ");
        $stmt->execute([$pool]);
        $rawResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. Daten für PHP strukturieren
        $questions = [];
        foreach ($rawResults as $row) {
            $qId = $row['question_id'];
            if (!isset($questions[$qId])) {
                $questions[$qId] = [
                    'id'            => $qId,
                    'question_text' => $row['question_text'],
                    'explanation'   => $row['general_explanation'],
                    'answers'       => []
                ];
            }
            $questions[$qId]['answers'][] = [
                'id'          => $row['answer_id'],
                'text'        => $row['answer_text'],
                'is_correct'  => $row['is_correct'],
                'explanation' => $row['answer_explanation'],
                'sort_order'  => $row['sort_order']
            ];
        }
        $questions = array_values($questions);

        // 3. Mischen und auf gewünschte Anzahl kürzen
        shuffle($questions);
        $quizQuestions = array_slice($questions, 0, $count);

        // 4. Antworten innerhalb der Fragen mischen
        foreach ($quizQuestions as &$singleQuestion) {
            shuffle($singleQuestion['answers']);
        }
        unset($singleQuestion);

        // 5. JETZT fest für game.php in der Session verankern!
        $_SESSION['quiz_questions'] = $quizQuestions;
        
        // 6. Frage-Index für den Start auf 0 setzen
        $_SESSION['current_question_index'] = 0;

        if (!isset($_SESSION['quiz_score'])) {
        $_SESSION['quiz_score'] = 0;
}

    } catch (PDOException $e) {
        die("Fehler beim Vorbereiten der Fragen: " . $e->getMessage());
    }
}

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Host-Lobby | damago Quizsystem</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body class="auth-page">

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

    <main class="auth-layout">
        <section class="auth-info">
            <h1>Host-Lobby.</h1>
            <p>
                Teile diesen Code mit den Teilnehmern.
                Sie müssen ihn eingeben, um der Lobby beizutreten.
            </p>

            <div class="info-list">
                <div>Teilnahme-Code: <?php echo htmlspecialchars($code); ?></div>
            </div>
        </section>

        <section class="auth-card">
            <div class="auth-header">
                <span class="eyebrow">Lobby bereit</span>
                <h2>Teilnehmer warten</h2>
                <p>Starte das Quiz, sobald alle Teilnehmer beigetreten sind.</p>
            </div>

            <button class="btn btn-primary" onclick="window.location.href='game.php'">
                Quiz starten
            </button>
            <div class="auth-links">
                <p>Zurück?</p>
                <a href="dashboard.php">Zurück zum Dashboard</a>
            </div>
        </section>
    </main>

</body>
</html>