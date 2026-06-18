<?php


$host     = "localhost";
$dbname   = "aurora";   
$user     = "root";
$password = "";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password, $options);
} catch (PDOException $e) {
    if (defined('ALLOW_DB_FAILURE') && ALLOW_DB_FAILURE === true) {
        throw $e;
    }
    die("Connectie mislukt: " . $e->getMessage());
}
