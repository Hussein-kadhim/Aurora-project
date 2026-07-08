<?php

require_once __DIR__ . '/BestaandeVoorstellingVerwijderenModel.php';

class BestaandeVoorstellingVerwijderenController {
    private $model;

    public function __construct(PDO $pdo) {
        $this->model = new BestaandeVoorstellingVerwijderenModel($pdo);
    }

    public function delete() {
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

        // 3. Verwerk POST-verzoek als we verwijderen
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $voorstellingId = (int)$_POST['id'];

            if ($voorstellingId > 0) {
                // Verwijder de voorstelling via het model (soft delete)
                $success = $this->model->verwijderVoorstelling($voorstellingId);

                if ($success) {
                    // Redirect naar overzicht met success flag
                    header('Location: ../voorstelling-beheren/index.php?success_delete=1');
                    exit();
                } else {
                    // Fout bij verwijderen
                    header('Location: ../voorstelling-beheren/index.php?error=delete_failed');
                    exit();
                }
            }
        }

        // Als er geen POST of geldig ID was, stuur terug naar overzicht
        header('Location: ../voorstelling-beheren/index.php');
        exit();
    }
}
