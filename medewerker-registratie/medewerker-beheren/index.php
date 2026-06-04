<?php
/*
  Auteur       : KadhimH
  Datum        : 2026-06-04
  Beschrijving : Entrypoint voor het medewerkersoverzicht dashboard.
  Opmerkingen  : Laadt de database configuratie, de controller en start de actie.
*/

if (!defined('ALLOW_DB_FAILURE')) {
    define('ALLOW_DB_FAILURE', true);
}

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/MedewerkerController.php';

$controller = new MedewerkerController($pdo);
$controller->index();
