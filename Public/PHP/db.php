<?php
// Hier bauen wir die Verbindung zur Datenbank auf und legen sie in $pdo ab.

$host = 'localhost';
$db   = 'damago_quiz';
$user = 'root';
$pass = '';
$port ='3306';
$charset = 'utf8mb4';

// Verbindungsdaten für PDO zusammensetzen
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
// PDO so einstellen, dass Fehler geworfen werden, Ergebnisse als Array kommen und echte Prepared Statements genutzt werden
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     // Verbindung herstellen
     $pdo = new PDO($dsn, $user, $pass, $options);

} catch (\PDOException $e) {
     // Klappt die Verbindung nicht, brechen wir mit der Fehlermeldung ab
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
