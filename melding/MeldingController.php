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

        // 2. Controleer of de gebruiker is ingelogd en toegang heeft (Administrator of Medewerker)
        $rol = $_SESSION['rol'] ?? '';
        if (empty($_SESSION['ingelogd']) || empty($_SESSION['gebruiker_id']) || ($rol !== 'Administrator' && $rol !== 'Medewerker')) {
            require_once __DIR__ . '/../includes/geen_toegang.php';
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

    // ---------------------------------------------------------------------------
    // Helper: render één mobiele kaart (ook gebruikt door de AJAX-route)
    // ---------------------------------------------------------------------------
    private function renderMobileCard(array $row): string {
        $isActief = ($row['IsActief'] === "\x01" || $row['IsActief'] === 1 || $row['IsActief'] === '1' || $row['IsActief'] === true);

        $senderSub = 'Systeemmelding';
        if (!empty($row['BezoekerVoornaam'])) {
            $senderSub = 'Bezoeker: ' . trim($row['BezoekerVoornaam'] . ' ' . ($row['BezoekerTussenvoegsel'] ?? '') . ' ' . $row['BezoekerAchternaam']);
        } elseif (!empty($row['MedewerkerVoornaam'])) {
            $senderSub = 'Medewerker: ' . trim($row['MedewerkerVoornaam'] . ' ' . ($row['MedewerkerTussenvoegsel'] ?? '') . ' ' . $row['MedewerkerAchternaam']);
        }

        $descriptionSubtext = !empty($row['Opmerking']) ? htmlspecialchars($row['Opmerking']) : $senderSub;
        $displayMobileDate  = $this->formatMobileDate($row['DatumAangemaakt']);
        $badgeClass = $isActief ? 'card-badge-nieuw' : 'card-badge-archief';
        $badgeText  = $isActief ? 'NIEUW' : 'ARCHIEF';

        $statusHtml = $isActief
            ? '<span class="status-unread">
                    <svg class="status-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    Ongelezen
               </span>'
            : '<span class="status-read">
                    <svg class="status-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Gelezen
               </span>';

        return '
        <div class="card-item">
            <div class="card-header">
                <span class="card-date">' . htmlspecialchars($displayMobileDate) . '</span>
                <span class="' . $badgeClass . '">' . $badgeText . '</span>
            </div>
            <div class="card-body">
                <div class="card-message">' . htmlspecialchars($row['Bericht']) . '</div>
                <div class="card-subtext">' . $descriptionSubtext . '</div>
            </div>
            <div class="card-footer">
                <span class="badge-outline">' . htmlspecialchars(strtoupper($row['Type'])) . '</span>
                <div class="card-status-wrapper">' . $statusHtml . '</div>
            </div>
        </div>';
    }

    // ---------------------------------------------------------------------------
    // Datumopmaak helpers
    // ---------------------------------------------------------------------------
    public static function formatDutchDate(string $datetime): string {
        if (empty($datetime)) return '';
        $timestamp = strtotime($datetime);
        if (!$timestamp) return htmlspecialchars($datetime);

        $months = [
            1=>'Januari',2=>'Februari',3=>'Maart',4=>'April',5=>'Mei',6=>'Juni',
            7=>'Juli',8=>'Augustus',9=>'September',10=>'Oktober',11=>'November',12=>'December'
        ];

        $day   = date('j', $timestamp);
        $month = $months[(int) date('n', $timestamp)] ?? '';
        $year  = date('Y', $timestamp);
        $time  = date('H:i', $timestamp);

        return "$day $month $year, $time";
    }

    public static function formatMobileDate(string $datetime): string {
        if (empty($datetime)) return '';
        $timestamp = strtotime($datetime);
        if (!$timestamp) return '';

        $months = [
            1=>'JAN',2=>'FEB',3=>'MAR',4=>'APR',5=>'MEI',6=>'JUN',
            7=>'JUL',8=>'AUG',9=>'SEP',10=>'OKT',11=>'NOV',12=>'DEC'
        ];

        $day   = sprintf('%02d', date('j', $timestamp));
        $month = $months[(int) date('n', $timestamp)] ?? '';
        $year  = date('Y', $timestamp);

        return "$day $month $year";
    }

    /**
     * Actie voor het aanmaken van een nieuwe melding.
     */
    public function nieuw() {
        // 1. Zorg dat er een sessie actief is
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 2. Controleer of de gebruiker is ingelogd en toegang heeft (Administrator of Medewerker)
        $rol = $_SESSION['rol'] ?? '';
        if (empty($_SESSION['ingelogd']) || empty($_SESSION['gebruiker_id']) || ($rol !== 'Administrator' && $rol !== 'Medewerker')) {
            require_once __DIR__ . '/../includes/geen_toegang.php';
            exit();
        }

        $errors = [];
        $titel = '';
        $inhoud = '';
        $type = 'Bericht'; // default type/prioriteit
        $delivery = 'INSTANT';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titel = trim($_POST['titel'] ?? '');
            $inhoud = trim($_POST['inhoud'] ?? '');
            $type = trim($_POST['type'] ?? '');
            $delivery = trim($_POST['delivery'] ?? 'INSTANT');

            // Validatie
            if ($titel === '') {
                $errors[] = 'Vul a.u.b. een titel in.';
            } elseif (strlen($titel) > 250) {
                $errors[] = 'De titel mag maximaal 250 tekens lang zijn.';
            }

            if (strlen($inhoud) > 250) {
                $errors[] = 'De inhoud mag maximaal 250 tekens lang zijn.';
            }

            // Type/prioriteit validatie
            $allowedTypes = ['Bericht', 'Klacht', 'Notificatie', 'Review', 'Update', 'Waarschuwing'];
            if (!in_array($type, $allowedTypes)) {
                $type = 'Bericht';
            }

            $isActief = ($delivery === 'INSTANT');

            if (empty($errors)) {
                $gebruikerId = (int)$_SESSION['gebruiker_id'];
                $medewerkerId = $this->model->getMedewerkerIdByGebruikerId($gebruikerId);

                $success = $this->model->createMelding($medewerkerId, $type, $titel, $isActief, $inhoud);

                if ($success) {
                    $_SESSION['success_message'] = 'Melding succesvol aangemaakt en versturd!';
                    header('Location: meldingen.php');
                    exit();
                } else {
                    $errors[] = 'Er is een technische fout opgetreden bij het opslaan van de melding.';
                }
            }
        }

        // Laad de view
        require_once __DIR__ . '/views/nieuw.php';
    }
}
