<?php

require_once __DIR__ . '/MeldingModel.php';

class MeldingController {
    private $model;

    public function __construct(PDO $pdo) {
        $this->model = new MeldingModel($pdo);
    }

    /**
     * Startpunt van de controller actie.
     * Beveiligt de route, verwerkt filters, haalt data op en laadt de view.
     */
    public function index() {
        // 1. Zorg dat er een sessie actief is
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 2. Controleer of de gebruiker is ingelogd
        if (empty($_SESSION['ingelogd']) || empty($_SESSION['gebruiker_id'])) {
            header('Location: ../login.php');
            exit();
        }

        // 3. Beveiliging: Alleen Medewerker en Administrator hebben toegang
        $rol = $_SESSION['rol'] ?? '';
        if ($rol !== 'Administrator' && $rol !== 'Medewerker') {
            header('Location: ../informatie/home.php');
            exit();
        }

        // 4. Lees filterparameters uit de querystring
        $filterType   = trim($_GET['type']   ?? '');
        $filterStatus = trim($_GET['status'] ?? '');
        $filterDate   = trim($_GET['date']   ?? '');

        // 5. Paginering
        $limit      = 5;
        $page       = max(1, (int) ($_GET['page'] ?? 1));
        $offset     = ($page - 1) * $limit;
        $techError  = false;
        $meldingen  = [];
        $totalFiltered = 0;

        try {
            $totalFiltered = $this->model->countMeldingen($filterType, $filterStatus, $filterDate);
            $meldingen     = $this->model->getMeldingen($filterType, $filterStatus, $filterDate, $limit, $offset);
        } catch (PDOException $e) {
            $techError = true;
        } catch (Throwable $e) {
            $techError = true;
        }

        // 6. Bereken paginabereik
        $totalPages = $totalFiltered > 0 ? (int) ceil($totalFiltered / $limit) : 1;
        $startRange = $totalFiltered > 0 ? $offset + 1 : 0;
        $endRange   = min($offset + $limit, $totalFiltered);

        // 7. Beantwoord AJAX-verzoeken voor mobiel "Laad meer"
        if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
            header('Content-Type: application/json');
            $html = '';
            foreach ($meldingen as $row) {
                $html .= $this->renderMobileCard($row);
            }
            echo json_encode([
                'html'    => $html,
                'hasMore' => ($page < $totalPages),
                'page'    => $page,
            ]);
            exit();
        }

        // 8. Laad de view
        require_once __DIR__ . '/views/meldingen.php';
    }
}
