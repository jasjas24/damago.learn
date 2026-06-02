<?php
// init.php einbinden, um Session und $username verfügbar zu machen
require_once 'init.php';
/** @var string $username */
?>
<header class="topbar">
    <a class="topbar-brand" href="dashboard.php">
        <img src="../damago-logo.png" alt="damago Logo" class="topbar-logo">
    </a>

    <div class="topbar-right">
        <?php if ($username !== 'Gast'): ?>
            <a href="profile.php" class="logout-button" style="margin-right: 15px;" title="Zum Profil">
                <?php echo htmlspecialchars($username); ?>
            </a>
        <?php endif; ?>    

        <a href="dashboard.php" class="logout-button">Dashboard</a>

        <?php if ($username !== 'Gast'): ?>
            <a href="logout.php" class="logout-button" style="margin-left: 10px;">
                logout
            </a>
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