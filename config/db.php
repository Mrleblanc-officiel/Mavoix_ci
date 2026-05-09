<?php
$host = 'localhost';
$db   = 'mavoix_ci';
$user = 'root';       // ou ton utilisateur SQL
$pass = 'Bamorysalif00@'; // ton mot de passe SQL
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // erreurs en exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // résultats en tableau associatif
    PDO::ATTR_EMULATE_PREPARES   => false,                  // vraie préparation côté serveur
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>