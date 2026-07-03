<?php

require_once __DIR__ . '/BestaandeVoorstellingWijzigenModel.php';

class BestaandeVoorstellingWijzigenController {
    private $model;

    public function __construct(PDO $pdo) {
        $this->model = new BestaandeVoorstellingWijzigenModel($pdo);
    }

    public function edit() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Controleer of de gebruiker is ingelogd
        if (empty($_SESSION['ingelogd']) || empty($_SESSION['gebruiker_id'])) {
            header('Location: ../../login.php');
            exit();
        }

        // 2. Beveiliging – alleen medewerkers/beheerders (Role not Bezoeker)
        if (empty($_SESSION['rol']) || $_SESSION['rol'] === 'Bezoeker') {
            header('Location: ../../informatie/home.php');
            exit();
        }

        $dbFout      = false;
        $foutmelding = '';
        $succes      = false;
        $errors      = [];

        // Haal ID op, kan uit POST of GET komen (wij gebruiken POST vanuit de modal)
        $voorstellingId = null;
        if (isset($_POST['id'])) {
            $voorstellingId = (int)$_POST['id'];
        } elseif (isset($_GET['id'])) {
            $voorstellingId = (int)$_GET['id'];
        }

        if (!$voorstellingId) {
            // Geen ID meegegeven, terug naar overzicht
            header('Location: ../voorstelling-beheren/index.php');
            exit();
        }

        // Haal huidige gegevens op om de velden te vullen
        $voorstelling = null;
        try {
            $voorstelling = $this->model->getVoorstellingById($voorstellingId);
            if (!$voorstelling) {
                // Voorstelling bestaat niet
                header('Location: ../voorstelling-beheren/index.php');
                exit();
            }
        } catch (PDOException $e) {
            $dbFout = true;
            $foutmelding = 'Fout bij ophalen van voorstelling uit de database.';
        }

        // 3. Verwerk POST-verzoek als we opslaan (dit gebeurt als 'naam' in POST zit)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['naam'])) {
            $naam            = trim($_POST['naam']            ?? '');
            $beschrijving    = trim($_POST['beschrijving']    ?? '');
            $datum           = trim($_POST['datum']           ?? '');
            $tijd            = trim($_POST['tijd']            ?? '');
            $maxTickets      = trim($_POST['max_tickets']     ?? '');
            $beschikbaarheid = trim($_POST['beschikbaarheid'] ?? '');

            // Validatie
            if ($naam === '') {
                $errors['naam'] = 'Naam is verplicht.';
            }
            if ($datum === '') {
                $errors['datum'] = 'Datum is verplicht.';
            }
            if ($tijd === '') {
                $errors['tijd'] = 'Tijd is verplicht.';
            }
            if ($maxTickets === '' || !is_numeric($maxTickets) || (int)$maxTickets < 1) {
                $errors['max_tickets'] = 'Vul een geldig aantal tickets in (minimaal 1).';
            }
            if ($beschikbaarheid === '') {
                $errors['beschikbaarheid'] = 'Beschikbaarheid is verplicht.';
            }

            if (empty($errors)) {
                try {
                    $this->model->updateVoorstelling(
                        $voorstellingId,
                        $naam,
                        $beschrijving,
                        $datum,
                        $tijd,
                        (int)$maxTickets,
                        $beschikbaarheid
                    );
                    $succes = true;
                    // Update de lokale $voorstelling array zodat het succes-scherm de juiste info toont
                    $voorstelling['Naam'] = $naam;
                    $voorstelling['Beschrijving'] = $beschrijving;
                    $voorstelling['Datum'] = $datum;
                    $voorstelling['Tijd'] = $tijd;
                    $voorstelling['MaxAantalTickets'] = $maxTickets;
                    $voorstelling['Beschikbaarheid'] = $beschikbaarheid;

                } catch (PDOException $e) {
                    $dbFout      = true;
                    $foutmelding = 'De server is momenteel niet bereikbaar. Probeer het later opnieuw.';
                } catch (Throwable $e) {
                    $dbFout      = true;
                    $foutmelding = 'Er is een onverwachte fout opgetreden. Probeer het later opnieuw.';
                }
            }
        }

        // 4. Laad de view
        require_once __DIR__ . '/views/edit.php';
    }
}
