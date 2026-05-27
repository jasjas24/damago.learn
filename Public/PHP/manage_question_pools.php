<?php
require_once __DIR__ . '/init.php';

/*
|--------------------------------------------------------------------------
| Platzhalter-Daten
|--------------------------------------------------------------------------
| Diese Daten sind nur für die Anzeige.
| Die echte Datenbanklogik kann dein Kollege später hier einbauen.
|--------------------------------------------------------------------------
*/

$questionPools = [
    [
        'id' => 1,
        'name' => 'PHP Grundlagen',
        'description' => 'Syntax, Variablen, Bedingungen, Schleifen und Formulare.',
        'is_active' => 1,
        'created_at' => '2026-05-27 09:00:00'
    ],
    [
        'id' => 2,
        'name' => 'HTML und CSS',
        'description' => 'HTML-Struktur, CSS-Layout, Klassen, IDs und responsives Design.',
        'is_active' => 1,
        'created_at' => '2026-05-27 09:15:00'
    ],
    [
        'id' => 3,
        'name' => 'JavaScript Grundlagen',
        'description' => 'Events, Funktionen, DOM-Manipulation und einfache Validierungen.',
        'is_active' => 0,
        'created_at' => '2026-05-27 09:30:00'
    ],
    [
        'id' => 4,
        'name' => 'Datenbanken',
        'description' => 'SQL, Tabellen, Beziehungen und einfache Datenbankabfragen.',
        'is_active' => 1,
        'created_at' => '2026-05-27 09:45:00'
    ]
];

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$activePools = array_filter($questionPools, function ($pool) {
    return (int)$pool['is_active'] === 1;
});

$inactivePools = array_filter($questionPools, function ($pool) {
    return (int)$pool['is_active'] === 0;
});

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Fragenpools verwalten</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php if (file_exists(__DIR__ . '/../CSS/style.css')): ?>
        <link rel="stylesheet" href="../CSS/style.css">
    <?php else: ?>
        <link rel="stylesheet" href="../css/style.css">
    <?php endif; ?>
</head>

<body class="lobby-page">

<div class="page-orbs">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
</div>

<?php
if (function_exists('topbar')) {
    topbar();
} elseif (file_exists(__DIR__ . '/topbar.php')) {
    require_once __DIR__ . '/topbar.php';
}
?>

<main class="lobby-layout">

    <section class="lobby-info">

        <span class="eyebrow">Adminbereich</span>

        <h1>Fragenpools verwalten</h1>

        <div class="host-card">
            <div class="auth-header">
                <span class="eyebrow">Erstellen</span>
                <h2>Neuen Fragenpool hinzufügen</h2>
                <p>Lege hier einen neuen Themenbereich für Fragen an.</p>
            </div>

            <form method="POST" action="#" class="auth-form">
                <input type="hidden" name="action" value="create">

                <div class="form-group">
                    <label for="name">Name des Fragenpools</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="z. B. PHP Grundlagen"
                    >
                </div>

                <div class="form-group">
                    <label for="description">Beschreibung</label>
                    <input
                        type="text"
                        id="description"
                        name="description"
                        placeholder="Kurze Beschreibung des Themenbereichs"
                    >
                </div>

                <div class="form-group">
                    <label for="is_active">Status</label>
                    <select id="is_active" name="is_active">
                        <option value="1" selected>Aktiv</option>
                        <option value="0">Inaktiv</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    Fragenpool erstellen
                </button>
            </form>
        </div>

    </section>

    <section class="lobby-panel">

        <div class="auth-header">
            <span class="eyebrow">Bearbeiten</span>
            <h2>Vorhandene Fragenpools</h2>
            <p>Klicke einen Fragenpool an, um die Optionen anzuzeigen.</p>
        </div>

        <div class="lobby-meta">
            <div>
                <span>Gesamt</span>
                <strong><?= count($questionPools) ?> Pools</strong>
            </div>

            <div>
                <span>Aktiv</span>
                <strong><?= count($activePools) ?> aktiv</strong>
            </div>
        </div>

        <div class="participant-header">
            <h3>Fragenpools</h3>
            <span><?= count($questionPools) ?> Einträge</span>
        </div>

        <?php if (count($questionPools) === 0): ?>

            <p class="status-message">
                Es wurden noch keine Fragenpools erstellt.
            </p>

        <?php else: ?>

            <div class="ranking-list">

                <?php foreach ($questionPools as $pool): ?>

                    <details class="ranking-item">
                        <summary>
                            <strong><?= e($pool['name']) ?></strong>

                            <?php if ((int)$pool['is_active'] === 1): ?>
                                <span class="correct-text">Aktiv</span>
                            <?php else: ?>
                                <span class="wrong-text">Inaktiv</span>
                            <?php endif; ?>
                        </summary>

                        <div class="ranking-footer">
                            <span>
                                <?= e($pool['description']) ?>
                            </span>
                        </div>

                        <div class="ranking-footer">
                            <span>
                                Erstellt am <?= e(date('d.m.Y H:i', strtotime($pool['created_at']))) ?>
                            </span>
                        </div>

                        <div class="lobby-actions">

                            <form method="POST" action="#">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="id" value="<?= (int)$pool['id'] ?>">
                                <button type="submit" class="btn-secondary">
                                    Bearbeiten
                                </button>
                            </form>

                            <?php if ((int)$pool['is_active'] === 1): ?>
                                <form method="POST" action="#">
                                    <input type="hidden" name="action" value="deactivate">
                                    <input type="hidden" name="id" value="<?= (int)$pool['id'] ?>">
                                    <button type="submit" class="btn-secondary">
                                        Deaktivieren
                                    </button>
                                </form>
                            <?php else: ?>
                                <form method="POST" action="#">
                                    <input type="hidden" name="action" value="activate">
                                    <input type="hidden" name="id" value="<?= (int)$pool['id'] ?>">
                                    <button type="submit" class="btn-secondary">
                                        Aktivieren
                                    </button>
                                </form>
                            <?php endif; ?>

                            <form method="POST" action="#">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int)$pool['id'] ?>">
                                <button type="submit" class="btn-secondary">
                                    Löschen
                                </button>
                            </form>

                        </div>

                    </details>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </section>

</main>

</body>
</html>