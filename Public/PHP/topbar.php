<?php
// init.php einbinden, um Session und $username verfügbar zu machen
require_once 'init.php';
/** @var string $username */
?>
<header class="topbar">
    <a class="topbar-brand">
        <img src="../damago-logo.png" alt="damago Logo" class="topbar-logo">
    </a>

    <?php if ($username !== 'Gast'): ?>
        <div class="topbar-account">
            <span class="account-name"><?php echo htmlspecialchars($username); ?></span>
            <a href="logout.php" class="logout-button">Abmelden</a>
        </div>
    <?php elseif (isset($_SESSION['player_name'])): ?>
        <div class="topbar-account">
            <span class="account-name"><?php echo htmlspecialchars($_SESSION['player_name']); ?></span>
            <a href="logout.php" class="logout-button">Verlassen</a>
        </div>
    <?php else: ?>
        <div class="topbar-account">
            <span class="account-name">Gast</span>
            <a href="logout.php" class="logout-button">Spiel beenden</a>
        </div>
    <?php endif; ?>
</header>
