<?php
require_once 'init.php';
require_once 'db.php';

// Nimmt das Registrierungsformular entgegen und legt daraus ein neues Benutzerkonto an.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 1. Daten bereinigen (trim entfernt Leerzeichen am Anfang/Ende)
    $username       = trim($_POST['username'] ?? '');
    $email          = trim($_POST['email'] ?? '');
    $password       = $_POST['password'] ?? '';
    $passwordRepeat = $_POST['password_repeat'] ?? '';

    // 2. Einfache Validierung (Backend-Sicherheit)
    if (empty($username) || empty($email) || empty($password) || empty($passwordRepeat)) {
        header("Location: ../register.html?error=empty_fields");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../register.html?error=invalid_email");
        exit;
    }

    // Passwort: Mindestlänge und Übereinstimmung serverseitig prüfen (LH 27.1)
    if (strlen($password) < 8) {
        header("Location: ../register.html?error=password_too_short");
        exit;
    }

    if ($password !== $passwordRepeat) {
        header("Location: ../register.html?error=password_mismatch");
        exit;
    }

    try {
        // 3. Prüfen, ob Username oder Email bereits existieren
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->rowCount() > 0) {
            // Nutzer existiert bereits
            header("Location: ../register.html?error=user_exists");
            exit;
        }

        // 4. Passwort sicher hashen (Niemals Klartext!)
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // 5. In die Datenbank einfügen
        $insert = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        if ($insert->execute([$username, $email, $hashedPassword])) {
            // Erfolg! Weiterleitung zum Login mit Erfolgsmeldung
            header("Location: ../login.html?registration=success");
            exit;
        }

    } catch (PDOException $e) {
        // Fehler protokollieren (nicht für User sichtbar machen)
        error_log($e->getMessage());
        header("Location: ../register.html?error=system_error");
        exit;
    }
}