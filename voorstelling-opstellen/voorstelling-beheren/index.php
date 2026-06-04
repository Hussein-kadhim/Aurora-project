<?php
/*
  Auteur       : KadhimH
  Beschrijving : Entrypoint voor het voorstellingenoverzicht dashboard.
*/

if (!defined('ALLOW_DB_FAILURE')) {
    define('ALLOW_DB_FAILURE', true);
}

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/VoorstellingController.php';

$controller = new VoorstellingController($pdo);
$controller->index();
