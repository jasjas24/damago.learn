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
    <a href="index.html" class="topbar-brand">
        <img src="damago-logo.png" alt="damago Logo" class="topbar-logo">
        <span>Quizsystem</span>
    </a>

    <nav class="topbar-nav">
        <a href="index.html">Start</a>
        <a href="login.html">Login</a>
        <a href="register.html">Registrieren</a>
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

            <form class="auth-form" action="#" method="post">
                <div class="form-group">
                    <label for="login">E-Mail oder Benutzername</label>
                   <input type="email" pattern="^[^\s@]+@[^\s@]+\.[^\s@]+$" placeholder="E-Mail Adresse eingeben">
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