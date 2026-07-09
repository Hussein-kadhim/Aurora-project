<?php

require_once __DIR__ . '/NieuweVoorstellingModel.php';

class NieuweVoorstellingController {
    private $model;

    public function __construct(PDO $pdo) {
        $this->model = new NieuweVoorstellingModel($pdo);
    }

    public function create() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Controleer of de gebruiker is ingelogd
        if (empty($_SESSION['ingelogd']) || empty($_SESSION['gebruiker_id'])) {
            header('Location: ../../login.php');
            exit();
        }

        // 2. Beveiliging – alleen medewerkers/beheerders
        if (empty($_SESSION['rol']) || $_SESSION['rol'] === 'Bezoeker') {
            header('Location: ../../informatie/home.php');
            exit();
        }

        $dbFout      = false;
        $foutmelding = '';
        $succes      = false;
        $errors      = [];

        // 3. Verwerk POST-verzoek
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                    // Haal MedewerkerId op via de ingelogde gebruiker
                    $gebruikerId  = (int)$_SESSION['gebruiker_id'];
                    $medewerkerId = $this->model->getMedewerkerIdByGebruikerId($gebruikerId);

                    if ($medewerkerId === null) {
                        $dbFout      = true;
                        $foutmelding = 'Uw account is niet gekoppeld aan een medewerkersprofiel. Neem contact op met een beheerder.';
                    } else {
                        $this->model->insertVoorstelling(
                            $medewerkerId,
                            $naam,
                            $beschrijving,
                            $datum,
                            $tijd,
                            (int)$maxTickets,
                            $beschikbaarheid
                        );
                        $succes = true;
                    }
                } catch (PDOException $e) {
                    $dbFout      = true;
                    $foutmelding = 'Er is een fout opgetreden. Probeer het later opnieuw.';
                } catch (Throwable $e) {
                    $dbFout      = true;
                    $foutmelding = 'Er is een fout opgetreden. Probeer het later opnieuw.';
                }
            }
        }

        // 4. Laad de view
        require_once __DIR__ . '/views/create.php';
    }
}
