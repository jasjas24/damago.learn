<?php
require_once 'init.php';

/** @var string $username */
/** @var string $role */



// $allowedRoles = ["admin", "teacher", "student", "guest"];

// if (!in_array($role, $allowedRoles, true)) {
//     $role = "gast";
// }

$roleNames = [
    "admin" => "Administrator",
    "teacher" => "Dozent",
    "student" => "User",
    "guest" => "Gast"
];

$displayRole = $roleNames[$role];

/*
    Alle möglichen Dashboard-Karten.
    quiz_code.php liegt im gleichen Ordner wie dashboard.php:
    Public/PHP/quiz_code.php
*/

$dashboardCards = [
    "join_quiz" => [
        "href" => "join_quiz.php",
        "icon" => "QB",
        "title" => "Quiz beitreten",
        "description" => "Teilnahme-Code eingeben und einer Quizrunde beitreten."
    ],
    "create_quiz" => [
        "href" => "host_quiz.php",
        "icon" => "QS",
        "title" => "Quiz eröffnen",
        "description" => "Eine neue Quizrunde erstellen und hosten."
    ],
    "history" => [
        "href" => "statistic.php",
        "icon" => "LF",
        "title" => "Lernfortschritt",
        "description" => "Eigene Ergebnisse und gespielte Quizrunden ansehen."
    ],
    "admin" => [
        "href" => "admin.php",
        "icon" => "AD",
        "title" => "Adminbereich",
        "description" => "Fragenpools, Fragen, Medien, Nutzer und Archive verwalten."
    ],
];

/*
    Rollenbasierte Zugriffskontrolle für die Anzeige.
    Jede Rolle bekommt nur die Karten, die sie sehen darf.
*/

$rolePermissions = [
    "admin" => [
        "join_quiz",
        "create_quiz",
        "history",
        "evaluation",
        "admin",
        "manage_questions",
        "manage_media"
    ],
    "teacher" => [
        "join_quiz",
        "create_quiz",
        "history",
        "evaluation"
    ],
    "student" => [
        "join_quiz",
        "create_quiz",
        "history"
    ],
    "guest" => [
        "join_quiz",
        "create_quiz"
    ]
];

$visibleCards = $rolePermissions[$role] ?? [];

/*
    Kategorien fuer das Dashboard: gruppieren die Karten thematisch.
    - Spielen:    an Quizrunden teilnehmen oder selbst hosten
    - Auswertung: eigene Ergebnisse und Quiz-Auswertungen ansehen
    - Verwaltung: Admin-Funktionen (Fragen, Medien, Nutzer, Archive)
    Angezeigt werden nur Kategorien, die fuer die jeweilige Rolle
    mindestens eine sichtbare Karte enthalten.
*/
$dashboardCategories = [
    "Spielen"    => ["join_quiz", "create_quiz"],
    "Auswertung" => ["history", "evaluation"],
    "Verwaltung" => ["admin", "manage_questions", "manage_media"],
];
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

    <div class="page-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <?php include_once 'topbar.php'; ?>

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
                <?php if ($role === 'guest'): ?>
                    <div class="denied">
                        Eigenen Lernfortschritt verfolgen ist als Gast nicht möglich!
                    </div>
                <?php endif; ?>
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

                    <?php foreach ($dashboardCategories as $categoryLabel => $categoryCardKeys): ?>
                        <?php
                            // Nur die Karten dieser Kategorie, die der Nutzer sehen darf.
                            // array_intersect behaelt die Reihenfolge der Kategorie-Definition bei.
                            $cardsInCategory = array_values(array_intersect($categoryCardKeys, $visibleCards));
                        ?>
                        <?php if (!empty($cardsInCategory)): ?>

                            <div class="dashboard-category">
                                <div class="dashboard-category-label"><?php echo htmlspecialchars($categoryLabel); ?></div>

                                <div class="dashboard-category-grid">
                                    <?php foreach ($cardsInCategory as $cardKey): ?>
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
                                </div>
                            </div>

                        <?php endif; ?>
                    <?php endforeach; ?>

                <?php endif; ?>

            </div>

            <div class="dashboard-footer-links">
                <a href="logout.php">Benutzer wechseln</a>
            </div>
        </section>
    </main>
</body>
</html>
