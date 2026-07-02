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

    /**
     * Voegt een nieuwe medewerker toe onder een database-transactie.
     * 
     * @param array $data De formulierdata van de nieuwe medewerker
     * @return bool True bij succes, anders false of werpt uitzondering
     */
    public function addMedewerker(array $data): bool {
        $this->pdo->beginTransaction();
        try {
            // 1. Verkrijg het volgende medewerkersnummer
            $stmtNum = $this->pdo->query("SELECT MAX(Nummer) FROM Medewerker");
            $maxNum = $stmtNum->fetchColumn();
            $nextNum = $maxNum ? (int)$maxNum + 1 : 10001;

            // 2. Wachtwoord hashen
            $wachtwoordHash = password_hash($data['wachtwoord'], PASSWORD_DEFAULT);

            // 3. Voeg Gebruiker toe
            $stmtGebruiker = $this->pdo->prepare("
                INSERT INTO Gebruiker (Voornaam, Tussenvoegsel, Achternaam, Gebruikersnaam, Wachtwoord, IsIngelogd, IsActief, Opmerking)
                VALUES (:voornaam, :tussenvoegsel, :achternaam, :gebruikersnaam, :wachtwoord, 0, 1, :opmerking)
            ");
            $stmtGebruiker->execute([
                'voornaam'       => $data['voornaam'],
                'tussenvoegsel'  => !empty($data['tussenvoegsel']) ? $data['tussenvoegsel'] : null,
                'achternaam'     => $data['achternaam'],
                'gebruikersnaam' => $data['email'], // Gebruikersnaam is gelijk aan e-mail
                'wachtwoord'     => $wachtwoordHash,
                'opmerking'      => !empty($data['opmerking']) ? $data['opmerking'] : null
            ]);
            $gebruikerId = (int) $this->pdo->lastInsertId();

            // 4. Voeg Contactgegevens toe
            $stmtContact = $this->pdo->prepare("
                INSERT INTO Contact (GebruikerId, Email, Mobiel, IsActief, Opmerking)
                VALUES (:gebruiker_id, :email, :mobiel, 1, NULL)
            ");
            $stmtContact->execute([
                'gebruiker_id' => $gebruikerId,
                'email'        => $data['email'],
                'mobiel'       => $data['mobiel']
            ]);

            // 5. Voeg Rol toe
            $stmtRol = $this->pdo->prepare("
                INSERT INTO Rol (GebruikerId, Naam, IsActief, Opmerking)
                VALUES (:gebruiker_id, :rol, 1, NULL)
            ");
            $stmtRol->execute([
                'gebruiker_id' => $gebruikerId,
                'rol'          => $data['rol'] // 'Medewerker' of 'Administrator'
            ]);

            // 6. Voeg Medewerker toe
            $stmtMedewerker = $this->pdo->prepare("
                INSERT INTO Medewerker (GebruikerId, Nummer, Medewerkersoort, IsActief, Opmerking)
                VALUES (:gebruiker_id, :nummer, :medewerkersoort, 1, :opmerking)
            ");
            $stmtMedewerker->execute([
                'gebruiker_id'     => $gebruikerId,
                'nummer'           => $nextNum,
                'medewerkersoort'  => $data['medewerkersoort'],
                'opmerking'        => !empty($data['opmerking']) ? $data['opmerking'] : null
            ]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Haalt een specifieke actieve medewerker op basis van Medewerker Id.
     * 
     * @param int $id MedewerkerId
     * @return array|false Medewerkergegevens of false bij niet gevonden
     */
    public function getMedewerkerById(int $id) {
        $query = "
            SELECT
                m.Id                AS MedewerkerId,
                m.GebruikerId,
                m.Nummer            AS MedewerkerNummer,
                m.Medewerkersoort,
                m.IsActief          AS MedewerkerActief,
                g.Voornaam,
                g.Tussenvoegsel,
                g.Achternaam,
                g.Gebruikersnaam,
                c.Email,
                c.Mobiel,
                r.Naam              AS Rol,
                g.Opmerking
            FROM medewerker m
            JOIN gebruiker  g ON g.Id = m.GebruikerId
            LEFT JOIN contact c ON c.GebruikerId = g.Id AND c.IsActief = 1
            LEFT JOIN rol     r ON r.GebruikerId = g.Id AND r.IsActief = 1
            WHERE m.Id = :id AND m.IsActief = 1
        ";

        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            if (defined('ALLOW_DB_FAILURE') && ALLOW_DB_FAILURE === true) {
                throw $e;
            }
            return false;
        }
    }

    /**
     * Controleert of een e-mailadres / gebruikersnaam al bestaat voor een ANDERE gebruiker.
     * 
     * @param string $email
     * @param int $gebruikerId
     * @return bool
     */
    public function gebruikersnaamBestaatVoorAnder(string $email, int $gebruikerId): bool {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Gebruiker WHERE Gebruikersnaam = ? AND Id != ?");
        $stmt->execute([$email, $gebruikerId]);
        if ((int)$stmt->fetchColumn() > 0) {
            return true;
        }

        $stmt2 = $this->pdo->prepare("SELECT COUNT(*) FROM Contact WHERE Email = ? AND GebruikerId != ?");
        $stmt2->execute([$email, $gebruikerId]);
        if ((int)$stmt2->fetchColumn() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Wijzigt een bestaande medewerker onder een database-transactie.
     * 
     * @param int $medewerkerId
     * @param int $gebruikerId
     * @param array $data De formulierdata van de medewerker
     * @return bool True bij succes, anders false of werpt uitzondering
     */
    public function updateMedewerker(int $medewerkerId, int $gebruikerId, array $data): bool {
        $this->pdo->beginTransaction();
        try {
            // 1. Update Gebruiker
            if (!empty($data['wachtwoord'])) {
                $wachtwoordHash = password_hash($data['wachtwoord'], PASSWORD_DEFAULT);
                $stmtGebruiker = $this->pdo->prepare("
                    UPDATE Gebruiker 
                    SET Voornaam = :voornaam, Tussenvoegsel = :tussenvoegsel, Achternaam = :achternaam, 
                        Gebruikersnaam = :gebruikersnaam, Wachtwoord = :wachtwoord, Opmerking = :opmerking
                    WHERE Id = :gebruiker_id
                ");
                $stmtGebruiker->execute([
                    'voornaam'       => $data['voornaam'],
                    'tussenvoegsel'  => !empty($data['tussenvoegsel']) ? $data['tussenvoegsel'] : null,
                    'achternaam'     => $data['achternaam'],
                    'gebruikersnaam' => $data['email'],
                    'wachtwoord'     => $wachtwoordHash,
                    'opmerking'      => !empty($data['opmerking']) ? $data['opmerking'] : null,
                    'gebruiker_id'   => $gebruikerId
                ]);
            } else {
                $stmtGebruiker = $this->pdo->prepare("
                    UPDATE Gebruiker 
                    SET Voornaam = :voornaam, Tussenvoegsel = :tussenvoegsel, Achternaam = :achternaam, 
                        Gebruikersnaam = :gebruikersnaam, Opmerking = :opmerking
                    WHERE Id = :gebruiker_id
                ");
                $stmtGebruiker->execute([
                    'voornaam'       => $data['voornaam'],
                    'tussenvoegsel'  => !empty($data['tussenvoegsel']) ? $data['tussenvoegsel'] : null,
                    'achternaam'     => $data['achternaam'],
                    'gebruikersnaam' => $data['email'],
                    'opmerking'      => !empty($data['opmerking']) ? $data['opmerking'] : null,
                    'gebruiker_id'   => $gebruikerId
                ]);
            }

            // 2. Update Contactgegevens
            $stmtCheckContact = $this->pdo->prepare("SELECT COUNT(*) FROM Contact WHERE GebruikerId = ? AND IsActief = 1");
            $stmtCheckContact->execute([$gebruikerId]);
            if ((int)$stmtCheckContact->fetchColumn() > 0) {
                $stmtContact = $this->pdo->prepare("
                    UPDATE Contact 
                    SET Email = :email, Mobiel = :mobiel 
                    WHERE GebruikerId = :gebruiker_id AND IsActief = 1
                ");
                $stmtContact->execute([
                    'email'        => $data['email'],
                    'mobiel'       => $data['mobiel'],
                    'gebruiker_id' => $gebruikerId
                ]);
            } else {
                $stmtContact = $this->pdo->prepare("
                    INSERT INTO Contact (GebruikerId, Email, Mobiel, IsActief, Opmerking)
                    VALUES (:gebruiker_id, :email, :mobiel, 1, NULL)
                ");
                $stmtContact->execute([
                    'gebruiker_id' => $gebruikerId,
                    'email'        => $data['email'],
                    'mobiel'       => $data['mobiel']
                ]);
            }

            // 3. Update Rol
            $stmtCheckRol = $this->pdo->prepare("SELECT COUNT(*) FROM Rol WHERE GebruikerId = ? AND IsActief = 1");
            $stmtCheckRol->execute([$gebruikerId]);
            if ((int)$stmtCheckRol->fetchColumn() > 0) {
                $stmtRol = $this->pdo->prepare("
                    UPDATE Rol 
                    SET Naam = :rol 
                    WHERE GebruikerId = :gebruiker_id AND IsActief = 1
                ");
                $stmtRol->execute([
                    'rol'          => $data['rol'],
                    'gebruiker_id' => $gebruikerId
                ]);
            } else {
                $stmtRol = $this->pdo->prepare("
                    INSERT INTO Rol (GebruikerId, Naam, IsActief, Opmerking)
                    VALUES (:gebruiker_id, :rol, 1, NULL)
                ");
                $stmtRol->execute([
                    'gebruiker_id' => $gebruikerId,
                    'rol'          => $data['rol']
                ]);
            }

            // 4. Update Medewerker
            $stmtMedewerker = $this->pdo->prepare("
                UPDATE Medewerker 
                SET Medewerkersoort = :medewerkersoort, Opmerking = :opmerking 
                WHERE Id = :medewerker_id
            ");
            $stmtMedewerker->execute([
                'medewerkersoort' => $data['medewerkersoort'],
                'opmerking'       => !empty($data['opmerking']) ? $data['opmerking'] : null,
                'medewerker_id'   => $medewerkerId
            ]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}

