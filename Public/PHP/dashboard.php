<?php
session_start();

/*
    Diese Werte werden später vom Backend nach Login,
    Registrierung oder Gastbeitritt gesetzt.

    Beispiel:
    $_SESSION["username"] = "Max";
    $_SESSION["role"] = "user";
*/

$username = $_SESSION["username"] ?? "Gast";
$role = $_SESSION["role"] ?? "gast";

$allowedRoles = ["admin", "dozent", "user", "gast"];

if (!in_array($role, $allowedRoles)) {
    $role = "gast";
}

$roleNames = [
    "admin" => "Admin",
    "dozent" => "Dozent",
    "user" => "User",
    "gast" => "Gast"
];

$displayRole = $roleNames[$role];
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | damago Quizsystem</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body class="auth-page">

    <header class="topbar">
        <a href="index.html" class="topbar-brand">
            <img src="damago-logo.png" alt="damago Logo" class="topbar-logo">
        </a>
    </header>

    <main class="auth-layout dashboard-auth-layout">
        <section class="auth-info">

            <h1>
                Willkommen,
                <?php echo htmlspecialchars($username); ?>.
            </h1>

            <p>
                Wähle aus, welche Funktion du im Quizsystem nutzen möchtest.
                Dein Dashboard zeigt dir nur die Bereiche, die zu deiner Rolle passen.
            </p>

            <div class="info-list">
                <div>
                    Angemeldet als: <?php echo htmlspecialchars($displayRole); ?>
                </div>
            </div>
        </section>

        <section class="dashboard-panel">
            <div class="auth-header">
                <span class="eyebrow">Lernbereich</span>
                <h2>Was möchtest du tun?</h2>
                <p>Wähle eine Aktion aus, um fortzufahren.</p>
            </div>

            <div class="dashboard-actions">

                <?php if ($role === "admin"): ?>

                    <a href="quiz_beitreten.html" class="dashboard-action-card">
                        <div class="dashboard-action-icon">QB</div>
                        <div>
                            <h3>Quiz beitreten</h3>
                            <p>Mit Teilnahme-Code an einer Quizrunde teilnehmen.</p>
                        </div>
                    </a>

                    <a href="quiz_erstellen.html" class="dashboard-action-card">
                        <div class="dashboard-action-icon">QS</div>
                        <div>
                            <h3>Quiz starten</h3>
                            <p>Eine neue Quizrunde erstellen und hosten.</p>
                        </div>
                    </a>

                    <a href="historie.html" class="dashboard-action-card">
                        <div class="dashboard-action-icon">LF</div>
                        <div>
                            <h3>Lernfortschritt</h3>
                            <p>Eigene Ergebnisse und gespielte Quizrunden ansehen.</p>
                        </div>
                    </a>

                    <a href="admin.html" class="dashboard-action-card">
                        <div class="dashboard-action-icon">AD</div>
                        <div>
                            <h3>Adminbereich</h3>
                            <p>Fragenpools, Fragen, Medien, Nutzer und Archive verwalten.</p>
                        </div>
                    </a>

                    <a href="fragen_verwalten.html" class="dashboard-action-card">
                        <div class="dashboard-action-icon">FV</div>
                        <div>
                            <h3>Fragen verwalten</h3>
                            <p>Fragen erstellen, bearbeiten, deaktivieren oder importieren.</p>
                        </div>
                    </a>

                    <a href="medien_verwalten.html" class="dashboard-action-card">
                        <div class="dashboard-action-icon">MV</div>
                        <div>
                            <h3>Medien verwalten</h3>
                            <p>Bilder hochladen und Fragen zuordnen.</p>
                        </div>
                    </a>

                <?php elseif ($role === "dozent"): ?>

                    <a href="quiz_beitreten.html" class="dashboard-action-card">
                        <div class="dashboard-action-icon">QB</div>
                        <div>
                            <h3>Quiz beitreten</h3>
                            <p>Mit Teilnahme-Code an einer Quizrunde teilnehmen.</p>
                        </div>
                    </a>

                    <a href="quiz_erstellen.html" class="dashboard-action-card">
                        <div class="dashboard-action-icon">QS</div>
                        <div>
                            <h3>Quiz starten</h3>
                            <p>Eine Quizrunde für den Unterricht erstellen und steuern.</p>
                        </div>
                    </a>

                    <a href="historie.html" class="dashboard-action-card">
                        <div class="dashboard-action-icon">LF</div>
                        <div>
                            <h3>Lernfortschritt</h3>
                            <p>Eigene Ergebnisse und gespielte Quizrunden ansehen.</p>
                        </div>
                    </a>

                    <a href="auswertung.html" class="dashboard-action-card">
                        <div class="dashboard-action-icon">AW</div>
                        <div>
                            <h3>Quiz-Auswertung</h3>
                            <p>Ergebnisse und Zwischenstände von Quizrunden ansehen.</p>
                        </div>
                    </a>

                <?php elseif ($role === "user"): ?>

                    <a href="quiz_beitreten.html" class="dashboard-action-card">
                        <div class="dashboard-action-icon">QB</div>
                        <div>
                            <h3>Quiz beitreten</h3>
                            <p>Mit Teilnahme-Code an einer Quizrunde teilnehmen.</p>
                        </div>
                    </a>

                    <a href="quiz_erstellen.html" class="dashboard-action-card">
                        <div class="dashboard-action-icon">QS</div>
                        <div>
                            <h3>Quiz starten</h3>
                            <p>Eine eigene Quizrunde starten und Lerninhalte üben.</p>
                        </div>
                    </a>

                    <a href="historie.html" class="dashboard-action-card">
                        <div class="dashboard-action-icon">LF</div>
                        <div>
                            <h3>Lernfortschritt</h3>
                            <p>Eigene Ergebnisse und gespielte Quizrunden ansehen.</p>
                        </div>
                    </a>

                <?php elseif ($role === "gast"): ?>

                    <a href="quiz_beitreten.html" class="dashboard-action-card">
                        <div class="dashboard-action-icon">QB</div>
                        <div>
                            <h3>Quiz beitreten</h3>
                            <p>Ohne Konto mit Teilnahme-Code an einer Quizrunde teilnehmen.</p>
                        </div>
                    </a>

                    <a href="quiz_erstellen.html" class="dashboard-action-card">
                        <div class="dashboard-action-icon">QS</div>
                        <div>
                            <h3>Quiz starten</h3>
                            <p>Ohne Registrierung eine Quizrunde erstellen und hosten.</p>
                        </div>
                    </a>

                <?php else: ?>

                    <p>Deine Rolle konnte nicht erkannt werden.</p>

                <?php endif; ?>

            </div>

            <div class="dashboard-footer-links">
                <a href="login.html">Benutzer wechseln</a>
                <a href="index.html">Zur Startseite</a>
            </div>
        </section>
    </main>

</body>
</html>