<?php
require_once 'init.php';
require_once 'db.php';

/** @var string $username */
/** @var string $role */

if (($username ?? 'Gast') === 'Gast') {
    header('Location: dashboard.php');
    exit;
}

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $repeatPassword = $_POST['repeat_password'] ?? '';

    if ($currentPassword === '' || $newPassword === '' || $repeatPassword === '') {
        $errorMessage = 'Bitte fülle alle Passwortfelder aus.';
    } elseif ($newPassword !== $repeatPassword) {
        $errorMessage = 'Das neue Passwort und die Wiederholung stimmen nicht überein.';
    } else {
        try {
            $stmt = $pdo->prepare("
                SELECT id, password_hash
                FROM users
                WHERE username = ?
                LIMIT 1
            ");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $errorMessage = 'Benutzerkonto wurde nicht gefunden.';
            } elseif (!password_verify($currentPassword, $user['password_hash'])) {
                $errorMessage = 'Das aktuelle Passwort ist falsch.';
            } else {
                $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

                $updateStmt = $pdo->prepare("
                    UPDATE users
                    SET password_hash = ?
                    WHERE id = ?
                ");
                $updateStmt->execute([
                    $newPasswordHash,
                    $user['id']
                ]);

                $successMessage = 'Dein Passwort wurde erfolgreich geändert.';
            }
        } catch (PDOException $e) {
            $errorMessage = 'Beim Ändern des Passworts ist ein Fehler aufgetreten.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil | damago Quizsystem</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body class="auth-page">

    <div class="page-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <?php include_once 'topbar.php'; ?>

    <main class="auth-layout">
        <section class="auth-info">
            <h1>Mein Profil.</h1>
            <p>
                Hier kannst du dein Passwort ändern.
                Gib dafür zuerst dein aktuelles Passwort ein.
            </p>

            <div class="info-list">
                <div class="info-list-code">
                    Angemeldet als: <?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            </div>
        </section>

        <section class="auth-card">
            <div class="auth-header">
                <span class="eyebrow">Profil</span>
                <h2>Passwort ändern</h2>
                <p>
                    Wenn der Admin dir ein neues Übergangspasswort gegeben hat,
                    gib dieses als aktuelles Passwort ein.
                </p>
            </div>

            <?php if ($successMessage !== ''): ?>
                <div class="status-message">
                    <?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <?php if ($errorMessage !== ''): ?>
                <div class="status-message">
                    <?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form class="auth-form" action="profile.php" method="POST">
                <div class="form-group">
                    <label for="current_password">Aktuelles Passwort</label>
                    <input 
                        type="password" 
                        id="current_password" 
                        name="current_password" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="new_password">Neues Passwort</label>
                    <input 
                        type="password" 
                        id="new_password" 
                        name="new_password" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="repeat_password">Neues Passwort wiederholen</label>
                    <input 
                        type="password" 
                        id="repeat_password" 
                        name="repeat_password" 
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary">
                    Passwort speichern
                </button>
            </form>

            <div class="auth-links">
                <a href="dashboard.php">Zurück zum Dashboard</a>
            </div>
        </section>
    </main>

</body>
</html>