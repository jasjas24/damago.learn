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
        "href"        => "manage_users.php",
        "icon"        => "NV",
        "title"       => "Nutzer",
        "description" => "Benutzerkonten einsehen, Rollen vergeben und verwalten."
    ],
    [
        "href"        => "archive.php",
        "icon"        => "AR",
        "title"       => "Archive",
        "description" => "Abgeschlossene Quizrunden und Ergebnisse einsehen."
    ],
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
