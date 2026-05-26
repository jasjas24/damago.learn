<?php
session_start();

// Zugriffsschutz: nur registrierte Nutzer
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit;
}

// Session-Infos
$username = isset($_SESSION['username']) ? $_SESSION['username'] : "test";
$bereich = isset($_SESSION['bereich']) ? $_SESSION['bereich'] : ""; // IT oder Pflege

// Platzhalter-Daten (werden später aus DB geladen)
$statistics = [
    [
        "thema" => "IT",
        "bereich" => "IT",
        "richtige_fragen" => 8,
        "endpunktzahl" => 80,
        "platz" => 2,
        "teilnehmer" => 15,
        "datum" => "2026-05-22 10:30"
    ],
    [
        "thema" => "Pflege",
        "bereich" => "Pflege",
        "richtige_fragen" => 10,
        "endpunktzahl" => 100,
        "platz" => 1,
        "teilnehmer" => 12,
        "datum" => "2026-05-21 14:15"
    ]
];

// Filter auf den Bereich des Users
$statistics = array_filter($statistics, function($stat) use ($bereich) {
    return $stat['bereich'] === $bereich;
});
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lernstatistik</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body class="auth-page">

    <div class="page-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <?php include_once 'topbar.php'; ?>

    <main class="container">
        <h1>Lernfortschritt</h1>
        <p>Hier siehst du deine persönliche Historie aller Spiele und Quiz.</p>

        <!-- Zurück-Button -->
        <div class="back-button-wrapper">
            <a href="dashboard.php" class="back-button">← Zurück zum Dashboard</a>
        </div>

        <div class="statistics-card">
            <?php foreach($statistics as $stat): ?>
            <div class="stat-row">
                <div class="stat-title"><?php echo htmlspecialchars($stat['thema']); ?></div>
                <div class="stat-values">
                    <span>Richtig: <?php echo $stat['richtige_fragen']; ?></span>
                    <span>Punkte: <?php echo $stat['endpunktzahl']; ?></span>
                    <span>Platz: <?php echo $stat['platz']; ?></span>
                    <span>Teilnehmer: <?php echo $stat['teilnehmer']; ?></span>
                    <span><?php echo $stat['datum']; ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>
