<?php
// Sta toe dat de applicatie de DB fout opvangt
if (!defined('ALLOW_DB_FAILURE')) {
    define('ALLOW_DB_FAILURE', true);
}

// Laad database configuratie
require_once __DIR__ . '/../../config.php';

// Laad de controller
require_once __DIR__ . '/AccountController.php';

// Initialiseer en start de controller
$controller = new AccountController($pdo);
$controller->index();
