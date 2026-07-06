<?php

class MeldingModel {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Haalt gefilterde meldingen op met paginering.
     *
     * @param string $filterType  Type filter (bijv. 'Update', 'Klacht')
     * @param string $filterStatus Status filter ('unread' of 'read')
     * @param string $filterDate  Datum filter (YYYY-MM-DD)
     * @param int    $limit       Aantal resultaten per pagina
     * @param int    $offset      Startpositie voor paginering
     * @return array Lijst met meldingen
     */
    public function getMeldingen($filterType = '', $filterStatus = '', $filterDate = '', $limit = 5, $offset = 0) {
        $where  = ' WHERE 1=1';
        $params = [];

        if ($filterType !== '') {
            $where .= ' AND m.Type = :type';
            $params['type'] = $filterType;
        }

        if ($filterStatus !== '') {
            $where .= ' AND m.IsActief = :status';
            $params['status'] = ($filterStatus === 'unread') ? 1 : 0;
        }

        if ($filterDate !== '') {
            $where .= ' AND DATE(m.DatumAangemaakt) = :date';
            $params['date'] = $filterDate;
        }

        $query = "
            SELECT
                m.Id,
                m.Nummer,
                m.Type,
                m.Bericht,
                m.IsActief,
                m.Opmerking,
                m.DatumAangemaakt,
                b.Relatienummer  AS BezoekerRelatienummer,
                bg.Voornaam      AS BezoekerVoornaam,
                bg.Tussenvoegsel AS BezoekerTussenvoegsel,
                bg.Achternaam    AS BezoekerAchternaam,
                mw.Nummer        AS MedewerkerNummer,
                mg.Voornaam      AS MedewerkerVoornaam,
                mg.Tussenvoegsel AS MedewerkerTussenvoegsel,
                mg.Achternaam    AS MedewerkerAchternaam
            FROM Melding m
            LEFT JOIN Bezoeker b   ON m.BezoekerId   = b.Id
            LEFT JOIN Gebruiker bg ON b.GebruikerId   = bg.Id
            LEFT JOIN Medewerker mw ON m.MedewerkerId = mw.Id
            LEFT JOIN Gebruiker mg ON mw.GebruikerId  = mg.Id
            $where
            ORDER BY m.DatumAangemaakt DESC
            LIMIT :limit OFFSET :offset
        ";

        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            foreach ($params as $key => $val) {
                $stmt->bindValue(':' . $key, $val);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            if (defined('ALLOW_DB_FAILURE') && ALLOW_DB_FAILURE === true) {
                throw $e;
            }
            return [];
        }
    }

    /**
     * Telt het totaal aantal gefilterde meldingen (voor paginering).
     *
     * @param string $filterType
     * @param string $filterStatus
     * @param string $filterDate
     * @return int
     */
    public function countMeldingen($filterType = '', $filterStatus = '', $filterDate = '') {
        $where  = ' WHERE 1=1';
        $params = [];

        if ($filterType !== '') {
            $where .= ' AND m.Type = :type';
            $params['type'] = $filterType;
        }

        if ($filterStatus !== '') {
            $where .= ' AND m.IsActief = :status';
            $params['status'] = ($filterStatus === 'unread') ? 1 : 0;
        }

        if ($filterDate !== '') {
            $where .= ' AND DATE(m.DatumAangemaakt) = :date';
            $params['date'] = $filterDate;
        }

        $query = "SELECT COUNT(*) FROM Melding m $where";
 

        try {
            $stmt = $this->pdo->prepare($query);
            foreach ($params as $key => $val) {
                $stmt->bindValue(':' . $key, $val);
            }
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            if (defined('ALLOW_DB_FAILURE') && ALLOW_DB_FAILURE === true) {
                throw $e;
            }
            return 0;
        }
    }

    /**
     * Zoek het Medewerker.Id op basis van GebruikerId.
     *
     * @param int $gebruikerId
     * @return int|null
     */
    public function getMedewerkerIdByGebruikerId($gebruikerId) {
        try {
            $stmt = $this->pdo->prepare("SELECT Id FROM Medewerker WHERE GebruikerId = ? LIMIT 1");
            $stmt->execute([$gebruikerId]);
            $id = $stmt->fetchColumn();
            return $id ? (int) $id : null;
        } catch (PDOException $e) {
            if (defined('ALLOW_DB_FAILURE') && ALLOW_DB_FAILURE === true) {
                throw $e;
            }
            return null;
        }
    }

    /**
     * Haal het eerstvolgende unieke meldingsnummer op.
     *
     * @return int
     */
    public function getNextMeldingNummer() {
        try {
            $stmt = $this->pdo->query("SELECT MAX(Nummer) FROM Melding");
            $max = $stmt->fetchColumn();
            return $max ? (int) $max + 1 : 90001;
        } catch (PDOException $e) {
            if (defined('ALLOW_DB_FAILURE') && ALLOW_DB_FAILURE === true) {
                throw $e;
            }
            return 90001;
        }
    }

    /**
     * Sla een nieuwe melding op in de database.
     *
     * @param int|null $medewerkerId
     * @param string   $type
     * @param string   $bericht
     * @param bool     $isActief
     * @param string   $opmerking
     * @return bool
     */
    public function createMelding($medewerkerId, $type, $bericht, $isActief, $opmerking) {
        $nummer = $this->getNextMeldingNummer();
        $query = "
            INSERT INTO Melding (BezoekerId, MedewerkerId, Nummer, Type, Bericht, IsActief, Opmerking)
            VALUES (NULL, :medewerkerId, :nummer, :type, :bericht, :isActief, :opmerking)
        ";
        try {
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute([
                'medewerkerId' => $medewerkerId,
                'nummer'       => $nummer,
                'type'         => $type,
                'bericht'      => $bericht,
                'isActief'     => $isActief ? 1 : 0,
                'opmerking'    => $opmerking !== '' ? $opmerking : null
            ]);
        } catch (PDOException $e) {
            if (defined('ALLOW_DB_FAILURE') && ALLOW_DB_FAILURE === true) {
                throw $e;
            }
            return false;
        }
    }

    /**
     * Zorg ervoor dat de MeldingFeedback tabel bestaat (dynamisch aangemaakt).
     */
    private function ensureFeedbackTableExists() {
        $query = "CREATE TABLE IF NOT EXISTS MeldingFeedback (
            Id INT AUTO_INCREMENT PRIMARY KEY,
            MeldingId INT NOT NULL,
            GebruikerId INT NOT NULL,
            FeedbackTekst TEXT NOT NULL,
            Rating INT DEFAULT 5,
            DatumAangemaakt DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (MeldingId) REFERENCES Melding(Id) ON DELETE CASCADE,
            FOREIGN KEY (GebruikerId) REFERENCES Gebruiker(Id) ON DELETE CASCADE
        ) ENGINE=InnoDB;";
        try {
            $this->pdo->exec($query);
        } catch (PDOException $e) {
            if (defined('ALLOW_DB_FAILURE') && ALLOW_DB_FAILURE === true) {
                throw $e;
            }
        }
    }

    /**
     * Sla feedback op voor een specifieke melding.
     */
    public function saveFeedback($meldingId, $gebruikerId, $tekst) {
        $this->ensureFeedbackTableExists();
        
        $query = "INSERT INTO MeldingFeedback (MeldingId, GebruikerId, FeedbackTekst) VALUES (:meldingId, :gebruikerId, :tekst)";
        try {
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute([
                'meldingId'   => $meldingId,
                'gebruikerId' => $gebruikerId,
                'tekst'       => $tekst
            ]);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * Haal feedback op voor een lijst met meldingsID's.
     * @param array $meldingIds
     * @return array
     */
    public function getFeedbackForMeldingen($meldingIds) {
        if (empty($meldingIds)) return [];
        
        $this->ensureFeedbackTableExists();

        $inQuery = implode(',', array_fill(0, count($meldingIds), '?'));
        
        $query = "
            SELECT f.MeldingId, f.FeedbackTekst, f.DatumAangemaakt,
                   g.Voornaam, g.Tussenvoegsel, g.Achternaam
            FROM MeldingFeedback f
            LEFT JOIN Gebruiker g ON f.GebruikerId = g.Id
            WHERE f.MeldingId IN ($inQuery)
            ORDER BY f.DatumAangemaakt ASC
        ";

        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(array_values($meldingIds));
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            if (defined('ALLOW_DB_FAILURE') && ALLOW_DB_FAILURE === true) {
                throw $e;
            }
            return [];
        }
    }

    /**
     * Haal alle feedback op inclusief gerelateerde meldingsinformatie.
     * @return array
     */
    public function getAllFeedback() {
        $this->ensureFeedbackTableExists();
        
        $query = "
            SELECT f.Id AS FeedbackId, f.FeedbackTekst, f.Rating, f.DatumAangemaakt,
                   g.Voornaam, g.Tussenvoegsel, g.Achternaam,
                   m.Id AS MeldingId, m.Bericht AS MeldingTitel
            FROM MeldingFeedback f
            LEFT JOIN Gebruiker g ON f.GebruikerId = g.Id
            LEFT JOIN Melding m ON f.MeldingId = m.Id
            ORDER BY f.DatumAangemaakt DESC
        ";

        try {
            $stmt = $this->pdo->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            if (defined('ALLOW_DB_FAILURE') && ALLOW_DB_FAILURE === true) {
                throw $e;
            }
            return [];
        }
    }

}
