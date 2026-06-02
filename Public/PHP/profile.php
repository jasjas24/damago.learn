<?php
require_once 'init.php';
require_once 'db.php';

// Nur eingeloggte Benutzer (keine Gäste/Spieler) dürfen das Profil sehen
if (($_SESSION['user_role'] ?? 'guest') === 'guest' || empty($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$userId       = (int) $_SESSION['user_id'];
$profileName  = $_SESSION['username'] ?? '';
$profileEmail = $_SESSION['user_email'] ?? '';

$pwMessage = '';   // Rückmeldung an den Benutzer
$pwSuccess = false;

// Passwort-Änderung verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'change_password') {
    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($current === '' || $new === '' || $confirm === '') {
        $pwMessage = 'Passwort konnte nicht gespeichert werden: Bitte alle Felder ausfüllen.';
    } elseif ($new !== $confirm) {
        $pwMessage = 'Passwort konnte nicht gespeichert werden: Die neuen Passwörter stimmen nicht überein.';
    } elseif (strlen($new) < 8) {
        $pwMessage = 'Passwort konnte nicht gespeichert werden: Das neue Passwort muss mindestens 8 Zeichen lang sein.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $hash = $stmt->fetchColumn();

            if (!$hash || !password_verify($current, $hash)) {
                $pwMessage = 'Passwort konnte nicht gespeichert werden: Das aktuelle Passwort ist falsch.';
            } else {
                $newHash = password_hash($new, PASSWORD_DEFAULT);
                $upd = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                $upd->execute([$newHash, $userId]);
                $pwSuccess = true;
                $pwMessage = 'Passwort erfolgreich gespeichert.';
            }
        } catch (PDOException $e) {
            $pwMessage = 'Passwort konnte nicht gespeichert werden (Datenbankfehler).';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mein Profil | damago Quizsystem</title>
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
        <section class="host-card profile-card">
            <div class="auth-header">
                <span class="eyebrow">Konto</span>
                <h2>Mein Profil</h2>
                <p>Deine bei der Registrierung hinterlegten Daten.</p>
            </div>

            <div class="form-group">
                <label for="profile_username">Benutzername</label>
                <input type="text" id="profile_username" value="<?php echo htmlspecialchars($profileName); ?>" disabled>
            </div>

            <div class="form-group">
                <label for="profile_email">E-Mail-Adresse</label>
                <input type="email" id="profile_email" value="<?php echo htmlspecialchars($profileEmail); ?>" disabled>
            </div>

            <p class="profile-hint">Diese Daten kann nur der Administrator ändern.</p>

            <div class="profile-pw<?php echo $pwMessage !== '' ? ' is-open' : ''; ?>">
                <button type="button" class="profile-pw-toggle" aria-expanded="<?php echo $pwMessage !== '' ? 'true' : 'false'; ?>">
                    <span>Passwort ändern</span>
                    <span class="profile-pw-chevron" aria-hidden="true">▾</span>
                </button>

                <div class="profile-pw-body" id="profile-pw-body"<?php echo $pwMessage !== '' ? '' : ' hidden'; ?>>
                    <?php if ($pwMessage !== ''): ?>
                        <div class="<?php echo $pwSuccess ? 'alert-success' : 'alert-error'; ?>">
                            <?php echo htmlspecialchars($pwMessage); ?>
                        </div>
                    <?php endif; ?>

                    <form class="auth-form" action="profile.php" method="POST">
                        <input type="hidden" name="action" value="change_password">

                        <div class="form-group">
                            <label for="current_password">Aktuelles Passwort</label>
                            <input type="password" id="current_password" name="current_password" required>
                        </div>

                        <div class="form-group">
                            <label for="new_password">Neues Passwort</label>
                            <input type="password" id="new_password" name="new_password" required>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Passwort bestätigen</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Speichern</button>
                    </form>
                </div>
            </div>

            <div class="host-back">
                <a href="dashboard.php" class="back-button">← Zurück zum Dashboard</a>
            </div>
        </section>
    </main>

    <script>
        // Passwort-Bereich auf-/zuklappen
        (function () {
            const box    = document.querySelector('.profile-pw');
            const toggle = document.querySelector('.profile-pw-toggle');
            const body   = document.getElementById('profile-pw-body');
            if (!box || !toggle || !body) return;

            toggle.addEventListener('click', function () {
                if (body.hasAttribute('hidden')) {
                    body.removeAttribute('hidden');
                    box.classList.add('is-open');
                    toggle.setAttribute('aria-expanded', 'true');
                } else {
                    body.setAttribute('hidden', '');
                    box.classList.remove('is-open');
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });
        })();
    </script>

</body>
</html>
