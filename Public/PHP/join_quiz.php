<?php
require_once 'init.php';
require_once 'db.php';

/** @var string $username */
/** @var string $role */

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Code vereinheitlichen (Leerzeichen weg und alles in Großbuchstaben)
    $joinCode = strtoupper(trim($_POST['join_code'] ?? ''));

    // Name ermitteln: Entweder der eingetippte Gastname oder der des eingeloggten Users
    if ($role === 'guest') {
        $playerName = trim($_POST['guest_name'] ?? '');
    } else {
        $playerName = $username;
    }

    if (empty($joinCode) || empty($playerName)) {
        $error = 'Bitte fülle alle Felder aus.';
    } else {
        try {
            // 1. Prüfen, ob die Lobby mit diesem Code existiert und offen ist
            $stmt = $pdo->prepare("SELECT id, is_started FROM quiz_lobbies WHERE join_code = ?");
            $stmt->execute([$joinCode]);
            $lobby = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$lobby) {
                $error = 'Ungültiger Code. Diese Lobby existiert nicht.';
            } elseif ((int)$lobby['is_started'] === 1) {
                $error = 'Dieses Spiel hat leider schon begonnen!';
            } else {
                // 2. Prüfen, ob der Name in dieser Lobby schon existiert (doppelte Namen verhindern)
                $stmtCheck = $pdo->prepare("SELECT id FROM lobby_players WHERE lobby_id = ? AND player_name = ?");
                $stmtCheck->execute([$lobby['id'], $playerName]);

                if ($stmtCheck->fetch()) {
                    $error = 'Dieser Name wird in der Lobby bereits verwendet.';
                } else {
                    // 3. Spieler in die Tabelle eintragen
                    $stmtJoin = $pdo->prepare("INSERT INTO lobby_players (lobby_id, player_name) VALUES (?, ?)");
                    $stmtJoin->execute([$lobby['id'], $playerName]);

                    // 4. Wichtige Daten für den Spieler in die Session schreiben
                    $_SESSION['player_lobby_id'] = $lobby['id'];
                    $_SESSION['player_name']     = $playerName;

                    // Weiterleitung in den Warteraum für Spieler
                    header("Location: host_lobby.php");
                    exit;
                }
            }
        } catch (PDOException $e) {
            $error = 'Datenbankfehler: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz beitreten | damago Quizsystem</title>
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
            <h1>Quizcode eingeben. Direkt mitmachen.</h1>
            <p>
                Gib den Teilnahme-Code ein, den du vom Host erhalten hast.
                Danach wirst du automatisch in die Lobby der Quizrunde weitergeleitet.
            </p>

            <div class="info-list">
                <div>Beitritt nur mit gültigem Teilnahme-Code möglich</div>
            </div>
        </section>

        <section class="auth-card">
            <div class="auth-header">
                <span class="eyebrow">Quiz beitreten</span>
                <h2>Teilnahme-Code</h2>
                <p>Trage den Code ein, den der Host für diese Quizrunde erstellt hat.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form class="auth-form" action="join_quiz.php" method="POST">
                <div class="form-group">
                    <label for="join_code">Teilnahme-Code</label>
                    <input
                        type="text"
                        id="join_code"
                        name="join_code"
                        placeholder="z. B. A7K9X"
                        style="text-transform: uppercase; letter-spacing: 2px;"
                        maxlength="6"
                        required
                    >
                </div>

                <?php if($role === 'guest'): ?>
                    <div class="form-group">
                        <label for="guest_name">Gastname</label>
                        <input
                            type="text"
                            id="guest_name"
                            name="guest_name"
                            placeholder="Gastname"
                            required
                        >
                    </div>

                    <button type="button" class="btn btn-secondary" onclick="(function(){ var f=document.getElementById('guest_name'); if(f){ f.value='Gast-'+Math.floor(1000+Math.random()*9000); } })()">
                        Gastnamen generieren
                    </button>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary">
                    Lobby betreten
                </button>
            </form>

            <?php if($role === 'guest'): ?>
                <div class="auth-links">
                    <p>Du hast bereits ein Konto?</p>
                    <a href="../login.html">Zum Login</a>
                </div>
            <?php endif; ?>

            <div class="secondary-links">
                <a href="dashboard.php">Zurück zum Dashboard</a>
            </div>
        </section>
    </main>

    <script>
        // Gastname beim Laden der Seite automatisch vorausfüllen
        (function() {
            var f = document.getElementById('guest_name');
            if (f) { f.value = 'Gast-' + Math.floor(1000 + Math.random() * 9000); }
        })();
    </script>

</body>
</html>
