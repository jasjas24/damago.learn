<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrierung | damago Quizsystem</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body class="auth-page">
    <header class="topbar">
    <a href="index.php" class="topbar-brand">
        <div class="logo-placeholder">damago</div>
        <span>Quizsystem</span>
    </a>

    <nav class="topbar-nav">
        <a href="index.php">Start</a>
        <a href="login.php">Login</a>
        <a href="register.php">Registrieren</a>
    </nav>
</header>

    <main class="auth-layout">
        <section class="auth-info">
            <div class="brand">
                <div class="brand-mark">d</div>
                <div>
                    <p class="brand-name">damago</p>
                    <p class="brand-subtitle">Quizsystem</p>
                </div>
            </div>

            <h1>Dein persönlicher Lernzugang.</h1>
            <p>
                Erstelle ein Konto, um an Quizrunden teilzunehmen und später
                deine Ergebnisse und Lernhistorie einsehen zu können.
            </p>

            <div class="info-list">
                <div>Eigene Quiz-Historie</div>
                <div>Teilnahme an Kurs-Quizzen</div>
                <div>Klare Rollen für Unterricht und Verwaltung</div>
            </div>
        </section>

        <section class="auth-card">
            <div class="auth-header">
                <span class="eyebrow">Neues Konto</span>
                <h2>Registrieren</h2>
                <p>Lege dein Benutzerkonto für das Quizsystem an.</p>
            </div>

            <form class="auth-form" action="#" method="post">
                <div class="form-group">
                    <label for="username">Benutzername</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Benutzernamen eingeben"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="email">E-Mail-Adresse</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="E-Mail-Adresse eingeben"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password">Passwort</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Passwort eingeben"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password_repeat">Passwort wiederholen</label>
                    <input
                        type="password"
                        id="password_repeat"
                        name="password_repeat"
                        placeholder="Passwort erneut eingeben"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="role">Rolle</label>
                    <select id="role" name="role" required>
                        <option value="">Rolle auswählen</option>
                        <option value="user">User</option>
                        <option value="dozent">Dozent</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    Konto erstellen
                </button>
            </form>

            <div class="auth-links">
                <p>Du hast bereits ein Konto?</p>
                <a href="login.php">Zum Login</a>
            </div>

            <div class="secondary-links">
                <a href="index.php">Zur Startseite</a>
                <a href="quiz_beitreten.php">Als Gast teilnehmen</a>
            </div>
        </section>
    </main>

</body>
</html>