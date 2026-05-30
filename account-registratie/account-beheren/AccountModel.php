<?php

class AccountModel {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Haalt alle accounts op, eventueel gefilterd op gebruikersnaam.
     * 
     * @param string $search Optionele zoekterm
     * @return array Lijst met accounts
     */
    public function getAllAccounts($search = '') {
        $query = "
            SELECT 
                g.Id,
                g.Voornaam,
                g.Tussenvoegsel,
                g.Achternaam,
                g.Gebruikersnaam,
                g.IsActief,
                g.IsIngelogd,
                r.Naam AS RolNaam
            FROM Gebruiker g
            LEFT JOIN Rol r ON g.Id = r.GebruikerId AND r.IsActief = 1
        ";

        if ($search !== '') {
            $query .= " WHERE g.Gebruikersnaam LIKE :search";
        }

        $query .= " ORDER BY g.Id ASC";

        try {
            $stmt = $this->pdo->prepare($query);
            if ($search !== '') {
                $stmt->execute(['search' => '%' . $search . '%']);
            } else {
                $stmt->execute();
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log fout of retourneer lege array
            return [];
        }
    }

    /**
     * Controleert het totale aantal accounts in de database.
     * 
     * @return int Aantal accounts
     */
    public function getAccountCount() {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM Gebruiker");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }
}
