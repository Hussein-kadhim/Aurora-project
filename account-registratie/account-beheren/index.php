<?php
// Laad database configuratie
require_once __DIR__ . '/../../config.php';

// Laad de controller
require_once __DIR__ . '/AccountController.php';

// Initialiseer en start de controller
$controller = new AccountController($pdo);
$controller->index();
