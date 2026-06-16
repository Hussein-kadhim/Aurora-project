<?php

require_once __DIR__ . '/AccountModel.php';

class AccountController {
    private $model;

    public function __construct(PDO $pdo) {
        $this->model = new AccountModel($pdo);
    }

    /**
     * Startpunt van de controller actie.
     */
    public function index() {
        // 1. Controleer of de gebruiker is ingelogd
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['ingelogd']) || empty($_SESSION['gebruiker_id'])) {
            // Tijdelijk uitgezet
        }

        // 2. Beveiliging: Alleen Administrator heeft toegang
        if (empty($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrator') {
            // Tijdelijk uitgezet
        }

        // 3. Haal zoekopdracht op
        $search = trim($_GET['search'] ?? '');

        // 4. Haal data op uit het Model
        $accounts = $this->model->getAllAccounts($search);
        $totalCount = $this->model->getAccountCount();

        // 5. Laad de view
        require_once __DIR__ . '/views/index.php';
    }

    /**
     * Toont het formulier en verwerkt het aanmaken van een nieuw account.
     */
    public function create() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Redirects uitgezet voor testdoeleinden
        // if (empty($_SESSION['ingelogd']) || empty($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrator') {
        //     header('Location: ../../informatie/home.php');
        //     exit();
        // }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $voornaam       = trim($_POST['Voornaam'] ?? '');
            $tussenvoegsel  = trim($_POST['Tussenvoegsel'] ?? '');
            $achternaam     = trim($_POST['Achternaam'] ?? '');
            $gebruikersnaam = trim($_POST['Gebruikersnaam'] ?? '');
            $email          = trim($_POST['Email'] ?? '');
            $mobiel         = trim($_POST['Mobiel'] ?? '');
            $rol            = trim($_POST['Rol'] ?? '');
            $wachtwoord     = $_POST['Wachtwoord'] ?? '';

            // 1. Validatie: Check verplichte velden
            if (empty($voornaam) || empty($achternaam) || empty($gebruikersnaam) || empty($email) || empty($mobiel) || empty($rol) || empty($wachtwoord)) {
                $error = 'Vul aub alle verplichte velden in.';
            } else {
                // 2. Wachtwoord hashen
                $gehashtWachtwoord = password_hash($wachtwoord, PASSWORD_DEFAULT);

                $data = [
                    'Voornaam'       => $voornaam,
                    'Tussenvoegsel'  => $tussenvoegsel,
                    'Achternaam'     => $achternaam,
                    'Gebruikersnaam' => $gebruikersnaam,
                    'Email'          => $email,
                    'Mobiel'         => $mobiel,
                    'Rol'            => $rol,
                    'Wachtwoord'     => $gehashtWachtwoord
                ];

                // 3. Opslaan via Model (Transactie)
                $success = $this->model->createAccount($data);

                if ($success) {
                    header('Location: index.php?success=1');
                    exit();
                } else {
                    $error = 'Systeemfout: Account kon niet worden opgeslagen. Probeer het later opnieuw.';
                }
            }
        }

        // Laad de view
        require_once __DIR__ . '/views/create.php';
    }
}
