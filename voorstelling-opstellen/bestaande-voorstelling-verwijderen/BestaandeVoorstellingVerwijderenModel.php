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
            $stmt = $this->pdo->prepare("UPDATE Voorstelling SET IsActief = 0 WHERE Id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
