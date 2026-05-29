<?php
require_once 'init.php';
?>



<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archiv | damago Quizsystem</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>

    <header class="topbar">
        <a href="dashboard.php" class="topbar-brand">
            <img src="../damago-logo.png" alt="damago Logo" class="topbar-logo">
        </a>

        <div class="topbar-account">
            <span class="account-name"><?php echo htmlspecialchars($username ?? 'Gast', ENT_QUOTES, 'UTF-8'); ?></span>
            <a href="logout.php" class="logout-button">logout</a>
        </div>
    </header>

    <main class="container">

        <div class="back-button-wrapper">
            <a href="dashboard.php" class="back-button">Zurück zum Dashboard</a>
        </div>

        <h1>Archiv</h1>
        <p>
            Übersicht abgeschlossener Quizrunden. Über die Filter kann später gezielt nach bestimmten Spielen gesucht werden.
        </p>

        <section class="statistics-card">

            <div class="stat-row">
                <div class="stat-title">Archiv filtern</div>

                <form class="auth-form" action="archive.php" method="GET">

                    <div class="form-group">
                        <label for="question_pool">Fragenpool</label>
                        <select id="question_pool" name="question_pool">
                            <option value="">Alle Fragenpools</option>
                            <option value="php_grundlagen">PHP Grundlagen</option>
                            <option value="datenbanken">Datenbanken</option>
                            <option value="html_css">HTML / CSS</option>
                            <option value="javascript">JavaScript</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="date_from">Datum von</label>
                        <input type="date" id="date_from" name="date_from">
                    </div>

                    <div class="form-group">
                        <label for="date_to">Datum bis</label>
                        <input type="date" id="date_to" name="date_to">
                    </div>

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="z. B. Kevin oder Gast_48291">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Archiv durchsuchen
                    </button>

                    <a href="archive.php" class="back-button">
                        Filter zurücksetzen
                    </a>

                </form>
            </div>

        </section>

        <br>

        <section class="statistics-card">

            <div class="stat-row">
                <div class="stat-title">Archiv-Übersicht</div>

                <table class="ranking-table">
                    <thead>
                        <tr>
                            <th>Datum / Uhrzeit</th>
                            <th>Fragenpool</th>
                            <th>Teilnehmer</th>
                            <th class="score-cell">Richtig beantwortet</th>
                            <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td>28.05.2026 - 14:30 Uhr</td>
                            <td>PHP Grundlagen</td>
                            <td>4</td>
                            <td class="score-cell">87%</td>
                            <td>
                                <a href="archive_detail.php?id=1" class="back-button">
                                    Details ansehen
                                </a>
                            </td>
                        </tr>

                        <tr>
                            <td>27.05.2026 - 10:15 Uhr</td>
                            <td>Datenbanken</td>
                            <td>3</td>
                            <td class="score-cell">72%</td>
                            <td>
                                <a href="archive_detail.php?id=2" class="back-button">
                                    Details ansehen
                                </a>
                            </td>
                        </tr>

                        <tr>
                            <td>26.05.2026 - 13:00 Uhr</td>
                            <td>HTML / CSS</td>
                            <td>6</td>
                            <td class="score-cell">91%</td>
                            <td>
                                <a href="archive_detail.php?id=3" class="back-button">
                                    Details ansehen
                                </a>
                            </td>
                        </tr>

                        <tr>
                            <td>25.05.2026 - 09:45 Uhr</td>
                            <td>JavaScript</td>
                            <td>5</td>
                            <td class="score-cell">65%</td>
                            <td>
                                <a href="archive_detail.php?id=4" class="back-button">
                                    Details ansehen
                                </a>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

        </section>

    </main>

</body>
</html>