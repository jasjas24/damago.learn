<?php
// Vor session_start() definieren, dass das Cookie beim Schließen des Browsers verfällt

ini_set('session.cookie_lifetime', 0);
ini_set('session.gc_maxlifetime', 1440); // 24 Minuten Inaktivitäts-Puffer auf dem Server

// Session-Cookie absichern: nicht per JavaScript lesbar (XSS) + SameSite gegen CSRF.
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CSRF-Schutz: einmal pro Session ein geheimes Token anlegen, das wir später in die Formulare legen.
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Gibt das versteckte Token-Feld aus, das in jedes Admin-Formular gehört.
if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="csrf_token" value="'
            . htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES) . '">';
    }
}

// Prüft bei einem POST, ob das mitgeschickte Token zum Session-Token passt, und bricht sonst ab.
if (!function_exists('csrf_check')) {
    function csrf_check(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            return;
        }
        $token = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || !is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            exit('Sicherheits-Token (CSRF) ungültig oder fehlend. Bitte die Seite neu laden und erneut versuchen.');
        }
    }
}

// 2. Standardwerte für Benutzer und Rollen festlegen
// Wir prüfen alle gängigen Keys, damit nichts verloren geht:
$username = $_SESSION["username"] ?? "Gast";
$role = $_SESSION["user_role"] ?? "guest";

// Damit alle nachfolgenden Dateien sauber arbeiten, spiegeln wir den gefundenen Namen in die Session zurück
if ($username !== "Gast") {
   # $_SESSION["user_name"] = $username;
    $_SESSION["username"] = $username;
   # $_SESSION["player_name"] = $username;
}
