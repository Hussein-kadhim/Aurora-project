<?php

class MedewerkerModel {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Haalt alle actieve medewerkers op, eventueel gefilterd op zoekterm.
     * 
     * @param string $search Optionele zoekterm
     * @return array Lijst met medewerkers
     */
    public function getAllMedewerkers($search = '') {
        $query = "
            SELECT
                m.Id                AS MedewerkerId,
                m.Nummer            AS MedewerkerNummer,
                m.Medewerkersoort,
                m.IsActief          AS MedewerkerActief,
                g.Voornaam,
                g.Tussenvoegsel,
                g.Achternaam,
                g.Gebruikersnaam,
                g.IsIngelogd,
                g.Ingelogd,
                c.Email,
                c.Mobiel,
                r.Naam              AS Rol,
                m.DatumAangemaakt
            FROM medewerker m
            JOIN gebruiker  g ON g.Id = m.GebruikerId
            LEFT JOIN contact c ON c.GebruikerId = g.Id AND c.IsActief = 1
            LEFT JOIN rol     r ON r.GebruikerId = g.Id AND r.IsActief = 1
            WHERE m.IsActief = 1
        ";

        if ($search !== '') {
            $query .= " AND (
                g.Voornaam LIKE :search 
                OR g.Achternaam LIKE :search 
                OR g.Gebruikersnaam LIKE :search 
                OR m.Nummer LIKE :search 
                OR m.Medewerkersoort LIKE :search
            )";
        }

        $query .= " ORDER BY m.Nummer ASC";

        try {
            $stmt = $this->pdo->prepare($query);
            if ($search !== '') {
                $stmt->execute(['search' => '%' . $search . '%']);
            } else {
                $stmt->execute();
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            if (defined('ALLOW_DB_FAILURE') && ALLOW_DB_FAILURE === true) {
                throw $e;
            }
            return [];
        }
    }

    /**
     * Controleert het totale aantal actieve medewerkers.
     * 
     * @return int Aantal medewerkers
     */
    public function getMedewerkerCount() {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM medewerker WHERE IsActief = 1");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            if (defined('ALLOW_DB_FAILURE') && ALLOW_DB_FAILURE === true) {
                throw $e;
            }
            return 0;
        }
    }

    /**
     * Controleert of een e-mailadres / gebruikersnaam al bestaat in het systeem.
     * 
     * @param string $email
     * @return bool
     */
    public function gebruikersnaamBestaat(string $email): bool {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Gebruiker WHERE Gebruikersnaam = ?");
        $stmt->execute([$email]);
        if ((int)$stmt->fetchColumn() > 0) {
            return true;
        }

        $stmt2 = $this->pdo->prepare("SELECT COUNT(*) FROM Contact WHERE Email = ?");
        $stmt2->execute([$email]);
        if ((int)$stmt2->fetchColumn() > 0) {
            return true;
        }

        return false;
    }
}
