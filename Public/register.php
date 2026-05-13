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
            <img src="damago-logo.png" alt="damago Logo" class="topbar-logo">
        </a>
    </header>

    <main class="auth-layout">
        <section class="auth-info">

            <h1>Wissen testen. Fortschritt sichtbar machen.</h1>
            <p>
                Erstelle dein Benutzerkonto, nimm an interaktiven Quizrunden teil
                und nutze das Quizsystem, um dein Wissen gezielt zu festigen.
            </p>

            <div class="info-list">
                <div>Für Unterricht, Prüfungsvorbereitung und eigenständiges Lernen</div>
            </div>
        </section>

        <section class="auth-card">
            <div class="auth-header">
                <span class="eyebrow">Neues Konto</span>
                <h2>Registrieren</h2>
                <p>Lege dein Benutzerkonto für das Quizsystem an.</p>
            </div>

            <form class="auth-form" action="PHP/check_register.php" method="post">
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
                    <label for="email">E-Mail</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        pattern="^[^\s@]+@[^\s@]+\.[^\s@]+$"
                        placeholder="E-Mail Adresse eingeben"
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

                <button type="submit" class="btn btn-primary">
                    Konto erstellen
                </button>
            </form>

            <div class="auth-links">
                <p>Du hast bereits ein Konto?</p>
                <a href="login.php">Zum Login</a>
            </div>

            <div class="secondary-links">
                <a href="quiz_beitreten.php">Als Gast teilnehmen</a>
            </div>
        </section>
    </main>

</body>
</html>
