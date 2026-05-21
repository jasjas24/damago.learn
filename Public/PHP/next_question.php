<?php
require_once 'init.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $currentIndex = $_SESSION['current_question_index'] ?? 0;
    $allQuestions = $_SESSION['quiz_questions'] ?? [];
    
    if (!isset($allQuestions[$currentIndex])) {
        header("Location: ../game.php");
        exit;
    }
    
    $currentQuestion = $allQuestions[$currentIndex];
    $answers = $currentQuestion['answers'];

    // Gewählte IDs des Users sammeln (leeres Array falls nichts gewählt oder Timeout)
    $chosenIds = isset($_POST['selected_answers']) ? array_map('intval', $_POST['selected_answers']) : [];
    
    // Ermittle alle korrekten Antwort-IDs aus der Datenbank für diese Frage
    $correctIds = [];
    foreach ($answers as $ans) {
        if (intval($ans['is_correct']) === 1) {
            $correctIds[] = intval($ans['id']);
        }
    }

    $pointsEarned = 0;
    $status = 'wrong';

    if (isset($_POST['timeout']) && $_POST['timeout'] === '1') {
        $status = 'timeout';
    } else {
        // Abgleich der Arrays für Multiple-Choice
        $correctChosen = array_intersect($chosenIds, $correctIds);
        $wrongChosen = array_diff($chosenIds, $correctIds);

        // Volle Punkte: Genau alle richtigen erwischt und keine falsche
        if (count($correctChosen) === count($correctIds) && count($wrongChosen) === 0) {
            $pointsEarned = 100;
            $status = 'correct';
        } 
        // Teilpunkte: Mindestens eine richtige, aber nicht alle, und KEINE falsche
        elseif (count($correctChosen) > 0 && count($correctChosen) < count($correctIds) && count($wrongChosen) === 0) {
            $pointsEarned = 50;
            $status = 'partial';
        }
    }

    // Punkte auf die Session aufrechnen
    $_SESSION['quiz_score'] = ($_SESSION['quiz_score'] ?? 0) + $pointsEarned;

    // Zustand für den Erklärungsmodus sichern
    $_SESSION['last_result'] = [
        'status' => $status,
        'chosen_ids' => $chosenIds,
        'points_earned' => $pointsEarned
    ];
    $_SESSION['show_explanation'] = true;

    header("Location: game.php");
    exit;
}