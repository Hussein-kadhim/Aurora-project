<?php
// Sta toe dat de applicatie de DB fout opvangt
if (!defined('ALLOW_DB_FAILURE')) {
    define('ALLOW_DB_FAILURE', true);
}

try {
    // Laad database configuratie
    require_once __DIR__ . '/../../config.php';

    // Laad de controller
    require_once __DIR__ . '/TicketController.php';

    // Initialiseer en start de controller
    $controller = new TicketController($pdo);
    $controller->delete();
} catch (PDOException $e) {
    header('Location: index.php?error_delete=1');
    exit();
}
