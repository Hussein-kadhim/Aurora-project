<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('ALLOW_DB_FAILURE')) {
    define('ALLOW_DB_FAILURE', true);
}

try {
    require_once __DIR__ . '/../../config.php';
    require_once __DIR__ . '/MedewerkerController.php';

    $controller = new MedewerkerController($pdo);
    
    $action = $_GET['action'] ?? 'index';
    if ($action === 'create') {
        $controller->create();
    } elseif ($action === 'edit') {
        $controller->edit();
    } elseif ($action === 'delete') {
        $controller->delete();
    } else {
        $controller->index();
    }
} catch (PDOException $e) {
    $dbFout = true;
    $foutmelding = 'De server is momenteel niet bereikbaar';
    $search = '';
    $medewerkers = [];
    $totalCount = 0;
    require_once __DIR__ . '/views/index.php';
}
