<?php
// Mappen van de ticket type/prijs categorie
$ticketType = 'Regulier Ticket';
if ($ticket !== null && !empty($ticket['PrijsOpmerking'])) {
    $remark = strtolower($ticket['PrijsOpmerking']);
    if (strpos($remark, 'vip') !== false || strpos($remark, 'premium') !== false) {
        $ticketType = 'VIP Ticket';
    } else if (strpos($remark, 'korting') !== false || strpos($remark, 'student') !== false || strpos($remark, 'senioren') !== false) {
        $ticketType = 'Korting Ticket';
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#D31027">
    <title>Ticket Scannen — Aurora</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .scan-card {
            background-color: #FFFFFF;
            border: 1px solid #131313;
            padding: 48px 40px;
            width: 100%;
            max-width: 480px;
            margin: 40px auto 20px;
            text-align: center;
            box-shadow: 0 8px 40px rgba(19, 19, 19, 0.03);
            border-radius: 0px; /* Strakke premium vierkante look uit mockup */
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            border: 2px solid #131313;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 28px;
        }

        .icon-circle.success {
            border-color: #131313;
            color: #131313;
        }

        .icon-circle.error {
            border-color: #D31027;
            color: #D31027;
        }

        .icon-circle.ready {
            border-color: #E5D3B3;
            color: #8c7144;
            background-color: rgba(229, 211, 179, 0.1);
        }

        .icon-circle svg {
            width: 36px;
            height: 36px;
        }

        .scan-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #131313;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .scan-subtitle {
            font-size: 0.95rem;
            color: #131313;
            opacity: 0.6;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .divider-line {
            height: 1px;
            background-color: #E5D3B3;
            border: none;
            margin: 24px 0;
            opacity: 0.6;
        }

        /* Grid */
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px 20px;
            text-align: left;
            margin: 28px 0;
        }

        .details-item {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .details-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #131313;
            opacity: 0.5;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .details-value {
            font-size: 0.95rem;
            font-weight: 700;
            color: #131313;
        }

        /* Status block */
        .status-block {
            background-color: #131313;
            color: #FFFFFF;
            display: inline-block;
            padding: 10px 24px;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin: 12px 0 28px;
        }

        /* Buttons & Forms */
        .btn-scan-action {
            background-color: #131313;
            color: #FFFFFF;
            border: 1px solid #131313;
            padding: 16px 24px;
            width: 100%;
            font-size: 0.95rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            margin-top: 16px;
        }

        .btn-scan-action:hover {
            background-color: #2c2c2c;
            border-color: #2c2c2c;
        }

        .btn-scan-action svg {
            width: 18px;
            height: 18px;
        }

        .link-dashboard {
            display: block;
            color: #131313;
            opacity: 0.6;
            font-size: 0.9rem;
            text-decoration: none;
            text-align: center;
            margin-top: 16px;
            transition: opacity 0.2s;
        }

        .link-dashboard:hover {
            opacity: 1;
            text-decoration: underline;
        }

        /* Input Form */
        .scan-form-group {
            text-align: left;
            margin-bottom: 24px;
        }

        .scan-form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #131313;
            margin-bottom: 8px;
        }

        .scan-input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #E5D3B3;
            border-radius: 4px;
            font-size: 1rem;
            outline: none;
            color: #131313;
            transition: all 0.2s;
        }

        .scan-input:focus {
            border-color: #131313;
            box-shadow: 0 0 0 3px rgba(19, 19, 19, 0.05);
        }

        .error-message-box {
            color: #D31027;
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 24px;
            background: rgba(211, 16, 39, 0.05);
            border: 1px solid rgba(211, 16, 39, 0.15);
            padding: 14px;
            text-align: center;
        }

        .location-footer {
            font-family: monospace;
            font-size: 0.85rem;
            color: #131313;
            opacity: 0.5;
            text-align: center;
            margin-top: 24px;
            margin-bottom: 40px;
            letter-spacing: 0.5px;
        }

        @media (max-width: 768px) {
            .scan-card {
                padding: 36px 24px;
                margin: 20px auto;
                border: 1px solid #131313;
            }

            .details-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }
    </style>
</head>
<body>

    <!-- Premium Navigatiebalk -->
    <?php require_once __DIR__ . '/../../../includes/navbar.php'; ?>

    <main class="dashboard-content">
        <div class="container" style="max-width: 600px;">

            <?php if ($success === true && $ticket !== null): ?>
                <!-- HAPPY SCENARIO: TICKET SUCCESVOL GESCAND -->
                <div class="scan-card">
                    <div class="icon-circle success">
                        <!-- Checkmark Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>

                    <h1 class="scan-title">Ticket Gescand</h1>
                    <p class="scan-subtitle">Toegang verleend voor het volgende ticket</p>
                    
                    <hr class="divider-line">

                    <div class="details-grid">
                        <div class="details-item">
                            <span class="details-label">Ticket ID</span>
                            <span class="details-value">#T-<?= htmlspecialchars($ticket['TicketNummer']) ?></span>
                        </div>
                        <div class="details-item">
                            <span class="details-label">Naam</span>
                            <span class="details-value"><?= htmlspecialchars(trim($ticket['BezoekerVoornaam'] . ' ' . ($ticket['BezoekerTussenvoegsel'] ?? '') . ' ' . $ticket['BezoekerAchternaam'])) ?></span>
                        </div>
                        <div class="details-item">
                            <span class="details-label">Type</span>
                            <span class="details-value"><?= htmlspecialchars($ticketType) ?></span>
                        </div>
                        <div class="details-item">
                            <span class="details-label">Tijdstip Scan</span>
                            <span class="details-value">Vandaag, <?= date('H:i') ?></span>
                        </div>
                    </div>

                    <hr class="divider-line">

                    <div>
                        <span class="status-block">GEBRUIKT</span>
                    </div>

                    <a href="scan.php" class="btn-scan-action">
                        <!-- Barcode/Scan SVG Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        <span>Scan volgende ticket</span>
                    </a>

                    <a href="index.php" class="link-dashboard">Terug naar dashboard</a>
                </div>

            <?php elseif ($success === false): ?>
                <!-- UNHAPPY SCENARIO: TICKET ONGELDIG OF REEDS GEBRUIKT -->
                <div class="scan-card">
                    <div class="icon-circle error">
                        <!-- Cross Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>

                    <h1 class="scan-title">Scan Mislukt</h1>
                    <p class="scan-subtitle" style="margin-bottom: 20px;">De barcode kon niet worden geverifieerd</p>

                    <div class="error-message-box">
                        <?= htmlspecialchars($errorMessage) ?>
                    </div>

                    <form method="post" action="scan.php">
                        <div class="scan-form-group">
                            <label for="barcode">Barcode</label>
                            <input 
                                type="text" 
                                id="barcode" 
                                name="barcode" 
                                class="scan-input" 
                                placeholder="Scan of voer barcode in..." 
                                autocomplete="off"
                                autofocus
                                required
                            >
                        </div>

                        <button type="submit" class="btn-scan-action">
                            <span>Scannen</span>
                        </button>
                    </form>

                    <a href="index.php" class="link-dashboard">Terug naar dashboard</a>
                </div>

            <?php else: ?>
                <!-- INITIAL / READY TO SCAN STATE -->
                <div class="scan-card">
                    <div class="icon-circle ready">
                        <!-- Barcode Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2zM9 16v-6m3 6v-6m3 6v-6" />
                        </svg>
                    </div>

                    <h1 class="scan-title">Ticket Scannen</h1>
                    <p class="scan-subtitle">Scan of voer de barcode in van het ticket om de geldigheid te controleren.</p>

                    <form method="post" action="scan.php">
                        <div class="scan-form-group">
                            <label for="barcode">Barcode</label>
                            <input 
                                type="text" 
                                id="barcode" 
                                name="barcode" 
                                class="scan-input" 
                                placeholder="Scan of voer barcode in..." 
                                autocomplete="off"
                                autofocus
                                required
                            >
                        </div>

                        <button type="submit" class="btn-scan-action">
                            <span>Scannen</span>
                        </button>
                    </form>

                    <a href="index.php" class="link-dashboard">Terug naar dashboard</a>
                </div>
            <?php endif; ?>

            <!-- Locatie Footer -->
            <div class="location-footer">
                
            </div>

        </div>
    </main>

    <!-- Footer include -->
    <?php require_once __DIR__ . '/../../../includes/footer.php'; ?>

</body>
</html>
