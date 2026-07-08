<?php

class BestaandeVoorstellingWijzigenModel {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Haal alle gegevens van een specifieke voorstelling op op basis van het ID
     */
    public function getVoorstellingById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM Voorstelling WHERE Id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Update een bestaande voorstelling in de database
     */
    public function updateVoorstelling(
        int    $id,
        string $naam,
        string $beschrijving,
        string $datum,
        string $tijd,
        int    $maxAantalTickets,
        string $beschikbaarheid
    ): void {
        $query = "
            UPDATE Voorstelling
            SET Naam = :naam,
                Beschrijving = :beschrijving,
                Datum = :datum,
                Tijd = :tijd,
                MaxAantalTickets = :maxTickets,
                Beschikbaarheid = :beschikbaarheid
            WHERE Id = :id
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ':id'              => $id,
            ':naam'            => $naam,
            ':beschrijving'    => $beschrijving,
            ':datum'           => $datum,
            ':tijd'            => $tijd,
            ':maxTickets'      => $maxAantalTickets,
            ':beschikbaarheid' => $beschikbaarheid,
        ]);
    }
}
