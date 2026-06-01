<?php
require_once __DIR__ . '/config.php';

$nieuweTickets = [
    [1, 1, 1, 80004, 'T-100020003004', '2026-06-01', '10:00:00', 'Gereserveerd', 1, 'Extra test ticket 1'],
    [2, 2, 2, 80005, 'T-100020003005', '2026-06-02', '11:00:00', 'Gereserveerd', 1, 'Extra test ticket 2'],
    [3, 1, 3, 80006, 'T-100020003006', '2026-06-03', '12:00:00', 'Gereserveerd', 1, 'Extra test ticket 3'],
    [1, 2, 1, 80007, 'T-100020003007', '2026-06-04', '14:00:00', 'Gereserveerd', 1, 'Extra test ticket 4'],
    [2, 1, 2, 80008, 'T-100020003008', '2026-06-05', '15:00:00', 'Gereserveerd', 1, 'Extra test ticket 5']
];

$stmt = $pdo->prepare("INSERT INTO Ticket (BezoekerId, VoorstellingId, PrijsId, Nummer, Barcode, Datum, Tijd, Status, IsActief, Opmerking) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$aantal = 0;
foreach ($nieuweTickets as $t) {
    try {
        $stmt->execute($t);
        $aantal++;
    } catch (Exception $e) {
        // Negeren als hij al bestaat (bij meervoudig runnen)
    }
}

echo "Succesvol $aantal nieuwe tickets toegevoegd!\n";
