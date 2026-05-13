<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | damago Quizsystem</title>
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

            <h1>Learning by Damago.</h1>
            <p>
                Melde dich an, um Quizrunden zu starten, an Unterrichtsquizzen
                teilzunehmen oder deine Lernfortschritte einzusehen.
            </p>

            <div class="info-list">
                <div>Unterrichtsquiz für Kurse und Umschulungen</div>
            </div>
        </section>

        <section class="auth-card">
            <div class="auth-header">
                <span class="eyebrow">Willkommen zurück</span>
                <h2>Login</h2>
                <p>Melde dich mit deinem Benutzerkonto an.</p>
            </div>

            <form class="auth-form" action="PHP/check_login.php" method="post">
                <div class="form-group">
                    <label for="login">E-Mail</label>
                   <input type="email" pattern="^[^\s@]+@[^\s@]+\.[^\s@]+$" placeholder="E-Mail Adresse eingeben" name="E-Mail">
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

                <button type="submit" value="Submit" class="btn btn-primary">
                    Einloggen
                </button>
            </form>

            <div class="auth-links">
                <p>Noch kein Konto?</p>
                <a href="register.php">Jetzt registrieren</a>
            </div>

            <div class="secondary-links">
                <a href="quiz_beitreten.php">Als Gast teilnehmen</a>
            </div>
        </section>
    </main>

</body>
</html>