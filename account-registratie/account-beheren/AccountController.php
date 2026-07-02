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

        $dbFout = false;
        $foutmelding = '';
        $accounts = [];
        $totalCount = 0;

        try {
            // 4. Haal data op uit het Model
            $accounts = $this->model->getAllAccounts($search);
            $totalCount = $this->model->getAccountCount();
        } catch (PDOException $e) {
            $dbFout = true;
            $foutmelding = 'De server is momenteel niet bereikbaar';
        } catch (Throwable $e) {
            $dbFout = true;
            $foutmelding = 'Er is een onverwachte fout opgetreden. Probeer het later opnieuw.';
        }

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
            } else {
                try {
                    if ($this->model->emailOfGebruikersnaamBestaat($email, $gebruikersnaam)) {
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

                        // 3. Opslaan via Model (Transactie)
                        $success = $this->model->createAccount($data);

                        if ($success) {
                            header('Location: index.php?success=1');
                            exit();
                        } else {
                            $error = 'Er is een fout opgetreden bij het opslaan van het account. Probeer het later nog eens.';
                        }
                    }
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000 || strpos($e->getMessage(), '1062') !== false) {
                        $error = 'Dit e-mailadres of deze gebruikersnaam is al in gebruik.';
                    } else {
                        $error = 'Er kon geen verbinding worden gemaakt met de database. Probeer het later nog eens.';
                    }
                } catch (Throwable $e) {
                    $error = 'Er is een onverwachte fout opgetreden. Probeer het later opnieuw.';
                }
            }
        }

        // Laad de view
        require_once __DIR__ . '/views/create.php';
    }

    /**
     * Toont het formulier en verwerkt het wijzigen van een bestaand account.
     */
    public function edit() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Controleer of de gebruiker is ingelogd en Administrator is
        if (empty($_SESSION['ingelogd']) || empty($_SESSION['gebruiker_id']) || empty($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrator') {
            require_once __DIR__ . '/../../includes/geen_toegang.php';
            exit();
        }

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            header('Location: index.php');
            exit();
        }

        // Fetch the existing account details
        try {
            $account = $this->model->getAccountById($id);
        } catch (PDOException $e) {
            $account = null;
        }

        if (!$account) {
            header('Location: index.php');
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

            // Validation: Check verplichte velden
            if (empty($voornaam) || empty($achternaam) || empty($gebruikersnaam) || empty($email) || empty($mobiel) || empty($rol)) {
                $error = 'Vul alle verplichte velden in.';
            } else {
                try {
                    // Check if email is in use by another user
                    if ($this->model->emailBestaatVoorAnder($email, $id)) {
                        $error = 'e-mailadres is al ingebruik';
                    } elseif ($this->model->gebruikersnaamBestaatVoorAnder($gebruikersnaam, $id)) {
                        $error = 'Dit e-mailadres of deze gebruikersnaam is al in gebruik.';
                    } else {
                        $data = [
                            'Voornaam'       => $voornaam,
                            'Tussenvoegsel'  => $tussenvoegsel,
                            'Achternaam'     => $achternaam,
                            'Gebruikersnaam' => $gebruikersnaam,
                            'Email'          => $email,
                            'Mobiel'         => $mobiel,
                            'Rol'            => $rol
                        ];

                        if (!empty($wachtwoord)) {
                            $data['Wachtwoord'] = password_hash($wachtwoord, PASSWORD_DEFAULT);
                        } else {
                            $data['Wachtwoord'] = null;
                        }

                        // Update via Model
                        $success = $this->model->updateAccount($id, $data);

                        if ($success) {
                            header('Location: index.php?success_edit=1');
                            exit();
                        } else {
                            $error = 'Er is een fout opgetreden bij het opslaan van het account. Probeer het later nog eens.';
                        }
                    }
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000 || strpos($e->getMessage(), '1062') !== false) {
                        if (strpos($e->getMessage(), 'Email') !== false || strpos($e->getMessage(), 'Contact.Email') !== false) {
                            $error = 'e-mailadres is al ingebruik';
                        } else {
                            $error = 'Dit e-mailadres of deze gebruikersnaam is al in gebruik.';
                        }
                    } else {
                        $error = 'Er kon geen verbinding worden gemaakt met de database. Probeer het later nog eens.';
                    }
                } catch (Throwable $e) {
                    $error = 'Er is een onverwachte fout opgetreden. Probeer het later opnieuw.';
                }
            }
        }

        // Laad de view
        require_once __DIR__ . '/views/edit.php';
    }
}

