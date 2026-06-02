<?php
require_once 'init.php';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credits | damago Quizsystem</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body class="auth-page">

    <div class="page-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <?php include_once 'topbar.php'; ?>

    <main class="host-layout">
        <section class="host-card">
            <div class="auth-header">
                <span class="eyebrow">Credits</span>
                <h2>Über dieses Projekt</h2>
                <p>damago Quizsystem – eine webbasierte Lernanwendung.</p>
            </div>

            <p class="credits-text">
                damago.learn ist ein webbasiertes Quizsystem, mit dem sich Lernrunden
                erstellen, hosten und gemeinsam spielen lassen. Teilnehmende treten per
                Code einer Lobby bei, beantworten Fragen aus verschiedenen Fragenpools
                und sehen ihren Fortschritt im persönlichen Lernbereich. Das Projekt
                entstand im Rahmen eines Unterrichtsprojekts.
            </p>

            <div class="credits-people">
                <h3>Entwickelt von</h3>
                <ul>
                    <li>Pascal Arndt</li>
                    <li>Marcin Banaszkiewicz</li>
                    <li>Paul Schulte</li>
                </ul>
            </div>

            <div class="host-back">
                <a href="dashboard.php" class="back-button">← Zurück zum Dashboard</a>
            </div>
        </section>
    </main>

    <?php include_once 'footbar.php'; ?>

</body>
</html>
