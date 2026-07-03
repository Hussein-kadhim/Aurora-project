<?php

require_once __DIR__ . '/TicketModel.php';

class TicketController {
    private $model;

    public function __construct(PDO $pdo) {
        $this->model = new TicketModel($pdo);
    }

    /**
     * Startpunt van de ticketoverzicht pagina.
     */
    public function index() {
        // 1. Controleer of de gebruiker is ingelogd
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Controleer of de gebruiker is ingelogd en toegang heeft
        $rol = $_SESSION['rol'] ?? '';
        if (empty($_SESSION['ingelogd']) || empty($_SESSION['gebruiker_id']) || empty($rol)) {
            require_once __DIR__ . '/../../includes/geen_toegang.php';
            exit();
        }

        $gebruikerId = (int) $_SESSION['gebruiker_id'];

        // 3. Haal zoekopdracht op
        $search = trim($_GET['search'] ?? '');

        // 4. Haal data op uit het Model
        $tickets = [];
        $totalCount = 0;
        try {
            $tickets = $this->model->getAllTickets($search, $gebruikerId, $rol);
            $totalCount = $this->model->getTicketCount($gebruikerId, $rol);
        } catch (PDOException $e) {
            // Behandel database-fouten (zoals geen geselecteerde database of ontbrekende tabellen)
            // door terug te vallen op een lege status (unhappy scenario)
            $tickets = [];
            $totalCount = 0;
        }

        // 5. Laad de view
        require_once __DIR__ . '/views/index.php';
    }

    /**
     * Ticket scannen pagina.
     */
    public function scan() {
        // 1. Controleer of de gebruiker is ingelogd
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Controleer of de gebruiker is ingelogd en toegang heeft (Administrator of Medewerker)
        $rol = $_SESSION['rol'] ?? '';
        if (empty($_SESSION['ingelogd']) || empty($_SESSION['gebruiker_id']) || ($rol !== 'Administrator' && $rol !== 'Medewerker')) {
            require_once __DIR__ . '/../../includes/geen_toegang.php';
            exit();
        }

        $ticket = null;
        $success = null;
        $errorMessage = '';
        $barcode = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $barcode = trim($_POST['barcode'] ?? '');

            if ($barcode === '') {
                $success = false;
                $errorMessage = 'Voer een geldige barcode in.';
            } else {
                try {
                    // Haal ticket op
                    $ticket = $this->model->getTicketByBarcode($barcode);

                    if (!$ticket) {
                        $success = false;
                        $errorMessage = 'Ongeldig of reeds gebruikt ticket';
                    } else {
                        // Controleer of de status al 'gebruikt', 'bezet' of 'geannuleerd' is
                        $status = strtolower($ticket['TicketStatus']);
                        if ($status === 'gebruikt' || $status === 'bezet' || $status === 'geannuleerd') {
                            $success = false;
                            $errorMessage = 'Ongeldig of reeds gebruikt ticket';
                            $ticket = null; // Toon geen ticketgegevens voor een reeds gebruikt/geannuleerd ticket
                        } else {
                            // Update status naar 'gebruikt'
                            $this->model->markTicketAsUsed($ticket['Id']);
                            // Update lokale status voor weergave
                            $ticket['TicketStatus'] = 'gebruikt';
                            $success = true;
                        }
                    }
                } catch (PDOException $e) {
                    $success = false;
                    $errorMessage = 'Er is een databasefout opgetreden.';
                    $ticket = null;
                }
            }
        }

        // Laad de view
        require_once __DIR__ . '/views/scan.php';
    }

    /**
     * Ticket reserveren pagina.
     */
    public function reserve() {
        // 1. Controleer of de gebruiker is ingelogd
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['ingelogd']) || empty($_SESSION['gebruiker_id'])) {
            require_once __DIR__ . '/../../includes/geen_toegang.php';
            exit();
        }

        $gebruikerId = (int) $_SESSION['gebruiker_id'];
        $voorstellingen = [];
        $errorMessage = '';
        $success = false;
        $reservedTickets = [];
        $gekozenVoorstelling = null;

        try {
            // Haal actieve voorstellingen op voor de dropdown
            $voorstellingen = $this->model->getActiveVoorstellingen();
        } catch (PDOException $e) {
            $errorMessage = 'Er is een databasefout opgetreden bij het laden van de voorstellingen.';
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $voorstellingId = (int) ($_POST['voorstelling_id'] ?? 0);
            $aantal = (int) ($_POST['aantal'] ?? 0);

            if ($voorstellingId <= 0) {
                $errorMessage = 'Selecteer een geldige voorstelling.';
            } elseif ($aantal <= 0 || $aantal > 10) {
                $errorMessage = 'Kies een aantal tickets tussen 1 en 10.';
            } else {
                try {
                    // Check beschikbare tickets
                    $available = $this->model->getAvailableTicketsForVoorstelling($voorstellingId);

                    if ($aantal > $available) {
                        // Unhappy scenario: Niet genoeg tickets beschikbaar
                        $errorMessage = 'Niet genoeg tickets beschikbaar';
                    } else {
                        // Happy scenario: Reserveer tickets
                        $reservedTickets = $this->model->reserveTickets($gebruikerId, $voorstellingId, $aantal);
                        if ($reservedTickets === false) {
                            $errorMessage = 'Niet genoeg tickets beschikbaar';
                        } else {
                            $success = true;

                            // Haal details op van de gekozen voorstelling voor de bevestigingspagina
                            // We halen alle actieve voorstellingen op en zoeken de juiste op basis van Id
                            foreach ($voorstellingen as $vs) {
                                if ((int)$vs['Id'] === $voorstellingId) {
                                    $gekozenVoorstelling = $vs;
                                    break;
                                }
                            }
                        }
                    }
                } catch (PDOException $e) {
                    $errorMessage = 'Er is een databasefout opgetreden bij het reserveren van de tickets.';
                }
            }
        }

        // Laad de view
        require_once __DIR__ . '/views/reserveren.php';
    }

    /**
     * Ticket wijzigen pagina.
     */
    public function edit() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Controleer of de gebruiker is ingelogd
        if (empty($_SESSION['ingelogd']) || empty($_SESSION['gebruiker_id'])) {
            require_once __DIR__ . '/../../includes/geen_toegang.php';
            exit();
        }

        $gebruikerId = (int)$_SESSION['gebruiker_id'];
        $rol = $_SESSION['rol'] ?? '';
        $ticketId = (int)($_GET['id'] ?? 0);

        if ($ticketId <= 0) {
            header('Location: index.php');
            exit();
        }

        // 2. Haal ticket op
        $ticket = $this->model->getTicketById($ticketId);
        if (!$ticket) {
            header('Location: index.php');
            exit();
        }

        // 3. Toegangscontrole: Een bezoeker mag alleen zijn eigen tickets wijzigen
        if ($rol === 'Bezoeker' && !$this->model->isTicketOwner($ticketId, $gebruikerId)) {
            require_once __DIR__ . '/../../includes/geen_toegang.php';
            exit();
        }

        $errorMessage = '';
        $successMessage = '';
        $voorstellingen = [];

        try {
            $voorstellingen = $this->model->getActiveVoorstellingen();
        } catch (PDOException $e) {
            $errorMessage = 'Fout bij het ophalen van voorstellingen.';
        }

        // 4. Verwerk het POST-verzoek (opslaan)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $voorstellingId = (int)($_POST['voorstelling_id'] ?? 0);
            $opmerking = trim($_POST['opmerking'] ?? '');

            // Unhappy scenario: Controleer of het ticket al is gebruikt
            $status = strtolower($ticket['TicketStatus']);
            if ($status === 'gebruikt' || $status === 'bezet') {
                $errorMessage = 'Gebruikt ticket kan niet worden gewijzigd';
            } elseif ($voorstellingId <= 0) {
                $errorMessage = 'Selecteer een geldige voorstelling.';
            } else {
                try {
                    // Sla de wijziging op
                    $this->model->updateTicket($ticketId, $voorstellingId, $opmerking);
                    
                    // Update het lokale ticket object voor weergave
                    $ticket = $this->model->getTicketById($ticketId);
                    $successMessage = 'Ticket succesvol gewijzigd';
                } catch (PDOException $e) {
                    $errorMessage = 'Er is een databasefout opgetreden bij het bijwerken van het ticket.';
                }
            }
        }

        // Laad de view
        require_once __DIR__ . '/views/edit.php';
    }

    /**
     * Ticket verwijderen actie.
     */
    public function delete() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Controleer of de gebruiker is ingelogd
        if (empty($_SESSION['ingelogd']) || empty($_SESSION['gebruiker_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Niet ingelogd']);
            exit();
        }

        $gebruikerId = (int)$_SESSION['gebruiker_id'];
        $rol = $_SESSION['rol'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ticketId = (int)($_POST['id'] ?? 0);

            if ($ticketId <= 0) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Ongeldig ticket ID']);
                exit();
            }

            // Controleer of de bezoeker eigenaar is van het ticket
            if ($rol === 'Bezoeker' && !$this->model->isTicketOwner($ticketId, $gebruikerId)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Geen toegang']);
                exit();
            }

            // Haal ticket op
            $ticket = $this->model->getTicketById($ticketId);
            if (!$ticket) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Ticket niet gevonden']);
                exit();
            }

            // Controleer of het ticket gebruikt is
            $status = strtolower($ticket['TicketStatus']);
            if ($status === 'gebruikt' || $status === 'bezet') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Gebruikt ticket kan niet worden verwijderd']);
                exit();
            }

            try {
                $this->model->deleteTicket($ticketId);
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit();
            } catch (PDOException $e) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Databasefout']);
                exit();
            }
        }

        header('Location: index.php');
        exit();
    }
}

