<?php
session_start();

$username = $_SESSION["username"] ?? "Gast";
$role = $_SESSION["role"] ?? "gast";

/*
    Test-Code für das Frontend.
    Später wird dieser Code aus der Datenbank geladen.
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

    <main class="quiz-code-layout">
        <section class="quiz-code-info">

            <h1>Teilnahme-Code eingeben.</h1>

            <p>
                Gib den Code ein, den du vom Host erhalten hast.
                Erst danach kannst du der passenden Quiz-Lobby beitreten.
            </p>

            <div class="quiz-code-hint">
                Der Teilnahme-Code wird vom Host generiert
            </div>
        </section>

            <?php if (!empty($error)): ?>
                <div class="message error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
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
        </section>
    </main>

</body>
</html>