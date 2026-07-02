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

        // 1. Controleer of de gebruiker is ingelogd en Administrator is
        if (empty($_SESSION['ingelogd']) || empty($_SESSION['gebruiker_id']) || empty($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrator') {
            require_once __DIR__ . '/../../includes/geen_toegang.php';
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

        // 1. Controleer of de gebruiker is ingelogd en Administrator is
        if (empty($_SESSION['ingelogd']) || empty($_SESSION['gebruiker_id']) || empty($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrator') {
            require_once __DIR__ . '/../../includes/geen_toegang.php';
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
                $fouten[] = 'Vul een voornaam in.';
            } elseif (strlen($voornaam) > 50) {
                $fouten[] = 'De voornaam mag maximaal 50 tekens lang zijn.';
            }

            if (strlen($tussenvoegsel) > 10) {
                $fouten[] = 'Het tussenvoegsel mag maximaal 10 tekens lang zijn.';
            }

            if ($achternaam === '') {
                $fouten[] = 'Vul een achternaam in.';
            } elseif (strlen($achternaam) > 50) {
                $fouten[] = 'De achternaam mag maximaal 50 tekens lang zijn.';
            }

            if ($email === '') {
                $fouten[] = 'Vul een e-mailadres in.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $fouten[] = 'Het ingevulde e-mailadres is niet geldig.';
            } elseif (strlen($email) > 100) {
                $fouten[] = 'Het e-mailadres mag maximaal 100 tekens lang zijn.';
            } elseif ($this->model->gebruikersnaamBestaat($email)) {
                $fouten[] = 'Dit e-mailadres is al in gebruik.';
            }

            if ($mobiel === '') {
                $fouten[] = 'Vul een mobiel telefoonnummer in.';
            } elseif (strlen($mobiel) > 20) {
                $fouten[] = 'Het mobiele nummer mag maximaal 20 tekens lang zijn.';
            }

            if ($medewerkersoort === '') {
                $fouten[] = 'Kies een functie/medewerkersoort.';
            } elseif (strlen($medewerkersoort) > 20) {
                $fouten[] = 'De medewerkersoort mag maximaal 20 tekens lang zijn.';
            }

            if ($rol === '') {
                $fouten[] = 'Kies een systeemrol.';
            } elseif (!in_array($rol, ['Medewerker', 'Administrator'])) {
                $fouten[] = 'De geselecteerde rol is niet geldig.';
            }

            if ($wachtwoord === '') {
                $fouten[] = 'Vul een wachtwoord in.';
            } elseif (strlen($wachtwoord) < 6) {
                $fouten[] = 'Het wachtwoord moet minimaal 6 tekens lang zijn.';
            } elseif (strlen($wachtwoord) > 255) {
                $fouten[] = 'Het wachtwoord mag maximaal 255 tekens lang zijn.';
            }

            if ($wachtwoord !== $wachtwoordBev) {
                $fouten[] = 'De wachtwoorden zijn niet identiek.';
            }

            if (strlen($opmerking) > 250) {
                $fouten[] = 'De opmerking mag maximaal 250 tekens lang zijn.';
            }

            // Als er geen fouten zijn, opslaan!
            if (empty($fouten)) {
                try {
                    $saved = $this->model->addMedewerker([
                        'voornaam'        => $voornaam,
                        'tussenvoegsel'   => $tussenvoegsel,
                        'achternaam'      => $achternaam,
                        'email'           => $email,
                        'mobiel'          => $mobiel,
                        'medewerkersoort' => $medewerkersoort,
                        'rol'             => $rol,
                        'wachtwoord'      => $wachtwoord,
                        'opmerking'       => $opmerking
                    ]);

                    if ($saved) {
                        $_SESSION['success'] = 'Nieuwe medewerker ' . htmlspecialchars($voornaam . ' ' . $achternaam) . ' is succesvol toegevoegd.';
                        header('Location: index.php');
                        exit();
                    } else {
                        $fouten[] = 'De medewerker kon niet worden opgeslagen. Probeer het later nog eens.';
                    }
                } catch (PDOException $e) {
                    $fouten[] = 'Er kon geen verbinding worden gemaakt met de database. Probeer het later nog eens.';
                } catch (Throwable $e) {
                    $fouten[] = 'Er is iets fout gegaan bij het verwerken. Probeer het later nog eens.';
                }
            }
        }

        require_once __DIR__ . '/views/create.php';
    }

    /**
     * Actie voor het bewerken van een bestaande medewerker.
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

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            header('Location: index.php');
            exit();
        }

        $fouten = [];

        try {
            $medewerker = $this->model->getMedewerkerById($id);
            if (!$medewerker) {
                $_SESSION['error'] = 'De opgevraagde medewerker bestaat niet of is niet actief.';
                header('Location: index.php');
                exit();
            }
        } catch (PDOException $e) {
            $dbFout = true;
            $foutmelding = 'De server is momenteel niet bereikbaar';
            $medewerkers = [];
            $totalCount = 0;
            $search = '';
            require_once __DIR__ . '/views/index.php';
            exit();
        }

        $gebruikerId = (int)$medewerker['GebruikerId'];

        // Initialiseer formuliervariabelen met database data
        $voornaam        = $medewerker['Voornaam'];
        $tussenvoegsel   = $medewerker['Tussenvoegsel'] ?? '';
        $achternaam      = $medewerker['Achternaam'];
        $email           = $medewerker['Email'] ?? $medewerker['Gebruikersnaam'];
        $mobiel          = $medewerker['Mobiel'] ?? '';
        $medewerkersoort = $medewerker['Medewerkersoort'];
        $rol             = $medewerker['Rol'] ?? 'Medewerker';
        $opmerking       = $medewerker['Opmerking'] ?? '';

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
                $fouten[] = 'Vul een voornaam in.';
            } elseif (strlen($voornaam) > 50) {
                $fouten[] = 'De voornaam mag maximaal 50 tekens lang zijn.';
            }

            if (strlen($tussenvoegsel) > 10) {
                $fouten[] = 'Het tussenvoegsel mag maximaal 10 tekens lang zijn.';
            }

            if ($achternaam === '') {
                $fouten[] = 'Vul een achternaam in.';
            } elseif (strlen($achternaam) > 50) {
                $fouten[] = 'De achternaam mag maximaal 50 tekens lang zijn.';
            }

            if ($email === '') {
                $fouten[] = 'Vul een e-mailadres in.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $fouten[] = 'Het ingevulde e-mailadres is niet geldig.';
            } elseif (strlen($email) > 100) {
                $fouten[] = 'Het e-mailadres mag maximaal 100 tekens lang zijn.';
            } elseif ($this->model->gebruikersnaamBestaatVoorAnder($email, $gebruikerId)) {
                $fouten[] = 'Dit e-mailadres is al in gebruik.';
            }

            if ($mobiel === '') {
                $fouten[] = 'Vul een mobiel telefoonnummer in.';
            } elseif (strlen($mobiel) > 20) {
                $fouten[] = 'Het mobiele nummer mag maximaal 20 tekens lang zijn.';
            }

            if ($medewerkersoort === '') {
                $fouten[] = 'Kies een functie/medewerkersoort.';
            } elseif (strlen($medewerkersoort) > 20) {
                $fouten[] = 'De medewerkersoort mag maximaal 20 tekens lang zijn.';
            }

            if ($rol === '') {
                $fouten[] = 'Kies een systeemrol.';
            } elseif (!in_array($rol, ['Medewerker', 'Administrator'])) {
                $fouten[] = 'De geselecteerde rol is niet geldig.';
            }

            // Wachtwoord is optioneel bij wijzigen, alleen valideren als het is ingevuld
            if ($wachtwoord !== '') {
                if (strlen($wachtwoord) < 6) {
                    $fouten[] = 'Het wachtwoord moet minimaal 6 tekens lang zijn.';
                } elseif (strlen($wachtwoord) > 255) {
                    $fouten[] = 'Het wachtwoord mag maximaal 255 tekens lang zijn.';
                }

                if ($wachtwoord !== $wachtwoordBev) {
                    $fouten[] = 'De wachtwoorden zijn niet identiek.';
                }
            }

            if (strlen($opmerking) > 250) {
                $fouten[] = 'De opmerking mag maximaal 250 tekens lang zijn.';
            }
        }

        require_once __DIR__ . '/views/edit.php';
    }
}
