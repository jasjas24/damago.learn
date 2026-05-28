<?php
require_once 'init.php';

/** @var string $username */
/** @var string $role */

if (!in_array($role, ['admin', 'teacher'])) {
    header("Location: dashboard.php");
    exit;
}

$menuItems = [
    [
        "href"        => "manage_question_pools.php",
        "icon"        => "FP",
        "title"       => "Fragenpools",
        "description" => "Fragenpools erstellen, bearbeiten und verwalten."
    ],
    [
        "href"        => "manage_questions.php",
        "icon"        => "FV",
        "title"       => "Fragen",
        "description" => "Fragen erstellen, bearbeiten, deaktivieren oder importieren."
    ],
    [
        "href"        => "manage_media.php",
        "icon"        => "MV",
        "title"       => "Medien",
        "description" => "Bilder hochladen und Fragen zuordnen."
    ],
    [
        "href"        => "manage_archives.php",
        "icon"        => "AR",
        "title"       => "Archiv",
        "description" => "Abgeschlossene Quizrunden und Ergebnisse einsehen."
    ],
];
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verwaltungsbereich | damago Quizsystem</title>
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
            <h1>Verwaltungsbereich.</h1>
            <p>
                Hier verwaltest du Fragenpools, Fragen, Medien und das Archiv.
                Alle Änderungen wirken sich direkt auf das gesamte System aus.
            </p>

            <div class="info-list">
                <div>Zugriff für Dozenten und Administratoren</div>
            </div>
        </section>

        <section class="dashboard-panel">
            <div class="auth-header">
                <span class="eyebrow">Verwaltung</span>
                <h2>Verwaltungsbereich</h2>
                <p>Wähle einen Bereich aus, den du verwalten möchtest.</p>
            </div>

            <div class="dashboard-actions">
                <?php foreach ($menuItems as $item): ?>
                    <a href="<?php echo htmlspecialchars($item['href']); ?>" class="dashboard-action-card">
                        <div class="dashboard-action-icon">
                            <?php echo htmlspecialchars($item['icon']); ?>
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

</body>
</html>
