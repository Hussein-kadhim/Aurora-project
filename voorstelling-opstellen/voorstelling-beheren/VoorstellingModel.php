<?php
class VoorstellingModel {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Haal alle actieve voorstellingen op, met optionele zoekterm
     */
    public function getAllVoorstellingen($search = '') {
        $query = "
            SELECT v.Id, v.Naam, v.Beschrijving, v.Datum, v.Tijd, v.MaxAantalTickets, v.Beschikbaarheid, v.DatumAangemaakt
            FROM Voorstelling v
            WHERE v.IsActief = 1
        ";
        
        $params = [];
        
        if (!empty($search)) {
            $query .= " AND (v.Naam LIKE :search OR v.Beschikbaarheid LIKE :search OR v.Datum LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        
        $query .= " ORDER BY v.Datum ASC, v.Tijd ASC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Telt het totaal aantal actieve voorstellingen (voor lege staten)
     */
    public function getVoorstellingCount() {
        $query = "SELECT COUNT(*) FROM Voorstelling WHERE IsActief = 1";
        return (int) $this->pdo->query($query)->fetchColumn();
    }
}
