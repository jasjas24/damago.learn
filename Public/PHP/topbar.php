<?php
// init.php einbinden, um Session und $username verfügbar zu machen
require_once 'init.php';
/** @var string $username */
?>
<header class="topbar">
    <a class="topbar-brand" href="dashboard.php">
        <img src="../damago-logo.png" alt="damago Logo" class="topbar-logo">
    </a>

    <div class="topbar-account topbar-account-spaced">
        <a href="dashboard.php" class="logout-button" title="Zum Dashboard">
            Dashboard
        </a>

        <?php if ($username !== 'Gast'): ?>
            <div>
                <a href="profile.php" class="logout-button" data-tooltip="Klicken, um Profil zu bearbeiten">
                    <?php echo htmlspecialchars($username); ?>
                </a>
                <a href="logout.php" class="logout-button">logout</a>
            </div>
        <?php elseif (isset($_SESSION['player_name'])): ?>
            <div>
                <span class="account-name"><?php echo htmlspecialchars($_SESSION['player_name']); ?></span>
            </div>
        <?php else: ?>
            <div>
                <span class="account-name">Gast</span>
            </div>
        <?php endif; ?>
    </div>
</header>

<script>
window.addEventListener('pageshow', function (event) {
    // Falls die Seite aus dem Arbeitsspeicher-Verlauf (Zurück-Button) kommt: Neustart erzwingen!
    if (event.persisted || (typeof window.performance !== "undefined" && window.performance.navigation.type === 2)) {
        window.location.reload();
    }
});
</script>