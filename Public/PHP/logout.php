<?php
require_once 'init.php';

// Meldet den Benutzer ab: Session leeren, Cookie entwerten und zurück zum Login.
$_SESSION = [];

// Falls Cookies genutzt werden, das Session-Cookie aktiv ablaufen lassen
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Session endgültig zerstören und zur Anmeldung leiten
session_destroy();
header("Location: ../login.html");

exit;