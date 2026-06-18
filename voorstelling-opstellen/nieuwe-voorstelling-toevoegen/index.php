<?php
/*
  Auteur       : KadhimH
  Beschrijving : Entrypoint voor het toevoegen van een nieuwe voorstelling.
*/

if (!defined('ALLOW_DB_FAILURE')) {
    define('ALLOW_DB_FAILURE', true);
}

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/NieuweVoorstellingController.php';

$controller = new NieuweVoorstellingController($pdo);
$controller->create();
