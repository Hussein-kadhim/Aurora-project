<?php
require_once __DIR__ . '/config.php';
$stmt = $pdo->query("SELECT Barcode, Status FROM Ticket");
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($tickets as $t) {
    echo $t['Barcode'] . " => " . $t['Status'] . "\n";
}
