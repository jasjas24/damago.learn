<?php
require_once 'init.php';
// Zeigt die Credits-Seite mit kurzer Projektbeschreibung und den Namen der Entwickler.
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Das Projekt | damago.learn</title>
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
                <span class="eyebrow">Das Projekt</span>
                <h2>Über dieses Projekt</h2>
                <p>damago.learn | eine webbasierte Lernanwendung.</p>
            </div>

            <p class="credits-text">
                damago.learn ist ein webbasiertes Quizsystem, mit dem sich Lernrunden
                erstellen, hosten und gemeinsam spielen lassen. Teilnehmende treten per
                Code einer Lobby bei, beantworten Fragen aus verschiedenen Fragenpools
                und sehen ihren Fortschritt im persönlichen Lernbereich.

                Das Projekt entstand im Rahmen eines Unterrichtsprojekts der damago und
                richtet sich an alle Umschüler des Ausbildungsbetriebs. Es schafft eine
                gemeinsame Plattform, auf der Wissen spielerisch vertieft werden kann,
                direkt im Unterricht, kollaborativ und praxisnah.

                Ein besonderer Dank gilt den Verantwortlichen der damago für die Möglichkeit,
                ein Projekt dieser Art eigenständig konzipieren und umsetzen zu dürfen.
            </p>

            <div class="credits-people">
                <h3>Entwickelt von den Anwendungsentwicklern des Kurses FI24</h3>
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
