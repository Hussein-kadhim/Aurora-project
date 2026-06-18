<?php
/**
 * Meldingsoverzicht — Aurora
 *
 * Entry point voor de Melding MVC module.
 * Laadt de database-configuratie, initialiseert de controller en roept de index-actie aan.
 */

// Laad database configuratie (één niveau omhoog)
require_once __DIR__ . '/../config.php';

// Laad de controller (die ook het model laadt)
require_once __DIR__ . '/MeldingController.php';

// Start de controller
$controller = new MeldingController($pdo);
$controller->index();