<?php
// bestande-voorstelling-wijzigen/index.php

// Inladen database configuratie (meestal in ../../config.php of ../../../config.php)
// Aurora project gebruikt vaak $pdo object in de controller
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/BestaandeVoorstellingWijzigenController.php';

$controller = new BestaandeVoorstellingWijzigenController($pdo);
$controller->edit();
