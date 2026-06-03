<?php
require_once 'init.php';
require_once 'db.php';

/** @var string $username */
/** @var string $role */

$roleNames = [
    "admin" => "Administrator",
    "teacher" => "Dozent",
    "student" => "User",
    "guest" => "Gast"
];

$displayRole = $roleNames[$role];

// Einmalige Hinweis-Meldung (z. B. "Dieses Spiel existiert nicht mehr").
$dashMessage = $_SESSION['dash_message'] ?? '';
unset($_SESSION['dash_message']);

/*
    Alle möglichen Dashboard-Karten.
    quiz_code.php liegt im gleichen Ordner wie dashboard.php:
    Public/PHP/quiz_code.php
*/

$dashboardCards = [
    "join_quiz" => [
        "href" => "join_quiz.php",
        "icon" => "join",
        "title" => "Quiz beitreten",
        "description" => "Teilnahme-Code eingeben und einer Quizrunde beitreten."
    ],
    "create_quiz" => [
        "href" => "host_quiz.php",
        "icon" => "create",
        "title" => "Quiz eröffnen",
        "description" => "Eine neue Quizrunde erstellen und hosten."
    ],
    "history" => [
        "href" => "statistic.php",
        "icon" => "history",
        "title" => "Lernfortschritt",
        "description" => "Eigene Ergebnisse und gespielte Quizrunden ansehen."
    ],

    "admin" => [
        "href" => "admin_area.php",
        "icon" => "admin",
        "title" => "Adminbereich",
        "description" => "Fragenpools, Fragen, Medien, Nutzer und Archive verwalten."
    ],

    "teacher" => [
        "href" => "teacher_area.php",
        "icon" => "teacher",
        "title" => "Verwaltungsbereich",
        "description" => "Fragenpools, Fragen, Medien und Archive verwalten."
    ],

];

// Inline-SVG-Icons (Feather-Stil). Styling liegt in style.css unter ".dashboard-action-icon svg".
$svgAttrs = 'viewBox="0 0 24 24" aria-hidden="true"';
$icons = [
    // Symbol für Quiz beitreten
    'join'    => '<svg ' . $svgAttrs . '><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>',
    // Symbol für Quiz eröffnen
    'create'  => '<svg ' . $svgAttrs . '><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>',
    // Symbol für den Lernfortschritt
    'history' => '<svg ' . $svgAttrs . '><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>',
    // Symbol für den Adminbereich
    'admin'   => '<svg ' . $svgAttrs . '><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>',
    // Symbol für den Verwaltungsbereich
    'teacher' => '<svg ' . $svgAttrs . '><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line></svg>',
    // Symbol für Spiel fortsetzen
    'resume'  => '<svg ' . $svgAttrs . '><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8"></polygon></svg>',
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
        "admin"

    ],
    "teacher" => [
        "join_quiz",
        "create_quiz",
        "history",
        "teacher"
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
    Laufende eigene Spiele eines registrierten Hosts (LH 9.4: Rückkehr nach Browser-Absturz/Neulogin).
    Ein Spiel gilt als fortsetzbar, solange es noch nicht durchgespielt ist und nicht zu alt ist.
*/
$activeGames = [];
$currentUserId = $_SESSION['user_id'] ?? null;
if ($currentUserId) {
    try {
        $stmtActive = $pdo->prepare("
            SELECT id, join_code, question_pool, question_count, current_question_index, is_started
            FROM quiz_lobbies
            WHERE host_user_id = ?
              AND is_aborted = 0
              AND current_question_index < question_count
              AND created_at >= (NOW() - INTERVAL 12 HOUR)
            ORDER BY created_at DESC
        ");
        $stmtActive->execute([$currentUserId]);
        $activeGames = $stmtActive->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $activeGames = [];
    }
}

/*
    Kategorien für das Dashboard, um die Karten zu gruppieren.
*/
$dashboardCategories = [
    "Spielen"    => ["join_quiz", "create_quiz"],
    "Auswertung" => ["history", "evaluation"],
    "Verwaltung" => ["admin", "teacher", "manage_questions", "manage_media"],
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

            <?php if ($dashMessage !== ''): ?>
                <div class="alert-error"><?php echo htmlspecialchars($dashMessage); ?></div>
            <?php endif; ?>

            <div class="dashboard-actions">

                <?php if (!empty($activeGames)): ?>
                    <div class="dashboard-category">
                        <div class="dashboard-category-label">Fortsetzen</div>
                        <div class="dashboard-category-grid">
                            <?php foreach ($activeGames as $game): ?>
                                <a href="host_resume.php?lobby_id=<?php echo (int)$game['id']; ?>" class="dashboard-action-card resume-card">
                                    <div class="dashboard-action-icon"><?php echo $icons['resume']; ?></div>
                                    <div>
                                        <h3>Spiel fortsetzen</h3>
                                        <p>
                                            <?php echo htmlspecialchars($game['question_pool']); ?> ·
                                            Code <?php echo htmlspecialchars($game['join_code']); ?> ·
                                            <?php echo ((int)$game['is_started'] === 1)
                                                ? ('läuft (Frage ' . ((int)$game['current_question_index'] + 1) . '/' . (int)$game['question_count'] . ')')
                                                : 'Lobby offen'; ?>
                                        </p>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

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
                                                <?php echo $icons[$card["icon"]] ?? htmlspecialchars($card["icon"]); ?>
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
    <?php include_once 'footbar.php'; ?>

</body>
</html>
