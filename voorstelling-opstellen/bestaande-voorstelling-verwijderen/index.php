<?php
// bestaande-voorstelling-verwijderen/index.php

if (!defined('ALLOW_DB_FAILURE')) {
    define('ALLOW_DB_FAILURE', true);
}

try {
    require_once __DIR__ . '/../../config.php';
    require_once __DIR__ . '/BestaandeVoorstellingVerwijderenController.php';

    $controller = new BestaandeVoorstellingVerwijderenController($pdo);
    $controller->delete();
} catch (PDOException $e) {
    // Database is offline, stuur terug met een foutmelding
    header('Location: ../voorstelling-beheren/index.php?error=delete_failed');
    exit;
}
