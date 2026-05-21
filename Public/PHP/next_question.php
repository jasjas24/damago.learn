<?php
require_once 'init.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Fall A: Die Zeit ist abgelaufen
    if (isset($_POST['timeout']) && $_POST['timeout'] === '1') {
        // Logik für "Zeit abgelaufen" (0 Punkte, direkt zur Erklärung oder nächsten Frage)
        $_SESSION['last_answer_status'] = 'timeout';
        
    } 
    // Fall B: Der User hat rechtzeitig einen der 4 Buttons geklickt
    unset($_POST['timeout']);
    if (isset($_POST['selected_answer'])) {
        $chosenAnswerId = intval($_POST['selected_answer']);
        
        // HIER KOMMT DIE AUSWERTUNG:
        // Wir prüfen gleich, ob $chosenAnswerId in der DB 'is_correct = 1' hat.
        // Wenn ja -> Punkte erhöhen!
        $_SESSION['last_answer_status'] = 'answered'; // temporär
    }

    // Weiter zur nächsten Frage (oder Zwischenseite für die Erklärung)
    $_SESSION['current_question_index']++;
    header("Location: game.php");
    exit;
}