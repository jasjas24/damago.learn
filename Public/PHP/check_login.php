<?php
session_start();
require_once 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $emailInput = trim($_POST['email'] ?? '');
    $passwordInput = $_POST['password'] ?? '';
    echo"hier";

    if (!empty($emailInput) && !empty($passwordInput)) {
        
           
    // SQL-Abfrage nur noch auf das E-Mail-Feld
        $stmt = $pdo->prepare("SELECT id, email, password_hash FROM user WHERE email = ?");
        $stmt->execute([$emailInput]);
        $user = $stmt->fetch();
        

        // Passwort-Check
        if ($user && password_verify($passwordInput, $user['password_hash'])) {
            // Session-Fixation verhindern, neue Session-ID nach Login vergeben
            session_regenerate_id();

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email']; 
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['username'];

            header("Location: dashboard.php");
            exit;
        } else {
            // Fehlerfall
            header("Location: ../login.html?error=1");
            exit;
        }
    }
}