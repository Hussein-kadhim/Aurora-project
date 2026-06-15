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
            header('Location: ../../login.php');
            exit();
        }

        // 2. Beveiliging: Alleen Administrator heeft toegang
        if (empty($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrator') {
            header('Location: ../../informatie/home.php');
            exit();
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

    /**
     * Actie voor het toevoegen van een nieuwe medewerker.
     */
    public function create() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Controleer of de gebruiker is ingelogd
        if (empty($_SESSION['ingelogd']) || empty($_SESSION['gebruiker_id'])) {
            header('Location: ../../login.php');
            exit();
        }

        // 2. Beveiliging: Alleen Administrator heeft toegang
        if (empty($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrator') {
            header('Location: ../../informatie/home.php');
            exit();
        }

        $fouten = [];
        $voornaam = '';
        $tussenvoegsel = '';
        $achternaam = '';
        $email = '';
        $mobiel = '';
        $medewerkersoort = 'Beheerder'; // default
        $rol = 'Medewerker'; // default
        $opmerking = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $voornaam        = trim($_POST['voornaam'] ?? '');
            $tussenvoegsel   = trim($_POST['tussenvoegsel'] ?? '');
            $achternaam      = trim($_POST['achternaam'] ?? '');
            $email           = trim($_POST['email'] ?? '');
            $mobiel          = trim($_POST['mobiel'] ?? '');
            $medewerkersoort = trim($_POST['medewerkersoort'] ?? '');
            $rol             = trim($_POST['rol'] ?? '');
            $wachtwoord      = $_POST['wachtwoord'] ?? '';
            $wachtwoordBev   = $_POST['wachtwoord_bevestigen'] ?? '';
            $opmerking       = trim($_POST['opmerking'] ?? '');

            // Validatie
            if ($voornaam === '') {
                $fouten[] = 'Voornaam is verplicht.';
            } elseif (strlen($voornaam) > 50) {
                $fouten[] = 'Voornaam mag maximaal 50 tekens bevatten.';
            }

            if (strlen($tussenvoegsel) > 10) {
                $fouten[] = 'Tussenvoegsel mag maximaal 10 tekens bevatten.';
            }

            if ($achternaam === '') {
                $fouten[] = 'Achternaam is verplicht.';
            } elseif (strlen($achternaam) > 50) {
                $fouten[] = 'Achternaam mag maximaal 50 tekens bevatten.';
            }

            if ($email === '') {
                $fouten[] = 'E-mailadres is verplicht.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $fouten[] = 'Vul een geldig e-mailadres in.';
            } elseif (strlen($email) > 100) {
                $fouten[] = 'E-mailadres mag maximaal 100 tekens bevatten.';
            } elseif ($this->model->gebruikersnaamBestaat($email)) {
                $fouten[] = 'Dit e-mailadres / gebruikersnaam is al in gebruik.';
            }

            if ($mobiel === '') {
                $fouten[] = 'Mobiel telefoonnummer is verplicht.';
            } elseif (strlen($mobiel) > 20) {
                $fouten[] = 'Mobiel nummer mag maximaal 20 tekens bevatten.';
            }

            if ($medewerkersoort === '') {
                $fouten[] = 'Medewerkersoort is verplicht.';
            } elseif (strlen($medewerkersoort) > 20) {
                $fouten[] = 'Medewerkersoort mag maximaal 20 tekens bevatten.';
            }

            if ($rol === '') {
                $fouten[] = 'Rol is verplicht.';
            } elseif (!in_array($rol, ['Medewerker', 'Administrator'])) {
                $fouten[] = 'Ongeldige rol geselecteerd.';
            }

            if ($wachtwoord === '') {
                $fouten[] = 'Wachtwoord is verplicht.';
            } elseif (strlen($wachtwoord) < 6) {
                $fouten[] = 'Wachtwoord moet minimaal 6 tekens bevatten.';
            } elseif (strlen($wachtwoord) > 255) {
                $fouten[] = 'Wachtwoord mag maximaal 255 tekens bevatten.';
            }

            if ($wachtwoord !== $wachtwoordBev) {
                $fouten[] = 'De wachtwoorden komen niet overeen.';
            }

            if (strlen($opmerking) > 250) {
                $fouten[] = 'Opmerking mag maximaal 250 tekens bevatten.';
            }
        }

        require_once __DIR__ . '/views/create.php';
    }
}
