<?php

require_once __DIR__ . '/MedewerkerModel.php';

class MedewerkerController {
    private $model;

    public function __construct(PDO $pdo) {
        $this->model = new MedewerkerModel($pdo);
    }

    /**
     * Startpunt van de controller actie.
     */
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Controleer of de gebruiker is ingelogd
        if (empty($_SESSION['ingelogd']) || empty($_SESSION['gebruiker_id'])) {
            // Tijdelijk uitgezet voor testdoeleinden
            // header('Location: ../../login.php');
            // exit();
        }

        // 2. Beveiliging: Alleen Administrator heeft toegang
        if (empty($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrator') {
            // Tijdelijk uitgezet voor testdoeleinden
            // header('Location: ../../informatie/home.php');
            // exit();
        }

        $dbFout = false;
        $foutmelding = '';
        $medewerkers = [];
        $totalCount = 0;
        $search = trim($_GET['search'] ?? '');

        try {
            // 3. Haal data op uit het Model
            $medewerkers = $this->model->getAllMedewerkers($search);
            $totalCount = $this->model->getMedewerkerCount();
        } catch (PDOException $e) {
            $dbFout = true;
            $foutmelding = 'De server is momenteel niet bereikbaar';
        } catch (Throwable $e) {
            $dbFout = true;
            $foutmelding = 'Er is een onverwachte fout opgetreden. Probeer het later opnieuw.';
        }

        // Helper functie voor de view
        if (!function_exists('volledigeNaam')) {
            function volledigeNaam(string $voornaam, ?string $tussenvoegsel, string $achternaam): string {
                $delen = array_filter([$voornaam, $tussenvoegsel, $achternaam]);
                return implode(' ', $delen);
            }
        }

        // 4. Laad de view
        require_once __DIR__ . '/views/index.php';
    }
}
