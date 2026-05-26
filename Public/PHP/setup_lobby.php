<?php
require_once 'init.php';
require_once 'db.php';

/** @var string $username */ // Kommt vermutlich aus init.php

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 1. Felder abgreifen und in normalen Variablen speichern
    $questionPool  = $_POST['question_pool'] ?? '';
    $questionCount = intval($_POST['question_count'] ?? 10); 
    $timeLimit     = intval($_POST['time_limit'] ?? 30);     
    $pointMode     = $_POST['point_mode'] ?? 'partial';
    $hostPlays     = $_POST['host_plays'] ?? 'no';
    $joinCode      = trim($_POST['join_code'] ?? '');

    if (empty($joinCode)) {
        die("Fehler: Es wurde kein Beitritts-Code übermittelt.");
    }

    // 2. Werte in die Session schreiben (deine bestehende Logik)
    $_SESSION['quiz_setup'] = [
        'code'        => $joinCode,
        'pool'        => $questionPool,
        'count'       => $questionCount,
        'time_limit'  => $timeLimit,
        'point_mode'  => $pointMode,
        'host_plays'  => $hostPlays
    ];

    try {
        // 3. NEU: Lobby in der Datenbank registrieren
        $stmt = $pdo->prepare("
            INSERT INTO quiz_lobbies (join_code, host_name, question_pool, question_count, time_limit, point_mode, host_plays) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $joinCode, 
            $username, 
            $questionPool, 
            $questionCount, 
            $timeLimit, 
            $pointMode, 
            $hostPlays
        ]);
        
        // Die soeben erzeugte ID der Lobby aus der DB holen
        $lobbyId = $pdo->lastInsertId();
        
        // ID in der Session merken (wichtig für das spätere Polling)
        $_SESSION['quiz_setup']['lobby_id'] = $lobbyId;

        // 4. NEU: Falls der Host mitspielt, ihn direkt in 'lobby_players' eintragen
        if ($hostPlays === 'yes') {
            $stmtPlayer = $pdo->prepare("INSERT INTO lobby_players (lobby_id, player_name) VALUES (?, ?)");
            $stmtPlayer->execute([$lobbyId, $username]);
        }

        // 5. Weiterleitung zur Lobby-Ansicht
        header("Location: host_lobby.php?code=" . urlencode($joinCode));
        exit;

    } catch (PDOException $e) {
        die("Datenbankfehler beim Erstellen der Lobby: " . $e->getMessage());
    }

} else {
    // Falls jemand die Datei direkt aufruft, zurück zum Dashboard
    header("Location: dashboard.php");
    exit;
}