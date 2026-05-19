<?php
session_start();

/*
    Diese Werte werden nach Login, Registrierung oder Gastbeitritt gesetzt.

    Beispiele:
    $_SESSION["username"] = "Max";
    $_SESSION["role"] = "user";

    Mögliche Rollen:
    admin
    dozent
    user
    gast
*/

$username = $_SESSION["username"] ?? "Gast";
$role = $_SESSION["role"] ?? "gast";

$allowedRoles = ["admin", "dozent", "user", "gast"];

if (!in_array($role, $allowedRoles, true)) {
    $role = "gast";
}

$roleNames = [
    "admin" => "Admin",
    "dozent" => "Dozent",
    "user" => "User",
    "gast" => "Gast"
];

$displayRole = $roleNames[$role];

/*
    Alle möglichen Dashboard-Karten.
    quiz_code.php liegt im gleichen Ordner wie dashboard.php:
    Public/PHP/quiz_code.php
*/

$dashboardCards = [
    "quiz_beitreten" => [
        "href" => "quiz_code.php",
        "icon" => "QB",
        "title" => "Quiz beitreten",
        "description" => "Teilnahme-Code eingeben und einer Quizrunde beitreten."
    ],
    "quiz_erstellen" => [
        "href" => "quiz_erstellen.html",
        "icon" => "QS",
        "title" => "Quiz starten",
        "description" => "Eine neue Quizrunde erstellen und hosten."
    ],
    "historie" => [
        "href" => "historie.php",
        "icon" => "LF",
        "title" => "Lernfortschritt",
        "description" => "Eigene Ergebnisse und gespielte Quizrunden ansehen."
    ],
    "auswertung" => [
        "href" => "auswertung.php",
        "icon" => "AW",
        "title" => "Quiz-Auswertung",
        "description" => "Ergebnisse und Zwischenstände von Quizrunden ansehen."
    ],
    "admin" => [
        "href" => "admin.php",
        "icon" => "AD",
        "title" => "Adminbereich",
        "description" => "Fragenpools, Fragen, Medien, Nutzer und Archive verwalten."
    ],
    "fragen_verwalten" => [
        "href" => "fragen_verwalten.php",
        "icon" => "FV",
        "title" => "Fragen verwalten",
        "description" => "Fragen erstellen, bearbeiten, deaktivieren oder importieren."
    ],
    "medien_verwalten" => [
        "href" => "medien_verwalten.php",
        "icon" => "MV",
        "title" => "Medien verwalten",
        "description" => "Bilder hochladen und Fragen zuordnen."
    ]
];

/*
    Rollenbasierte Zugriffskontrolle für die Anzeige.
    Jede Rolle bekommt nur die Karten, die sie sehen darf.
*/

$rolePermissions = [
    "admin" => [
        "quiz_beitreten",
        "quiz_erstellen",
        "historie",
        "auswertung",
        "admin",
        "fragen_verwalten",
        "medien_verwalten"
    ],
    "dozent" => [
        "quiz_beitreten",
        "quiz_erstellen",
        "historie",
        "auswertung"
    ],
    "user" => [
        "quiz_beitreten",
        "quiz_erstellen",
        "historie"
    ],
    "gast" => [
        "quiz_beitreten",
        "quiz_erstellen"
    ]
];

$visibleCards = $rolePermissions[$role] ?? [];
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
        <a href="../index.html" class="topbar-brand">
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

                <?php if (empty($visibleCards)): ?>

                    <p>Für deine Rolle wurden keine Funktionen gefunden.</p>

                <?php else: ?>

                    <?php foreach ($visibleCards as $cardKey): ?>
                        <?php $card = $dashboardCards[$cardKey]; ?>

                        <a href="<?php echo htmlspecialchars($card["href"]); ?>" class="dashboard-action-card">
                            <div class="dashboard-action-icon">
                                <?php echo htmlspecialchars($card["icon"]); ?>
                            </div>

                            <div>
                                <h3><?php echo htmlspecialchars($card["title"]); ?></h3>
                                <p><?php echo htmlspecialchars($card["description"]); ?></p>
                            </div>
                        </a>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>

            <div class="dashboard-footer-links">
                <a href="../login.html">Benutzer wechseln</a>
            </div>
        </section>
    </main>

</body>
</html>