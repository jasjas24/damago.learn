<?php
require_once 'init.php';

/** @var string $username */
/** @var string $role */

// Nur Admins dürfen rein
if ($role !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

$menuItems = [
    [
        "href"        => "manage_question_pools.php",
        "icon"        => "pools",
        "title"       => "Fragenpools",
        "description" => "Fragenpools erstellen, bearbeiten und verwalten."
    ],
    [
        "href"        => "manage_questions.php",
        "icon"        => "questions",
        "title"       => "Fragen",
        "description" => "Fragen erstellen, bearbeiten, deaktivieren oder importieren."
    ],
    [
        "href"        => "manage_media.php",
        "icon"        => "media",
        "title"       => "Medien",
        "description" => "Bilder hochladen und Fragen zuordnen."
    ],
    [
        "href"        => "manage_users.php",
        "icon"        => "users",
        "title"       => "Benutzer",
        "description" => "Benutzerkonten einsehen, Rollen vergeben und verwalten."
    ],
    [
        "href"        => "archive.php",
        "icon"        => "archive",
        "title"       => "Archive",
        "description" => "Abgeschlossene Quizrunden und Ergebnisse einsehen."
    ],
];

// Inline-SVG-Icons (Feather-Stil). Styling (Größe, stroke, fill ...) liegt in style.css unter ".dashboard-action-icon svg".
$svgAttrs = 'viewBox="0 0 24 24" aria-hidden="true"';
$icons = [
    // Fragenpools – gestapelte Ebenen
    'pools' => '<svg ' . $svgAttrs . '><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>',
    // Fragen – Fragezeichen im Kreis
    'questions' => '<svg ' . $svgAttrs . '><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
    // Medien – Bild
    'media' => '<svg ' . $svgAttrs . '><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>',
    // Nutzer – Personen
    'users' => '<svg ' . $svgAttrs . '><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
    // Archiv – Archivbox
    'archive' => '<svg ' . $svgAttrs . '><polyline points="21 8 21 21 3 21 3 8"></polyline><rect x="1" y="3" width="22" height="5"></rect><line x1="10" y1="12" x2="14" y2="12"></line></svg>',
];
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adminbereich | damago Quizsystem</title>
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
            <h1>Adminbereich.</h1>
            <p>
                Hier verwaltest du die Inhalte und Einstellungen des Quizsystems.
                Alle Änderungen wirken sich direkt auf das gesamte System aus.
            </p>

            <div class="info-list">
                <div>Zugriff nur für Administratoren</div>
            </div>
        </section>

        <section class="dashboard-panel">
            <div class="auth-header">
                <span class="eyebrow">Administration</span>
                <h2>Verwaltung</h2>
                <p>Wähle einen Bereich aus, den du verwalten möchtest.</p>
            </div>

            <div class="dashboard-actions">
                <?php foreach ($menuItems as $item): ?>
                    <a href="<?php echo htmlspecialchars($item['href']); ?>" class="dashboard-action-card">
                        <div class="dashboard-action-icon">
                            <?php echo $icons[$item['icon']] ?? htmlspecialchars($item['icon']); ?>
                        </div>
                        <div>
                            <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                            <p><?php echo htmlspecialchars($item['description']); ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="dashboard-footer-links">
                <a href="dashboard.php">← Zurück zum Dashboard</a>
            </div>
        </section>

    </main>

    <?php include_once 'footbar.php'; ?>

</body>
</html>
