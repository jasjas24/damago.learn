<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz-Ergebnis | damago Quizsystem</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>

    <header class="topbar">
        <a href="dashboard.php" class="topbar-brand">
            <img src="../damago-logo.png" alt="damago Logo" class="topbar-logo">
        </a>

        <div class="topbar-account">
            <span class="account-name">Gast</span>
            <a href="logout.php" class="logout-button">logout</a>
        </div>
    </header>

    <main class="container">

        <div class="back-button-wrapper">
            <a href="dashboard.php" class="back-button">Zurück zum Dashboard</a>
        </div>

        <h1>Quiz-Ergebnis</h1>

        <section class="statistics-card">

            <div class="stat-row">
                <div class="stat-title">Teilnehmer anzahl</div>
                <div class="stat-values">
                    <span>4 Spieler</span>
                </div>
            </div>

            <div class="stat-row">
                <div class="stat-title">Höchste Punktzahl</div>
                <div class="stat-values">
                    <span>5000 Punkte</span>
                </div>
            </div>

            <div class="stat-row">
                <div class="stat-title">Dein Ergebnis</div>
                <div class="stat-values">
                    <span>Platz 1</span>
                    <span>5000 Punkte</span>
                    <span>Ergebnis wurde gespeichert</span>
                </div>
            </div>

        </section>
                <br>

        <section class="statistics-card">

            <div class="stat-row">
                <div class="stat-title">Ranking</div>

                <table class="ranking-table">
                    <thead>
                        <tr>
                            <th>Pl.</th>
                            <th>Name</th>
                             <th class="score-cell">Punkte</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="current-player">
                            <td>1.</td>
                            <td>
                                Kevin
                                <span class="you-badge">(Du)</span>
                            </td>
                            <td class="score-cell">5000</td>
                        </tr>

                        <tr>
                            <td>1.</td>
                            <td>Lisa</td>
                            <td class="score-cell">5000</td>
                        </tr>

                        <tr>
                            <td>3.</td>
                            <td>Lars</td>
                            <td class="score-cell">4800</td>
                        </tr>

                        <tr>
                            <td>4.</td>
                            <td>Gast_48291</td>
                            <td class="score-cell">3200</td>
                        </tr>
                    </tbody>
                </table>

            </div>

        </section>
                <br>

        <div class="back-button-wrapper">
            <a href="setup_lobby.php" class="back-button">Neues Quiz starten</a>
        </div>

    </main>

</body>
</html>