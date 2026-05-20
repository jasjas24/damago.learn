<?php
require_once 'init.php';
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 1. Felder abgreifen und in normalen Variablen speichern
    // Der ?? Operator setzt einen sicheren Standardwert, falls was schiefgeht
    $questionPool  = $_POST['question_pool'] ?? '';
    $questionCount = intval($_POST['question_count'] ?? 10); // in Ganzzahl umwandeln
    $timeLimit     = intval($_POST['time_limit'] ?? 30);     // in Ganzzahl umwandeln
    $pointMode     = $_POST['point_mode'] ?? 'partial';
    $hostPlays     = $_POST['host_plays'] ?? 'no';
    $joinCode      = trim($_POST['join_code'] ?? '');

    // 2. Werte in die Session schreiben, damit sie auf den nächsten Seiten leben
    $_SESSION['quiz_setup'] = [
        'code'        => $joinCode,
        'pool'        => $questionPool,
        'count'       => $questionCount,
        'time_limit'  => $timeLimit,
        'point_mode'  => $pointMode,
        'host_plays'  => $hostPlays
    ];

    // Optional: Falls du auf der Formularseite auch deinen JS-Quizcode mitgeschickt hast:
    if (isset($_POST['join_code'])) {
        $_SESSION['quiz_setup']['code'] = trim($_POST['join_code']);
    }

        // $stmt = $pdo->prepare("
        //     SELECT 
        //         q.id AS question_id,
        //         q.question_text,
        //         q.explanation AS general_explanation, 
        //         a.id AS answer_id,
        //         a.answer_text,
        //         a.is_correct,
        //         a.explanation AS answer_explanation,  
        //         a.sort_order
        //     FROM questions q
        //     INNER JOIN question_pools p ON p.id = q.question_pool_id
        //     INNER JOIN answer_options a ON a.question_id = q.id
        //     WHERE p.name = ? AND q.is_active = 1
        //     ORDER BY q.id ASC, a.sort_order ASC
        // ");

        // $stmt->execute(array($_SESSION["quiz_setup"]["pool"]));
        // $rawResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // $questions = [];

        // foreach ($rawResults as $row) {
        //     $qId = $row['question_id'];
            
        //     // Wenn die Frage noch nicht im Array existiert, lege sie neu an
        //     if (!isset($questions[$qId])) {
        //         $questions[$qId] = [
        //             'id'            => $qId,
        //             'question_text' => $row['question_text'],
        //             'explanation'   => $row['general_explanation'],
        //             'answers'       => [] // Hier sammeln wir gleich die 4 Antworten
        //         ];
        //     }
            
        //     // Füge die aktuelle Antwort der jeweiligen Frage hinzu
        //     $questions[$qId]['answers'][] = [
        //         'id'          => $row['answer_id'],
        //         'text'        => $row['answer_text'],
        //         'is_correct'  => $row['is_correct'],
        //         'explanation' => $row['answer_explanation'],
        //         'sort_order'  => $row['sort_order']
        //     ];
        // }

        // // Gewünschte Anzahl aus dem Setup holen (z. B. 10)
        // $maxQuestions = $_SESSION["quiz_setup"]["count"] ?? 10;

        // // 1. Die Fragen zufällig durchmischen
        // shuffle($questions);

        // // 2. Nur die gewünschte Anzahl an Fragen behalten
        // $quizQuestions = array_slice($questions, 0, $maxQuestions);

    
    // 4. Weiterleitung zur Lobby-Ansicht
    header("Location: host_lobby.php");
    exit;

} else {
    // Falls jemand die Datei direkt aufruft, zurück zum Dashboard
    header("Location: dashboard.php");
    exit;
}