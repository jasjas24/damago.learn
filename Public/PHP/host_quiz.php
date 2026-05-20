<?php
require_once 'init.php';
require_once 'db.php';

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
    <link rel="stylesheet" href="../CSS/quiz_host.css">
</head>
<body class="auth-page">

    <header class="topbar">
        <a href="dashboard.php" class="topbar-brand">
            <img src="../damago-logo.png" alt="damago Logo" class="topbar-logo">
        </a>

        <div class="topbar-account">
            <span class="account-name">
                <?php echo htmlspecialchars($username); ?>
            </span>

            <a href="logout.php" class="logout-button">
                logout
            </a>
        </div>
    </header>

    <main class="host-layout">

            <p>
                Wähle die Einstellungen für deine Quizrunde aus.
                Danach erhalten die Teilnehmer einen Teilnahme-Code,
                mit dem sie der Lobby beitreten können.
            </p>

            
        

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
                            <?php 
                                // Macht den ersten Buchstaben groß fürs Auge (z. B. 'Linux' statt 'linux')
                                echo htmlspecialchars(ucfirst($p['name'])); 
                            ?>
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
                        <option value="yes"selected>Ja</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    Lobby erstellen
                </button>
            </form>

            <div class="host-back">
                <p>Du möchtest zurück?</p>
                <a href="dashboard.php">Zurück zum Dashboard</a>
            </div>
        </section>
    </main>

</body>
<script>
document.getElementById('quizForm').addEventListener('submit', function(event) {
    // 1. Zeichenpool für den Code (ohne leicht verwechselbare Zeichen wie O, 0, 1, I)
    const chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
    let generatedCode = "";
    
    // 2. Einen 5-stelligen Code generieren
    for (let i = 0; i < 5; i++) {
        generatedCode += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    
    // 3. Den generierten Code in das versteckte Input-Feld schreiben
    document.getElementById('join_code').value = generatedCode;
    

});
</script>
</html>