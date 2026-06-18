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

        // 1. Controleer of de gebruiker is ingelogd en toegang heeft (Administrator of Medewerker)
        $rol = $_SESSION['rol'] ?? '';
        if (empty($_SESSION['ingelogd']) || empty($_SESSION['gebruiker_id']) || ($rol !== 'Administrator' && $rol !== 'Medewerker')) {
            require_once __DIR__ . '/../../includes/geen_toegang.php';
            exit();
        }

        // 3. Haal zoekopdracht op
        $search = trim($_GET['search'] ?? '');

        // 4. Haal data op uit het Model
        $tickets = [];
        $totalCount = 0;
        try {
            $tickets = $this->model->getAllTickets($search);
            $totalCount = $this->model->getTicketCount();
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
}

