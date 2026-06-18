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

        if (empty($_SESSION['ingelogd']) || empty($_SESSION['gebruiker_id'])) {
            // Tijdelijk uitgezet
            // header('Location: ../../login.php');
            // exit();
        }

        // 2. Beveiliging: Medewerker en Administrator hebben toegang
        if (empty($_SESSION['rol']) || ($_SESSION['rol'] !== 'Administrator' && $_SESSION['rol'] !== 'Medewerker')) {
            // Tijdelijk uitgezet
            // header('Location: ../../informatie/home.php');
            // exit();
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

        if (empty($_SESSION['ingelogd']) || empty($_SESSION['gebruiker_id'])) {
            // Tijdelijk uitgezet
            // header('Location: ../../login.php');
            // exit();
        }

        // 2. Beveiliging: Medewerker en Administrator hebben toegang
        if (empty($_SESSION['rol']) || ($_SESSION['rol'] !== 'Administrator' && $_SESSION['rol'] !== 'Medewerker')) {
            // Tijdelijk uitgezet
            // header('Location: ../../informatie/home.php');
            // exit();
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
}

