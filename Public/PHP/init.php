<?php
// Vor session_start() definieren, dass das Cookie beim Schließen des Browsers verfällt

ini_set('session.cookie_lifetime', 0);
ini_set('session.gc_maxlifetime', 1440); // 24 Minuten Inaktivitäts-Puffer auf dem Server

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Standardwerte für Benutzer und Rollen festlegen
$username = $_SESSION["user_name"] ?? "Gast";
$role = $_SESSION["user_role"] ?? "guest";
