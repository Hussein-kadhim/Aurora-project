<?php
/*
  Auteur       : KadhimH
  Beschrijving : Entrypoint voor het voorstellingenoverzicht dashboard.
*/

if (!defined('ALLOW_DB_FAILURE')) {
    define('ALLOW_DB_FAILURE', true);
}

try {
    require_once __DIR__ . '/../../config.php';
    require_once __DIR__ . '/VoorstellingController.php';

    $controller = new VoorstellingController($pdo);
    $controller->index();
} catch (PDOException $e) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $dbFout = true;
    $foutmelding = 'Er is een fout opgetreden. Probeer het later opnieuw.';
    $voorstellingen = [];
    $totalCount = 0;
    $search = trim($_GET['search'] ?? '');
    require_once __DIR__ . '/views/index.php';
}
