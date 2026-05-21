<?php
require_once 'init.php';

unset($_SESSION['show_explanation']);
unset($_SESSION['last_result']);

$_SESSION['current_question_index']++;

header("Location: game.php");
exit;