<?php
// Alles rund um den geheimen Host-Token: erzeugen, prüfen und damit die Host-Session wiederherstellen.
// In der Datenbank steht immer nur der Hash, der Klartext bleibt beim Host in der Session.

if (!function_exists('host_token_hash')) {
    // Macht aus dem Klartext-Token den Hash, der in der Datenbank gespeichert wird.
    function host_token_hash(string $token): string
    {
        return hash('sha256', $token);
    }
}

if (!function_exists('generate_host_token')) {
    // Erzeugt einen neuen, zufälligen geheimen Host-Token.
    function generate_host_token(): string
    {
        return bin2hex(random_bytes(16));
    }
}

if (!function_exists('verify_host_token')) {
    // Prüft, ob das übergebene Token zum gespeicherten Hash der Lobby passt.
    function verify_host_token(PDO $pdo, $lobby_id, ?string $token): bool
    {
        if (empty($lobby_id) || empty($token)) {
            return false;
        }
        $stmt = $pdo->prepare("SELECT host_token_hash FROM quiz_lobbies WHERE id = ?");
        $stmt->execute([$lobby_id]);
        $stored = $stmt->fetchColumn();
        if (!$stored) {
            return false;
        }
        return hash_equals((string)$stored, host_token_hash($token));
    }
}

if (!function_exists('find_lobby_by_host_token')) {
    // Sucht die Lobby über den Token-Hash, damit wir die Host-Session wiederherstellen können.
    function find_lobby_by_host_token(PDO $pdo, ?string $token)
    {
        if (empty($token)) {
            return false;
        }
        $stmt = $pdo->prepare("SELECT * FROM quiz_lobbies WHERE host_token_hash = ?");
        $stmt->execute([host_token_hash($token)]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('restore_host_session')) {
    // Stellt die Host-Session aus der Lobby-Zeile und dem Klartext-Token wieder her.
    function restore_host_session(array $lobby, string $token): void
    {
        $isHostPlaying = ((int)$lobby['host_plays'] === 1);

        $_SESSION['quiz_setup'] = [
            'code'            => $lobby['join_code'],
            'pool'            => $lobby['question_pool'],
            'count'           => (int)$lobby['question_count'],
            'time_limit'      => (int)$lobby['time_limit'],
            'point_mode'      => $lobby['point_mode'],
            'host_plays'      => $isHostPlaying ? 'yes' : 'no',
            'is_host_playing' => $isHostPlaying,
            'avatar'          => '',
            'lobby_id'        => (int)$lobby['id'],
            'host_token'      => $token,
        ];

        // Gespeicherte Frage- und Antwortreihenfolge laden, die für alle gleich ist.
        $decoded = !empty($lobby['quiz_data']) ? json_decode($lobby['quiz_data'], true) : null;
        if (!empty($decoded)) {
            $_SESSION['quiz_questions'] = $decoded;
        }
        $_SESSION['current_question_index'] = (int)$lobby['current_question_index'];
        if (!isset($_SESSION['quiz_score'])) {
            $_SESSION['quiz_score'] = 0;
        }
        // Damit der Host in der Teilnehmerliste richtig markiert werden kann.
        if (empty($_SESSION['username'])) {
            $_SESSION['username'] = $lobby['host_name'];
        }
    }
}
