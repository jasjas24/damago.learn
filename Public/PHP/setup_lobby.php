<?php
session_start();

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

    // 3. Weiterleitung zur Lobby-Ansicht
    header("Location: host_lobby.php");
    exit;

} else {
    // Falls jemand die Datei direkt aufruft, zurück zum Dashboard
    header("Location: dashboard.php");
    exit;
}