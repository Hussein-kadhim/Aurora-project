<?php

class TicketModel {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Haal alle actieve tickets op, optioneel gefilterd op zoekterm en bezoeker.
     */
    public function getAllTickets($search = '', $gebruikerId = null, $rol = '') {
        $sql = "
            SELECT 
                t.Id,
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

        $params = [];

        if ($rol === 'Bezoeker' && $gebruikerId !== null) {
            $sql .= " AND g.Id = :gebruikerId";
            $params[':gebruikerId'] = $gebruikerId;
        }

        if ($search !== '') {
            $sql .= " AND (
                t.Nummer LIKE :search 
                OR v.Naam LIKE :search 
                OR g.Voornaam LIKE :search 
                OR g.Achternaam LIKE :search
                OR CONCAT(g.Voornaam, ' ', IFNULL(g.Tussenvoegsel, ''), ' ', g.Achternaam) LIKE :search
            )";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY t.Nummer DESC";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Haal het totale aantal actieve tickets op (optioneel gefilterd).
     */
    public function getTicketCount($gebruikerId = null, $rol = '') {
        if ($rol === 'Bezoeker' && $gebruikerId !== null) {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) 
                FROM Ticket t
                JOIN Bezoeker b ON t.BezoekerId = b.Id
                WHERE t.IsActief = 1 AND b.GebruikerId = ?
            ");
            $stmt->execute([$gebruikerId]);
            return (int) $stmt->fetchColumn();
        }
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Ticket WHERE IsActief = 1");
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * Haal een ticket op basis van barcode op.
     */
    public function getTicketByBarcode($barcode) {
        $sql = "
            SELECT 
                t.Id,
                t.Nummer AS TicketNummer,
                t.Barcode,
                t.Status AS TicketStatus,
                v.Naam AS VoorstellingNaam,
                v.Datum AS VoorstellingDatum,
                v.Tijd AS VoorstellingTijd,
                g.Voornaam AS BezoekerVoornaam,
                g.Tussenvoegsel AS BezoekerTussenvoegsel,
                g.Achternaam AS BezoekerAchternaam,
                p.Tarief AS PrijsTarief,
                p.Opmerking AS PrijsOpmerking
            FROM Ticket t
            JOIN Bezoeker b ON t.BezoekerId = b.Id
            JOIN Gebruiker g ON b.GebruikerId = g.Id
            JOIN Voorstelling v ON t.VoorstellingId = v.Id
            JOIN Prijs p ON t.PrijsId = p.Id
            WHERE t.Barcode = :barcode AND t.IsActief = 1
            LIMIT 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':barcode', $barcode, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Wijzig de status van een ticket naar 'gebruikt'.
     */
    public function markTicketAsUsed($ticketId) {
        $stmt = $this->pdo->prepare("UPDATE Ticket SET Status = 'gebruikt' WHERE Id = ?");
        return $stmt->execute([$ticketId]);
    }

    /**
     * Berekent het aantal beschikbare tickets voor een voorstelling.
     */
    public function getAvailableTicketsForVoorstelling($voorstellingId) {
        // Haal MaxAantalTickets op
        $stmtMax = $this->pdo->prepare("SELECT MaxAantalTickets FROM Voorstelling WHERE Id = ? AND IsActief = 1");
        $stmtMax->execute([$voorstellingId]);
        $maxTickets = $stmtMax->fetchColumn();

        if ($maxTickets === false) {
            return 0; // Voorstelling bestaat niet of is inactief
        }

        // Telt het aantal reeds gereserveerde/actieve tickets
        $stmtBooked = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM Ticket 
            WHERE VoorstellingId = ? AND Status != 'Geannuleerd' AND IsActief = 1
        ");
        $stmtBooked->execute([$voorstellingId]);
        $bookedTickets = (int) $stmtBooked->fetchColumn();

        $available = (int) $maxTickets - $bookedTickets;
        return $available > 0 ? $available : 0;
    }

    /**
     * Haal alle actieve en geplande voorstellingen op die in de toekomst liggen.
     * Automatisch verplaatst/voegt testdata toe indien er minder dan 2 voorstellingen in de toekomst zijn.
     */
    public function getActiveVoorstellingen() {
        try {
            $countStmt = $this->pdo->query("SELECT COUNT(*) FROM Voorstelling WHERE IsActief = 1 AND Datum >= CURDATE() AND Beschikbaarheid = 'Ingepland'");
            $activeCount = (int) $countStmt->fetchColumn();

            if ($activeCount <= 1) {
                // Update 'The Sound of Music' (Id: 1) naar de toekomst (vandaag + 10 dagen)
                $this->pdo->query("UPDATE Voorstelling SET Datum = DATE_ADD(CURDATE(), INTERVAL 10 DAY), Beschikbaarheid = 'Ingepland' WHERE Id = 1");

                // Voeg extra test voorstellingen toe of zet ze in de toekomst (vandaag + X dagen)
                $testShows = [
                    [4, 3, 'The Lion King', 'Prachtige musical met indrukwekkende kostuums en bekende muziek.', 'DATE_ADD(CURDATE(), INTERVAL 14 DAY)', '19:30:00', 120, 'Ingepland', 1, 'Populair theater'],
                    [5, 3, 'Hamilton', 'De bekende musical over het leven van Alexander Hamilton.', 'DATE_ADD(CURDATE(), INTERVAL 20 DAY)', '20:00:00', 150, 'Ingepland', 1, 'Broadway hit'],
                    [6, 3, 'Mamma Mia!', 'Gezellige feel-good musical met alle grote hits van ABBA.', 'DATE_ADD(CURDATE(), INTERVAL 30 DAY)', '14:00:00', 180, 'Ingepland', 1, 'Middagvoorstelling']
                ];

                foreach ($testShows as $show) {
                    $chk = $this->pdo->prepare("SELECT COUNT(*) FROM Voorstelling WHERE Id = ?");
                    $chk->execute([$show[0]]);
                    if ((int)$chk->fetchColumn() === 0) {
                        $this->pdo->query("
                            INSERT INTO Voorstelling (Id, MedewerkerId, Naam, Beschrijving, Datum, Tijd, MaxAantalTickets, Beschikbaarheid, IsActief, Opmerking)
                            VALUES ({$show[0]}, {$show[1]}, '{$show[2]}', '{$show[3]}', {$show[4]}, '{$show[5]}', {$show[6]}, '{$show[7]}', {$show[8]}, '{$show[9]}')
                        ");
                    } else {
                        $this->pdo->query("UPDATE Voorstelling SET Datum = {$show[4]}, Beschikbaarheid = 'Ingepland' WHERE Id = {$show[0]}");
                    }
                }
            }
        } catch (PDOException $e) {
            // Silent catch
        }

        $stmt = $this->pdo->prepare("
            SELECT Id, Naam, Datum, Tijd, MaxAantalTickets 
            FROM Voorstelling 
            WHERE IsActief = 1 AND Beschikbaarheid = 'Ingepland' AND Datum >= CURDATE()
            ORDER BY Datum ASC, Tijd ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Reserveert tickets binnen een database transactie.
     * Genereert unieke reserveringsnummers en barcodes.
     */
    public function reserveTickets($gebruikerId, $voorstellingId, $aantal) {
        // Haal BezoekerId op bij GebruikerId
        $bezoekerStmt = $this->pdo->prepare("SELECT Id FROM Bezoeker WHERE GebruikerId = ?");
        $bezoekerStmt->execute([$gebruikerId]);
        $bezoekerId = $bezoekerStmt->fetchColumn();

        if (!$bezoekerId) {
            // Maak bezoeker aan voor medewerkers/administrators die testen
            $relatieStmt = $this->pdo->query("SELECT MAX(Relatienummer) FROM Bezoeker");
            $maxRelatie = (int) $relatieStmt->fetchColumn();
            $newRelatie = $maxRelatie > 0 ? $maxRelatie + 1 : 50001;

            $insertBezoeker = $this->pdo->prepare("INSERT INTO Bezoeker (GebruikerId, Relatienummer, Opmerking) VALUES (?, ?, 'Automatisch gegenereerd bij ticketreservering')");
            $insertBezoeker->execute([$gebruikerId, $newRelatie]);
            $bezoekerId = $this->pdo->lastInsertId();
        }

        $this->pdo->beginTransaction();
        try {
            // Controleer beschikbare tickets nogmaals binnen de transactie
            $available = $this->getAvailableTicketsForVoorstelling($voorstellingId);
            if ($aantal > $available) {
                $this->pdo->rollBack();
                return false;
            }

            // Haal de standaard prijs op
            $prijsStmt = $this->pdo->query("SELECT Id FROM Prijs WHERE IsActief = 1 LIMIT 1");
            $prijsId = $prijsStmt->fetchColumn() ?: 1;

            $ticketsReserved = [];

            for ($i = 0; $i < $aantal; $i++) {
                // Genereer uniek reserveringsnummer (Nummer)
                $numStmt = $this->pdo->query("SELECT MAX(Nummer) FROM Ticket");
                $maxNum = (int) $numStmt->fetchColumn();
                $newNum = $maxNum > 0 ? $maxNum + 1 : 80001;

                // Genereer unieke barcode (T- + 12 cijfers)
                do {
                    $randomDigits = '';
                    for ($j = 0; $j < 12; $j++) {
                        $randomDigits .= rand(0, 9);
                    }
                    $barcode = 'T-' . $randomDigits;

                    $checkStmt = $this->pdo->prepare("SELECT COUNT(*) FROM Ticket WHERE Barcode = ?");
                    $checkStmt->execute([$barcode]);
                    $exists = (int) $checkStmt->fetchColumn() > 0;
                } while ($exists);

                // Voeg ticket toe
                $insertStmt = $this->pdo->prepare("
                    INSERT INTO Ticket (BezoekerId, VoorstellingId, PrijsId, Nummer, Barcode, Datum, Tijd, Status, IsActief)
                    VALUES (?, ?, ?, ?, ?, CURDATE(), CURTIME(), 'Gereserveerd', 1)
                ");
                $insertStmt->execute([$bezoekerId, $voorstellingId, $prijsId, $newNum, $barcode]);

                $ticketsReserved[] = [
                    'Nummer' => $newNum,
                    'Barcode' => $barcode
                ];
            }

            $this->pdo->commit();
            return $ticketsReserved;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Haal een ticket op basis van Id op.
     */
    public function getTicketById($id) {
        $sql = "
            SELECT 
                t.Id,
                t.BezoekerId,
                t.VoorstellingId,
                t.PrijsId,
                t.Nummer AS TicketNummer,
                t.Barcode,
                t.Status AS TicketStatus,
                t.Opmerking,
                v.Naam AS VoorstellingNaam,
                v.Datum AS VoorstellingDatum,
                v.Tijd AS VoorstellingTijd,
                g.Voornaam AS BezoekerVoornaam,
                g.Tussenvoegsel AS BezoekerTussenvoegsel,
                g.Achternaam AS BezoekerAchternaam
            FROM Ticket t
            JOIN Bezoeker b ON t.BezoekerId = b.Id
            JOIN Gebruiker g ON b.GebruikerId = g.Id
            JOIN Voorstelling v ON t.VoorstellingId = v.Id
            WHERE t.Id = :id AND t.IsActief = 1
            LIMIT 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Controleer of een GebruikerId eigenaar is van een ticket.
     */
    public function isTicketOwner($ticketId, $gebruikerId) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM Ticket t
            JOIN Bezoeker b ON t.BezoekerId = b.Id
            WHERE t.Id = :ticketId AND b.GebruikerId = :gebruikerId
        ");
        $stmt->execute([
            ':ticketId' => $ticketId,
            ':gebruikerId' => $gebruikerId
        ]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Update ticketgegevens (VoorstellingId en Opmerking).
     */
    public function updateTicket($id, $voorstellingId, $opmerking) {
        $stmt = $this->pdo->prepare("
            UPDATE Ticket 
            SET VoorstellingId = :voorstellingId, 
                Opmerking = :opmerking 
            WHERE Id = :id
        ");
        return $stmt->execute([
            ':voorstellingId' => $voorstellingId,
            ':opmerking' => $opmerking,
            ':id' => $id
        ]);
    }

    /**
     * Deactiveer/verwijder een ticket (soft delete door IsActief = 0).
     */
    public function deleteTicket($id) {
        $stmt = $this->pdo->prepare("UPDATE Ticket SET Status = 'Geannuleerd' WHERE Id = ?");
        return $stmt->execute([$id]);
    }
}

