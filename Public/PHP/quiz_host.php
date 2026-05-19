<?php
session_start();

$username = $_SESSION["username"] ?? "Gast";
$role = $_SESSION["role"] ?? "gast";

/*
    Testversion:
    Der Code wird hier erstmal nur simuliert.
    Später wird er beim Erstellen eines Spiels in der Datenbank gespeichert.
*/

function generateJoinCode($length = 5)
{
    $characters = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
    $code = "";

    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[random_int(0, strlen($characters) - 1)];
    }

    return $code;
}

if (!isset($_SESSION["host_join_code"])) {
    $_SESSION["host_join_code"] = generateJoinCode();
}

$joinCode = $_SESSION["host_join_code"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    /*
        Später:
        Hier wird das Spiel in der Datenbank erstellt.
        Danach kommt der Host in die Host-Lobby.
    */

    $_SESSION["current_game_code"] = $joinCode;
    $_SESSION["is_host"] = true;

    header("Location: host_lobby.php");
    exit;
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

            <div class="host-code-preview">
                <span>Generierter Teilnahme-Code</span>
                <strong><?php echo htmlspecialchars($joinCode); ?></strong>
            </div>
        </section>

        <section class="host-card">
            <div class="auth-header">
                <span class="eyebrow">Host-Einstellungen</span>
                <h2>Quiz starten</h2>
                <p>Lege fest, wie die Quizrunde ablaufen soll.</p>
            </div>

            <form class="auth-form" action="quiz_host.php" method="post">
                <div class="form-group">
                    <label for="question_pool">Fragenpool</label>
                    <select id="question_pool" name="question_pool" required>
                        <option value="">Fragenpool auswählen</option>
                        <option value="programmierung">Grundlagen Programmierung</option>
                        <option value="linux">Grundlagen Linux</option>
                        <option value="netzwerk">Netzwerktechnik</option>
                        <option value="python">Python Grundlagen</option>
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
                        <option value="no" selected>Nein</option>
                        <option value="yes">Ja</option>
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
</html>