<?php
// 1. Session starten 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Standardwerte für Benutzer und Rollen festlegen
$username = $_SESSION["user_name"] ?? "Gast";
$role = $_SESSION["user_role"] ?? "guest";
