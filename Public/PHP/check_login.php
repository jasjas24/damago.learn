<?php
session_start();
require_once 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $emailInput = trim($_POST['login'] ?? '');
    $passwordInput = $_POST['password'] ?? '';

    if (!empty($emailInput) && !empty($passwordInput)) {
        
           
    // SQL-Abfrage nur noch auf das E-Mail-Feld
        $stmt = $pdo->prepare("SELECT id, email, password_hash FROM users WHERE email = ?");
        $stmt->execute([$emailInput]);
        $user = $stmt->fetch();

        // Passwort-Check
        if ($user && password_verify($passwordInput, $user['password_hash'])) {
            // Session-Fixation verhindern
            session_regenerate_id();

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email']; // E-Mail statt Username speichern

            header("Location: ../dashboard.php");
            exit;
        } else {
            // Fehlerfall
            header("Location: ../login.html?error=1");
            exit;
        }
    }
}