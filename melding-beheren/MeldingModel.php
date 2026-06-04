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
            return 0;
        }
    }
}
