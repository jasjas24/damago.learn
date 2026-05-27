<?php
require_once 'init.php';
require_once 'db.php';

/** @var string $username */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 1. Felder abgreifen
    $questionPool  = $_POST['question_pool'] ?? '';
    $questionCount = intval($_POST['question_count'] ?? 10); 
    $timeLimit     = intval($_POST['time_limit'] ?? 30);    
    $pointMode     = $_POST['point_mode'] ?? 'partial';
    $hostPlaysRaw  = $_POST['host_plays'] ?? 'no'; // 'yes' oder 'no'
    $joinCode      = trim($_POST['join_code'] ?? '');
    

    // Boolean-Flag für einfachere Abfragen in game.php
    $isHostPlaying = ($hostPlaysRaw === 'yes');

    if (empty($joinCode)) {
        die("Fehler: Es wurde kein Beitritts-Code übermittelt.");
    }

    // 2. Werte in die Session schreiben
    $_SESSION['quiz_setup'] = [
        'code'          => $joinCode,
        'pool'          => $questionPool,
        'count'         => $questionCount,
        'time_limit'    => $timeLimit,
        'point_mode'    => $pointMode,
        'host_plays'    => $hostPlaysRaw,      // Für Datenbank/Anzeige
        'is_host_playing' => $isHostPlaying     // WICHTIG für die Logik in game.php
    ];

    try {
        // 3. Lobby in der Datenbank registrieren
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
            $hostPlaysRaw
        ]);
        
        $lobbyId = $pdo->lastInsertId();
        $_SESSION['quiz_setup']['lobby_id'] = $lobbyId;

        // 4. Falls der Host mitspielt, ihn direkt in 'lobby_players' eintragen
        if ($isHostPlaying) {
            $stmtPlayer = $pdo->prepare("INSERT INTO lobby_players (lobby_id, player_name) VALUES (?, ?)");
            $stmtPlayer->execute([$lobbyId, $username]);
        }

        // 5. Weiterleitung
        header("Location: host_lobby.php?code=" . urlencode($joinCode));
        exit;

    } catch (PDOException $e) {
        die("Datenbankfehler beim Erstellen der Lobby: " . $e->getMessage());
    }

} else {
    header("Location: dashboard.php");
    exit;
}