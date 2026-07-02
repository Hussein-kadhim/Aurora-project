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

    /**
     * Haalt een specifiek account op bij ID.
     * 
     * @param int $id
     * @return array|null
     */
    public function getAccountById($id) {
        $query = "
            SELECT 
                g.Id,
                g.Voornaam,
                g.Tussenvoegsel,
                g.Achternaam,
                g.Gebruikersnaam,
                g.IsActief,
                g.IsIngelogd,
                r.Naam AS RolNaam,
                c.Email,
                c.Mobiel
            FROM Gebruiker g
            LEFT JOIN Rol r ON g.Id = r.GebruikerId AND r.IsActief = 1
            LEFT JOIN Contact c ON g.Id = c.GebruikerId AND c.IsActief = 1
            WHERE g.Id = :id
        ";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Update een bestaand account.
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateAccount($id, array $data) {
        $this->pdo->beginTransaction();

        try {
            // 1. Gebruiker bijwerken
            if (!empty($data['Wachtwoord'])) {
                $stmtGebruiker = $this->pdo->prepare("
                    UPDATE Gebruiker 
                    SET Voornaam = :voornaam, 
                        Tussenvoegsel = :tussenvoegsel, 
                        Achternaam = :achternaam, 
                        Gebruikersnaam = :gebruikersnaam,
                        Wachtwoord = :wachtwoord
                    WHERE Id = :id
                ");
                $stmtGebruiker->execute([
                    'voornaam'       => $data['Voornaam'],
                    'tussenvoegsel'  => empty($data['Tussenvoegsel']) ? null : $data['Tussenvoegsel'],
                    'achternaam'     => $data['Achternaam'],
                    'gebruikersnaam' => $data['Gebruikersnaam'],
                    'wachtwoord'     => $data['Wachtwoord'],
                    'id'             => $id
                ]);
            } else {
                $stmtGebruiker = $this->pdo->prepare("
                    UPDATE Gebruiker 
                    SET Voornaam = :voornaam, 
                        Tussenvoegsel = :tussenvoegsel, 
                        Achternaam = :achternaam, 
                        Gebruikersnaam = :gebruikersnaam
                    WHERE Id = :id
                ");
                $stmtGebruiker->execute([
                    'voornaam'       => $data['Voornaam'],
                    'tussenvoegsel'  => empty($data['Tussenvoegsel']) ? null : $data['Tussenvoegsel'],
                    'achternaam'     => $data['Achternaam'],
                    'gebruikersnaam' => $data['Gebruikersnaam'],
                    'id'             => $id
                ]);
            }

            // 2. Rol bijwerken
            $stmtRol = $this->pdo->prepare("
                UPDATE Rol 
                SET Naam = :naam 
                WHERE GebruikerId = :gebruikerId AND IsActief = 1
            ");
            $stmtRol->execute([
                'gebruikerId' => $id,
                'naam'        => $data['Rol']
            ]);

            // 3. Contact bijwerken
            $stmtContact = $this->pdo->prepare("
                UPDATE Contact 
                SET Email = :email, 
                    Mobiel = :mobiel 
                WHERE GebruikerId = :gebruikerId AND IsActief = 1
            ");
            $stmtContact->execute([
                'gebruikerId' => $id,
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
     * Controleert of een e-mailadres al in gebruik is door een ander account.
     * 
     * @param string $email
     * @param int $id
     * @return bool
     */
    public function emailBestaatVoorAnder($email, $id) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM Contact 
            WHERE Email = :email AND GebruikerId != :id
        ");
        $stmt->execute([
            'email' => $email,
            'id'    => $id
        ]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Controleert of een gebruikersnaam al in gebruik is door een ander account.
     * 
     * @param string $gebruikersnaam
     * @param int $id
     * @return bool
     */
    public function gebruikersnaamBestaatVoorAnder($gebruikersnaam, $id) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM Gebruiker 
            WHERE Gebruikersnaam = :gebruikersnaam AND Id != :id
        ");
        $stmt->execute([
            'gebruikersnaam' => $gebruikersnaam,
            'id'             => $id
        ]);
        return $stmt->fetchColumn() > 0;
    }
}

