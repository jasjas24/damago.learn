<?php
require_once 'init.php';
// Löscht gezielt nur die Quiz-Daten, damit der User eingeloggt bleibt
unset($_SESSION['quiz_questions']);
unset($_SESSION['current_question_index']);
unset($_SESSION['quiz_setup']);
unset($_SESSION['player_lobby_id']);
unset($_SESSION['show_explanation']);
unset($_SESSION['waiting_for_reveal']);
unset($_SESSION['last_result']);

header("Location: dashboard.php");
exit;