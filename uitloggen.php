<?php
session_start();
require_once __DIR__ . '/config.php';
 
if (!empty($_SESSION['gebruiker_id'])) {
    $update = $pdo->prepare("UPDATE gebruiker SET IsIngelogd = 0, Uitgelogd = CURRENT_TIMESTAMP WHERE Id = ?");
    $update->execute([$_SESSION['gebruiker_id']]);
}
 
session_destroy();
header('Location: login.php');
exit();
 
 