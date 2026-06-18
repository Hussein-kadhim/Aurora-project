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

        $stmt = $this->pdo->prepare($query);
        if ($search !== '') {
            $stmt->execute(['search' => '%' . $search . '%']);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Controleert het totale aantal accounts in de database.
     * 
     * @return int Aantal accounts
     */
    public function getAccountCount() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM Gebruiker");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Voegt een nieuw account toe in de database (inclusief Rol en Contact gegevens).
     * 
     * @param array $data Accountgegevens
     * @return bool True bij succes, false bij falen
     */
    public function createAccount(array $data) {
        $this->pdo->beginTransaction();

        try {
            // 1. Gebruiker toevoegen
            $stmtGebruiker = $this->pdo->prepare("
                INSERT INTO Gebruiker (Voornaam, Tussenvoegsel, Achternaam, Gebruikersnaam, Wachtwoord)
                VALUES (:voornaam, :tussenvoegsel, :achternaam, :gebruikersnaam, :wachtwoord)
            ");
            $stmtGebruiker->execute([
                'voornaam'       => $data['Voornaam'],
                'tussenvoegsel'  => empty($data['Tussenvoegsel']) ? null : $data['Tussenvoegsel'],
                'achternaam'     => $data['Achternaam'],
                'gebruikersnaam' => $data['Gebruikersnaam'],
                'wachtwoord'     => $data['Wachtwoord']
            ]);

            $gebruikerId = $this->pdo->lastInsertId();

            // 2. Rol toevoegen
            $stmtRol = $this->pdo->prepare("
                INSERT INTO Rol (GebruikerId, Naam)
                VALUES (:gebruikerId, :naam)
            ");
            $stmtRol->execute([
                'gebruikerId' => $gebruikerId,
                'naam'        => $data['Rol']
            ]);

            // 3. Contact toevoegen
            $stmtContact = $this->pdo->prepare("
                INSERT INTO Contact (GebruikerId, Email, Mobiel)
                VALUES (:gebruikerId, :email, :mobiel)
            ");
            $stmtContact->execute([
                'gebruikerId' => $gebruikerId,
                'email'       => $data['Email'],
                'mobiel'      => $data['Mobiel']
            ]);

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Controleert of een e-mailadres of gebruikersnaam al bestaat.
     * 
     * @param string $email
     * @param string $gebruikersnaam
     * @return bool
     */
    public function emailOfGebruikersnaamBestaat($email, $gebruikersnaam) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM Gebruiker g
            LEFT JOIN Contact c ON g.Id = c.GebruikerId
            WHERE g.Gebruikersnaam = :gebruikersnaam OR c.Email = :email
        ");
        $stmt->execute([
            'gebruikersnaam' => $gebruikersnaam,
            'email'          => $email
        ]);
        return $stmt->fetchColumn() > 0;
    }
}
