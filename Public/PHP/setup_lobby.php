<?php
require_once 'init.php';
require_once 'db.php';
require_once 'avatars.php';
require_once 'host_auth.php';

/** @var string $username */

/**
 * Erzeugt serverseitig einen eindeutigen Teilnahme-Code (LH 8.3):
 * 6 Zeichen, nur Großbuchstaben/Ziffern ohne verwechselbare 0/O/1/I,
 * mit Eindeutigkeitsprüfung gegen bestehende Lobbys (passt zu varchar(6)).
 */
if (!function_exists('damago_generate_join_code')) {
    function damago_generate_join_code(PDO $pdo): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // ohne 0/O/1/I
        $stmt  = $pdo->prepare("SELECT 1 FROM quiz_lobbies WHERE join_code = ? LIMIT 1");
        for ($attempt = 0; $attempt < 50; $attempt++) {
            $code = '';
            for ($i = 0; $i < 6; $i++) {
                $code .= $chars[random_int(0, strlen($chars) - 1)];
            }
            $stmt->execute([$code]);
            if (!$stmt->fetchColumn()) {
                return $code;
            }
        }
        throw new RuntimeException('Es konnte kein freier Teilnahme-Code erzeugt werden.');
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 1. Felder abgreifen
    $questionPool  = $_POST['question_pool'] ?? '';
    $questionCount = intval($_POST['question_count'] ?? 10);
    $timeLimit     = intval($_POST['time_limit'] ?? 30);
    $pointMode     = $_POST['point_mode'] ?? 'partial';
    $hostPlaysRaw  = $_POST['host_plays'] ?? 'no'; // 'yes' oder 'no'
    $avatar        = trim($_POST['avatar'] ?? '');


    // Boolean-Flag für einfachere Abfragen in game.php
    $isHostPlaying = ($hostPlaysRaw === 'yes');

    // Geheimen Host-Token erzeugen (Klartext nur in Session, DB speichert nur den Hash).
    $hostToken = generate_host_token();

    // Avatar ist nur Pflicht, wenn der Host selbst mitspielt.
    if ($isHostPlaying && !damago_is_valid_avatar($avatar)) {
        die("Fehler: Bitte wähle einen gültigen Avatar aus.");
    }

    // Pool-Größe prüfen (LH 11.3): Spiel darf nicht starten, wenn der Pool weniger
    // AKTIVE Fragen enthält als gewählt. Mit verständlicher Meldung zurück zum Formular.
    $stmtActiveCount = $pdo->prepare("
        SELECT COUNT(*)
        FROM questions q
        INNER JOIN question_pools p ON p.id = q.question_pool_id
        WHERE p.name = ? AND q.is_active = 1
    ");
    $stmtActiveCount->execute([$questionPool]);
    $activeCount = (int)$stmtActiveCount->fetchColumn();

    if ($questionCount < 1 || $activeCount < $questionCount) {
        $_SESSION['host_error'] = "Der ausgewählte Fragenpool enthält nur {$activeCount} aktive Frage(n). Du hast {$questionCount} Fragen ausgewählt.";
        $_SESSION['host_form']  = [
            'question_pool'  => $questionPool,
            'question_count' => $questionCount,
            'time_limit'     => $timeLimit,
            'point_mode'     => $pointMode,
            'host_plays'     => $hostPlaysRaw,
        ];
        header("Location: host_quiz.php");
        exit;
    }

    // Teilnahme-Code serverseitig erzeugen (eindeutig, sicherer Zeichensatz, LH 8.3)
    $joinCode = damago_generate_join_code($pdo);

    // 2. Werte in die Session schreiben
    $_SESSION['quiz_setup'] = [
        'code'          => $joinCode,
        'pool'          => $questionPool,
        'count'         => $questionCount,
        'time_limit'    => $timeLimit,
        'point_mode'    => $pointMode,
        'host_plays'    => $hostPlaysRaw,      // Für Datenbank/Anzeige
        'is_host_playing' => $isHostPlaying,    // WICHTIG für die Logik in game.php
        'avatar'        => $avatar,             // Avatar des Hosts (falls er mitspielt)
        'host_token'    => $hostToken           // Klartext-Token für Host-Link/Host-Aktionen
    ];

    // Alte Spiel-Daten einer vorherigen Runde leeren, damit host_lobby.php die
    // Fragen für den NEU gewählten Pool frisch lädt (sonst bleiben alte Fragen stehen).
    unset($_SESSION['quiz_questions']);
    unset($_SESSION['current_question_index']);
    unset($_SESSION['quiz_score']);
    unset($_SESSION['last_result']);
    unset($_SESSION['waiting_for_reveal']);

    try {
        // 3. Lobby in der Datenbank registrieren (nur der Hash des Host-Tokens wird gespeichert)
        $stmt = $pdo->prepare("
            INSERT INTO quiz_lobbies (join_code, host_name, question_pool, question_count, time_limit, point_mode, host_plays, host_token_hash, host_user_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $joinCode,
            $username,
            $questionPool,
            $questionCount,
            $timeLimit,
            $pointMode,
            $isHostPlaying ? 1 : 0,
            host_token_hash($hostToken),
            $_SESSION['user_id'] ?? null   // registrierter Host: Konto-Zuordnung; Gast: NULL
        ]);
        
        $lobbyId = $pdo->lastInsertId();
        $_SESSION['quiz_setup']['lobby_id'] = $lobbyId;

        // 4. Falls der Host mitspielt, ihn direkt in 'lobby_players' eintragen
        if ($isHostPlaying) {
            $stmtPlayer = $pdo->prepare("INSERT INTO lobby_players (lobby_id, player_name, avatar, user_id) VALUES (?, ?, ?, ?)");
            $stmtPlayer->execute([$lobbyId, $username, $avatar, $_SESSION['user_id'] ?? null]);
        }

        // 5. Weiterleitung (Host-Link enthält den Token, damit der Host zurückkehren kann)
        header("Location: host_lobby.php?code=" . urlencode($joinCode) . "&host_token=" . urlencode($hostToken));
        exit;

    } catch (PDOException $e) {
        die("Datenbankfehler beim Erstellen der Lobby: " . $e->getMessage());
    }

} else {
    header("Location: dashboard.php");
    exit;
}