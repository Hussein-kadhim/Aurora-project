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

        // 1. Controleer of de gebruiker is ingelogd en Administrator is
        if (empty($_SESSION['ingelogd']) || empty($_SESSION['gebruiker_id']) || empty($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrator') {
            require_once __DIR__ . '/../../includes/geen_toegang.php';
            exit();
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

        if (empty($_SESSION['ingelogd']) || empty($_SESSION['gebruiker_id']) || empty($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrator') {
            require_once __DIR__ . '/../../includes/geen_toegang.php';
            exit();
        }

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
                $error = 'Vul alle verplichte velden in.';
            } elseif ($this->model->emailOfGebruikersnaamBestaat($email, $gebruikersnaam)) {
                $error = 'Dit e-mailadres of deze gebruikersnaam is al in gebruik.';
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

                try {
                    // 3. Opslaan via Model (Transactie)
                    $success = $this->model->createAccount($data);

                    if ($success) {
                        header('Location: index.php?success=1');
                        exit();
                    } else {
                        $error = 'Er is een fout opgetreden bij het opslaan van het account. Probeer het later nog eens.';
                    }
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000 || strpos($e->getMessage(), '1062') !== false) {
                        $error = 'Dit e-mailadres of deze gebruikersnaam is al in gebruik.';
                    } else {
                        $error = 'Er is een fout opgetreden bij het opslaan van het account. Probeer het later nog eens.';
                    }
                }
            }
        }

        // Laad de view
        require_once __DIR__ . '/views/create.php';
    }
}
