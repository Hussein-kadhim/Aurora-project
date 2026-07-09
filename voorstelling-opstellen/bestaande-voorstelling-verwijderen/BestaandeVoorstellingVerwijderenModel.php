<?php

class BestaandeVoorstellingVerwijderenModel {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Zet IsActief op 0 voor de meegegeven voorstelling (soft delete)
     */
    public function verwijderVoorstelling(int $id): bool {
        try {
            // UNHAPPY SCENARIO TRIGGER:
            // Als de naam van de voorstelling "Unhappy Delete" of "Error" bevat, simuleren we dat het mislukt.
            $checkStmt = $this->pdo->prepare("SELECT Naam FROM Voorstelling WHERE Id = :id");
            $checkStmt->execute([':id' => $id]);
            $naam = $checkStmt->fetchColumn();
            
            if ($naam && (stripos($naam, 'Unhappy Delete') !== false || stripos($naam, 'Error') !== false)) {
                return false; // Simuleer dat het verwijderen is mislukt
            }

            $stmt = $this->pdo->prepare("UPDATE Voorstelling SET IsActief = 0 WHERE Id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
