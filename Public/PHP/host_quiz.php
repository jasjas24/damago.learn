<?php
require_once 'init.php';
require_once 'db.php';
require_once 'avatars.php';

/** @var string $username */
/** @var string $role */

try {
    // Holt alle verfügbaren Pools aus der Datenbank
    $stmt = $pdo->query("SELECT id, name FROM question_pools ORDER BY id ASC");
    $pools = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Fehler beim Laden der Fragenpools: " . $e->getMessage());
}

// Flash-Meldung (z. B. "Pool zu klein") und zuletzt eingegebene Werte aus setup_lobby.php
$hostError = $_SESSION['host_error'] ?? '';
$hostForm  = $_SESSION['host_form']  ?? [];
unset($_SESSION['host_error'], $_SESSION['host_form']);

$fPool  = $hostForm['question_pool']  ?? '';
$fCount = (int)($hostForm['question_count'] ?? 10);
$fTime  = (string)($hostForm['time_limit'] ?? '30');
$fMode  = $hostForm['point_mode'] ?? 'partial';
$fPlays = $hostForm['host_plays'] ?? 'yes';
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz hosten | damago Quizsystem</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body class="auth-page">

    <div class="page-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <?php include_once 'topbar.php'; ?>

    <main class="host-layout">
        <section class="host-card">
            <div class="auth-header">
                <span class="eyebrow">Host-Einstellungen</span>
                <h2>Quiz erstellen</h2>
                <p>Lege fest, wie die Quizrunde ablaufen soll.</p>
            </div>

            <?php if ($hostError !== ''): ?>
                <div class="alert-error"><?php echo htmlspecialchars($hostError); ?></div>
            <?php endif; ?>

            <form id="quizForm" class="auth-form" action="setup_lobby.php" method="POST">
                <div class="form-group">
                    <label for="question_pool">Fragenpool</label>
                    <select id="question_pool" name="question_pool" required>
                        <option value="">Fragenpool auswählen</option>
                        <?php foreach ($pools as $p): ?>
                            <option value="<?php echo htmlspecialchars($p['name']); ?>" <?php echo ($p['name'] === $fPool) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars(ucfirst($p['name'])); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="question_count">Anzahl Fragen</label>
                    <input
                        type="number"
                        id="question_count"
                        name="question_count"
                        min="1"
                        max="50"
                        value="<?php echo $fCount; ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="time_limit">Zeitlimit pro Frage</label>
                    <select id="time_limit" name="time_limit" required>
                        <option value="15" <?php echo $fTime === '15' ? 'selected' : ''; ?>>15 Sekunden</option>
                        <option value="30" <?php echo $fTime === '30' ? 'selected' : ''; ?>>30 Sekunden</option>
                        <option value="45" <?php echo $fTime === '45' ? 'selected' : ''; ?>>45 Sekunden</option>
                        <option value="60" <?php echo $fTime === '60' ? 'selected' : ''; ?>>60 Sekunden</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="point_mode">Punkte-Modus</label>
                    <select id="point_mode" name="point_mode" required>
                        <option value="all_or_nothing" <?php echo $fMode === 'all_or_nothing' ? 'selected' : ''; ?>>Ganz oder gar nicht</option>
                        <option value="partial" <?php echo $fMode === 'partial' ? 'selected' : ''; ?>>Teilpunkte</option>
                        <option value="time_bonus" <?php echo $fMode === 'time_bonus' ? 'selected' : ''; ?>>Zeitbonus</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="host_plays">Host spielt mit</label>
                    <select id="host_plays" name="host_plays" required>
                        <option value="no" <?php echo $fPlays === 'no' ? 'selected' : ''; ?>>Nein</option>
                        <option value="yes" <?php echo $fPlays === 'yes' ? 'selected' : ''; ?>>Ja</option>
                    </select>
                </div>

                <?php damago_render_avatar_picker(); ?>

                <button type="submit" class="btn btn-primary">
                    Lobby erstellen
                </button>
            </form>

            <div class="host-back">
                <a href="dashboard.php" class="back-button">← Zurück zum Dashboard</a>
            </div>
        </section>
    </main>

    <?php include_once 'footbar.php'; ?>

</body>
<script>
// Avatar-Auswahl nur anzeigen/verlangen, wenn der Host selbst mitspielt
(function () {
    const hostPlays = document.getElementById('host_plays');
    const avatarGroup = document.querySelector('.avatar-group');
    if (!hostPlays || !avatarGroup) return;

    // Blendet die Avatar-Auswahl ein oder aus, je nachdem ob der Host mitspielt.
    function syncAvatarRequirement() {
        const plays = hostPlays.value === 'yes';
        avatarGroup.hidden = !plays;
        avatarGroup.querySelectorAll('input[name="avatar"]').forEach(function (radio) {
            radio.required = plays;
        });
    }

    hostPlays.addEventListener('change', syncAvatarRequirement);
    syncAvatarRequirement();
})();
</script>
</html>
