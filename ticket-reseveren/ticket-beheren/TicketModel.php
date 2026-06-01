<?php

class TicketModel {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Haal alle actieve tickets op, optioneel gefilterd op zoekterm.
     */
    public function getAllTickets($search = '') {
        $sql = "
            SELECT 
                t.Nummer AS TicketNummer,
                v.Naam AS VoorstellingNaam,
                g.Voornaam AS BezoekerVoornaam,
                g.Tussenvoegsel AS BezoekerTussenvoegsel,
                g.Achternaam AS BezoekerAchternaam,
                t.Status AS TicketStatus
            FROM Ticket t
            JOIN Bezoeker b ON t.BezoekerId = b.Id
            JOIN Gebruiker g ON b.GebruikerId = g.Id
            JOIN Voorstelling v ON t.VoorstellingId = v.Id
            WHERE t.IsActief = 1
        ";

        if ($search !== '') {
            $sql .= " AND (
                t.Nummer LIKE :search 
                OR v.Naam LIKE :search 
                OR g.Voornaam LIKE :search 
                OR g.Achternaam LIKE :search
                OR CONCAT(g.Voornaam, ' ', IFNULL(g.Tussenvoegsel, ''), ' ', g.Achternaam) LIKE :search
            )";
        }

        $sql .= " ORDER BY t.Nummer DESC";

        $stmt = $this->pdo->prepare($sql);

        if ($search !== '') {
            $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Haal het totale aantal actieve tickets op (zonder filter).
     */
    public function getTicketCount() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Ticket WHERE IsActief = 1");
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }
}
