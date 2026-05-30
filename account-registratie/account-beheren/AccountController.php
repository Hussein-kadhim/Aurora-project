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
            header('Location: ../../login.php');
            exit();
        }

        // 2. Beveiliging: Alleen Administrator heeft toegang
        if (empty($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrator') {
            // Niet gemachtigd -> Stuur terug naar home met een melding of toon foutpagina
            header('Location: ../../informatie/home.php');
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
}
