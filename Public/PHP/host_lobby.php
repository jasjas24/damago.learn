<?php
session_start();

$username = $_SESSION["username"] ?? "Gast";
$joinCode = $_SESSION["current_game_code"] ?? "A7K9";
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
                <div>Teilnahme-Code: <?php echo htmlspecialchars($joinCode); ?></div>
            </div>
        </section>

        <section class="auth-card">
            <div class="auth-header">
                <span class="eyebrow">Lobby bereit</span>
                <h2>Teilnehmer warten</h2>
                <p>Starte das Quiz, sobald alle Teilnehmer beigetreten sind.</p>
            </div>

            <button class="btn btn-primary">
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