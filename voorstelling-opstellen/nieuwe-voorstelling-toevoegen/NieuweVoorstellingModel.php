<?php

class NieuweVoorstellingModel {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Haal het MedewerkerId op via de GebruikerId (uit sessie)
     */
    public function getMedewerkerIdByGebruikerId(int $gebruikerId): ?int {
        $stmt = $this->pdo->prepare(
            "SELECT Id FROM Medewerker WHERE GebruikerId = :gid AND IsActief = 1 LIMIT 1"
        );
        $stmt->execute([':gid' => $gebruikerId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['Id'] : null;
    }

    /**
     * Voeg een nieuwe voorstelling in de database in
     */
    public function insertVoorstelling(
        int    $medewerkerId,
        string $naam,
        string $beschrijving,
        string $datum,
        string $tijd,
        int    $maxAantalTickets,
        string $beschikbaarheid
    ): void {
        $query = "
            INSERT INTO Voorstelling
                (MedewerkerId, Naam, Beschrijving, Datum, Tijd, MaxAantalTickets, Beschikbaarheid, IsActief)
            VALUES
                (:medewerkerId, :naam, :beschrijving, :datum, :tijd, :maxTickets, :beschikbaarheid, 1)
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ':medewerkerId'    => $medewerkerId,
            ':naam'            => $naam,
            ':beschrijving'    => $beschrijving,
            ':datum'           => $datum,
            ':tijd'            => $tijd,
            ':maxTickets'      => $maxAantalTickets,
            ':beschikbaarheid' => $beschikbaarheid,
        ]);
    }
}
