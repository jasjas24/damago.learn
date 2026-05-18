<?php

$host = 'localhost';
$db   = 'damago_quiz';
$user = 'root'; 
$pass = '';     
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     echo "Datenbank erfolgreich verbunden"; 
     header("Location: ../dashboard.php");
} catch (\PDOException $e) {
     // Im echten Betrieb Fehlermeldung loggen, nicht direkt ausgeben
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>