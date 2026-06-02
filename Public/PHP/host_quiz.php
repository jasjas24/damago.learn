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

            <form id="quizForm" class="auth-form" action="setup_lobby.php" method="POST">
                <input type="hidden" id="join_code" name="join_code" value="">
                <div class="form-group">
                    <label for="question_pool">Fragenpool</label>
                    <select id="question_pool" name="question_pool" required>
                        <option value="">Fragenpool auswählen</option>
                        <?php foreach ($pools as $p): ?>
                            <option value="<?php echo htmlspecialchars($p['name']); ?>">
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
                        value="10"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="time_limit">Zeitlimit pro Frage</label>
                    <select id="time_limit" name="time_limit" required>
                        <option value="15">15 Sekunden</option>
                        <option value="30" selected>30 Sekunden</option>
                        <option value="45">45 Sekunden</option>
                        <option value="60">60 Sekunden</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="point_mode">Punkte-Modus</label>
                    <select id="point_mode" name="point_mode" required>
                        <option value="all_or_nothing">Ganz oder gar nicht</option>
                        <option value="partial" selected>Teilpunkte</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="host_plays">Host spielt mit</label>
                    <select id="host_plays" name="host_plays" required>
                        <option value="no">Nein</option>
                        <option value="yes" selected>Ja</option>
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
document.getElementById('quizForm').addEventListener('submit', function(event) {
    const chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
    let generatedCode = "";
    for (let i = 0; i < 5; i++) {
        generatedCode += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('join_code').value = generatedCode;
});

// Avatar-Auswahl nur anzeigen/verlangen, wenn der Host selbst mitspielt
(function () {
    const hostPlays = document.getElementById('host_plays');
    const avatarGroup = document.querySelector('.avatar-group');
    if (!hostPlays || !avatarGroup) return;

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
