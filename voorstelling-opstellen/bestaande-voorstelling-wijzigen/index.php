<?php
// bestaande-voorstelling-wijzigen/index.php

if (!defined('ALLOW_DB_FAILURE')) {
    define('ALLOW_DB_FAILURE', true);
}

try {
    require_once __DIR__ . '/../../config.php';
    require_once __DIR__ . '/BestaandeVoorstellingWijzigenController.php';

    $controller = new BestaandeVoorstellingWijzigenController($pdo);
    $controller->edit();
} catch (PDOException $e) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $dbFout = true;
    $foutmelding = 'Er is een fout opgetreden. Probeer het later opnieuw.';
    $succes = false;
    $errors = [];
    $voorstellingId = $_POST['id'] ?? $_GET['id'] ?? null;
    $voorstelling = null;
    require_once __DIR__ . '/views/edit.php';
}
