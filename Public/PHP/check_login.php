<?php
require_once 'init.php';
require_once 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $emailInput = trim($_POST['email'] ?? '');
    $passwordInput = $_POST['password'] ?? '';
    

    if (!empty($emailInput) && !empty($passwordInput)) {
        $stmt = $pdo->prepare("
            SELECT u.id, u.email, u.password_hash, r.name, u.username, u.avatar_image_id 
            FROM users u
            INNER JOIN roles r ON u.role_id = r.id
            WHERE u.email = ?
        ");
        $stmt->execute([$emailInput]);
        $user = $stmt->fetch();
        

        // Passwort-Check
        if ($user && password_verify($passwordInput, $user['password_hash'])) {
            // Session-Fixation verhindern, neue Session-ID nach Login vergeben
            session_regenerate_id();

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email']; 
            $_SESSION['user_role'] = $user['name'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_avatar_id'] = $user['avatar_image_id'];

            header("Location: dashboard.php");
            exit;
        } else {
            // Fehlerfall
            header("Location: ../login.html?error=1");
            exit;
        }
    }
}