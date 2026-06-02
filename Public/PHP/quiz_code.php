<?php
require_once 'init.php';
/** @var string $username */

/*
    Legacy-Seite: Teilnahme-Code eingeben (Testversion ohne DB-Validierung).
    Für den produktiven Einsatz: join_quiz.php verwenden.
*/
$hostCode = "A7K9";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $enteredCode = strtoupper(trim($_POST["join_code"] ?? ""));

    if (empty($enteredCode)) {
        $error = "Bitte gib einen Teilnahme-Code ein.";
    } elseif ($enteredCode === $hostCode) {
        $_SESSION["join_code"] = $enteredCode;
        header("Location: ../lobby.html");
        exit;
    } else {
        $error = "Der eingegebene Code ist falsch. Bitte prüfe ihn und versuche es erneut.";
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz beitreten | damago Quizsystem</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body class="auth-page">

    <div class="page-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <?php include_once 'topbar.php'; ?>

    <main class="quiz-code-layout">
        <div class="quiz-code-info">
            <h1>Teilnahme-Code eingeben.</h1>
            <p>
                Gib den Code ein, den du vom Host erhalten hast.
                Danach wirst du automatisch in die Lobby der Quizrunde weitergeleitet.
            </p>
            <div class="quiz-code-hint">
                Der Teilnahme-Code wird vom Host generiert
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form class="auth-form" action="quiz_code.php" method="post">
            <div class="form-group">
                <label for="join_code">Teilnahme-Code</label>
                <input
                    type="text"
                    id="join_code"
                    name="join_code"
                    placeholder="z. B. A7K9"
                    maxlength="6"
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary">
                Lobby betreten
            </button>
        </form>

        <div class="quiz-code-back">
            <p>Du möchtest zurück?</p>
            <a href="dashboard.php">Zurück zum Dashboard</a>
        </div>
    </main>

    <?php include_once 'footbar.php'; ?>

</body>
</html>
