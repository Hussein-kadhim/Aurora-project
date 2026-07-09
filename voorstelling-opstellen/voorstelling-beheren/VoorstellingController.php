<?php

require_once __DIR__ . '/VoorstellingModel.php';

class VoorstellingController {
    private $model;

    public function __construct(PDO $pdo) {
        $this->model = new VoorstellingModel($pdo);
    }

    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Controleer of de gebruiker is ingelogd en toegang heeft (Administrator of Medewerker)
        $rol = $_SESSION['rol'] ?? '';
        if (empty($_SESSION['ingelogd']) || empty($_SESSION['gebruiker_id']) || ($rol !== 'Administrator' && $rol !== 'Medewerker')) {
            require_once __DIR__ . '/../../includes/geen_toegang.php';
            exit();
        }

        $dbFout = false;
        $foutmelding = '';
        $voorstellingen = [];
        $totalCount = 0;
        
        $search = trim($_GET['search'] ?? '');

        // Unhappy scenario voor verwijderen
        if (isset($_GET['error']) && $_GET['error'] === 'delete_failed') {
            $dbFout = true;
            $foutmelding = 'Er is een fout opgetreden. Probeer het later opnieuw.';
        }

        try {
            // 3. Haal data op uit het Model
            $voorstellingen = $this->model->getAllVoorstellingen($search);
            $totalCount = $this->model->getVoorstellingCount();
        } catch (PDOException $e) {
            $dbFout = true;
            $foutmelding = 'Er is een fout opgetreden. Probeer het later opnieuw.';
        } catch (Throwable $e) {
            $dbFout = true;
            $foutmelding = 'Er is een fout opgetreden. Probeer het later opnieuw.';
        }

        // 4. Laad de view
        require_once __DIR__ . '/views/index.php';
    }
}
