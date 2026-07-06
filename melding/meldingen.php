<?php
/**
 * Meldingsoverzicht — Aurora
 *
 * Entry point voor de Melding MVC module.
 * Laadt de database-configuratie, initialiseert de controller en roept de index-actie aan.
 */

// Sta toe dat de applicatie de DB fout opvangt
if (!defined('ALLOW_DB_FAILURE')) {
    define('ALLOW_DB_FAILURE', true);
}

try {
    // Laad database configuratie (één niveau omhoog)
    require_once __DIR__ . '/../config.php';

    // Laad de controller (die ook het model laadt)
    require_once __DIR__ . '/MeldingController.php';

    // Start de controller
    $controller = new MeldingController($pdo);

    $action = $_GET['action'] ?? ($_POST['action'] ?? 'index');
    if ($action === 'nieuw') {
        $controller->nieuw();
    } elseif ($action === 'feedback') {
        $controller->feedback();
    } else {
        $controller->index();
    }
} catch (PDOException $e) {
    $techError = true;
    $filterType = '';
    $filterStatus = '';
    $filterDate = '';
    $meldingen = [];
    $totalFiltered = 0;
    $totalPages = 1;
    $page = 1;
    require_once __DIR__ . '/views/meldingen.php';
}