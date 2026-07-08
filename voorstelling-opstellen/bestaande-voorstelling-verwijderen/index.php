<?php
// bestande-voorstelling-verwijderen/index.php

// Inladen database configuratie
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/BestaandeVoorstellingVerwijderenController.php';

$controller = new BestaandeVoorstellingVerwijderenController($pdo);
$controller->delete();
