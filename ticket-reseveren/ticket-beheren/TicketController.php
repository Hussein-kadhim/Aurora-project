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
            header('Location: ../../login.php');
            exit();
        }

        // 2. Beveiliging: Medewerker en Administrator hebben toegang
        if (empty($_SESSION['rol']) || ($_SESSION['rol'] !== 'Administrator' && $_SESSION['rol'] !== 'Medewerker')) {
            header('Location: ../../informatie/home.php');
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
}
