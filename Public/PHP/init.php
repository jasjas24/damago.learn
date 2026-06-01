<?php
// Vor session_start() definieren, dass das Cookie beim Schließen des Browsers verfällt

ini_set('session.cookie_lifetime', 0);
ini_set('session.gc_maxlifetime', 1440); // 24 Minuten Inaktivitäts-Puffer auf dem Server

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Standardwerte für Benutzer und Rollen festlegen
// Wir prüfen alle gängigen Keys, damit nichts verloren geht:
$username = $_SESSION["username"] ?? "Gast";
$role = $_SESSION["user_role"] ?? "guest";

// Damit alle nachfolgenden Dateien sauber arbeiten, spiegeln wir den gefundenen Namen in die Session zurück
if ($username !== "Gast") {
    $_SESSION["user_name"] = $username;
    $_SESSION["username"] = $username;
    $_SESSION["player_name"] = $username;
}
