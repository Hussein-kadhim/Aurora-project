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

        // 1. Controleer of de gebruiker is ingelogd
        if (empty($_SESSION['ingelogd']) || empty($_SESSION['gebruiker_id'])) {
            header('Location: ../../login.php');
            exit();
        }

        // 2. Beveiliging
        if (empty($_SESSION['rol']) || $_SESSION['rol'] === 'Bezoeker') {
            header('Location: ../../informatie/home.php');
            exit();
        }

        $dbFout = false;
        $foutmelding = '';
        $voorstellingen = [];
        $totalCount = 0;
        
        $search = trim($_GET['search'] ?? '');

        try {
            // 3. Haal data op uit het Model
            $voorstellingen = $this->model->getAllVoorstellingen($search);
            $totalCount = $this->model->getVoorstellingCount();
        } catch (PDOException $e) {
            $dbFout = true;
            $foutmelding = 'De server is momenteel niet bereikbaar.';
        } catch (Throwable $e) {
            $dbFout = true;
            $foutmelding = 'Er is een onverwachte fout opgetreden.';
        }

        // 4. Laad de view
        require_once __DIR__ . '/views/index.php';
    }
}
