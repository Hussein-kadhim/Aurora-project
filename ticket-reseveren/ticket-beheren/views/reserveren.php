<?php
$reservedTickets = isset($reservedTickets) ? $reservedTickets : [];
$gekozenVoorstelling = isset($gekozenVoorstelling) ? $gekozenVoorstelling : null;
$voorstellingen = isset($voorstellingen) ? $voorstellingen : [];
$errorMessage = isset($errorMessage) ? $errorMessage : '';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#D31027">
    <title>Tickets Reserveren — Aurora</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .reserve-card {
            background-color: #FFFFFF;
            border: 1px solid #E5D3B3;
            padding: 48px 40px;
            width: 100%;
            max-width: 550px;
            margin: 40px auto 40px;
            box-shadow: 0 8px 40px rgba(19, 19, 19, 0.03);
            border-radius: 12px;
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            border: 2px solid #D31027;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 28px;
        }

        .icon-circle.success {
            border-color: #10B981;
            color: #10B981;
            background-color: rgba(16, 185, 129, 0.06);
        }

        .icon-circle.success svg {
            width: 36px;
            height: 36px;
        }

        .reserve-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #131313;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
            text-align: center;
        }

        .reserve-subtitle {
            font-size: 0.95rem;
            color: #131313;
            opacity: 0.6;
            line-height: 1.6;
            margin-bottom: 32px;
            text-align: center;
        }

        .divider-line {
            height: 1px;
            background-color: #E5D3B3;
            border: none;
            margin: 24px 0;
            opacity: 0.6;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            text-align: left;
            margin: 24px 0;
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

        .form-group {
            text-align: left;
            margin-bottom: 24px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #131313;
        }

        .form-select, .form-input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #E5D3B3;
            border-radius: 8px;
            font-size: 0.95rem;
            outline: none;
            color: #131313;
            background-color: #FFFFFF;
            transition: all 0.2s;
            font-family: inherit;
        }

        .form-select:focus, .form-input:focus {
            border-color: #D31027;
            box-shadow: 0 0 0 3px rgba(211, 16, 39, 0.05);
        }

        .btn-reserve-action {
            background-color: #D31027;
            color: #FFFFFF;
            border: none;
            border-radius: 8px;
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
            transition: background-color 0.2s, transform 0.1s;
            margin-top: 8px;
        }

        .btn-reserve-action:hover {
            background-color: #b50e21;
        }

        .btn-reserve-action:active {
            transform: scale(0.98);
        }

        .link-back {
            display: block;
            color: #131313;
            opacity: 0.6;
            font-size: 0.9rem;
            text-decoration: none;
            text-align: center;
            margin-top: 24px;
            transition: opacity 0.2s;
        }

        .link-back:hover {
            opacity: 1;
            text-decoration: underline;
        }

        .error-message-box {
            color: #D31027;
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 24px;
            background: rgba(211, 16, 39, 0.05);
            border: 1px solid rgba(211, 16, 39, 0.15);
            border-radius: 8px;
            padding: 14px;
            text-align: center;
        }

        /* Ticket result cards */
        .ticket-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin: 20px 0;
        }

        .ticket-badge-card {
            background: #FAFAFA;
            border: 1px solid #E5D3B3;
            border-radius: 8px;
            padding: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .ticket-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .ticket-num {
            font-size: 0.85rem;
            font-weight: 700;
            color: #131313;
        }

        .ticket-barcode-text {
            font-family: monospace;
            font-size: 0.9rem;
            color: #D31027;
            font-weight: 600;
        }

        .barcode-visual {
            font-family: 'Libre Barcode 39', monospace;
            font-size: 2rem;
            letter-spacing: 2px;
            opacity: 0.85;
            user-select: none;
        }

        @media (max-width: 600px) {
            .reserve-card {
                padding: 32px 24px;
                margin: 20px 12px;
            }

            .details-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
        }
    </style>
</head>
<body>

    <!-- Premium Navigatiebalk -->
    <?php require_once __DIR__ . '/../../../includes/navbar.php'; ?>

    <main class="dashboard-content">
        <div class="container" style="max-width: 650px;">

            <?php if (isset($success) && $success === true && !empty($reservedTickets)): ?>
                <!-- HAPPY SCENARIO: RESERVERING GESLAAGD -->
                <div class="reserve-card">
                    <div class="icon-circle success">
                        <!-- Checkmark Icon -->
                        <i class="fa-solid fa-check" style="font-size: 2.2rem;"></i>
                    </div>

                    <h1 class="reserve-title">Reservering Bevestigd</h1>
                    <p class="reserve-subtitle">Uw tickets zijn succesvol gereserveerd. Bewaar de barcodes goed.</p>
                    
                    <hr class="divider-line">

                    <h3 class="details-label" style="margin-bottom: 12px;">Voorstelling Details</h3>
                    <div class="details-grid">
                        <div class="details-item">
                            <span class="details-label">Voorstelling</span>
                            <span class="details-value"><?= htmlspecialchars(isset($gekozenVoorstelling['Naam']) ? $gekozenVoorstelling['Naam'] : 'Voorstelling') ?></span>
                        </div>
                        <div class="details-item">
                            <span class="details-label">Datum</span>
                            <span class="details-value"><?= htmlspecialchars(isset($gekozenVoorstelling['Datum']) ? date('d-m-Y', strtotime($gekozenVoorstelling['Datum'])) : '') ?></span>
                        </div>
                        <div class="details-item">
                            <span class="details-label">Tijdstip</span>
                            <span class="details-value"><?= htmlspecialchars(isset($gekozenVoorstelling['Tijd']) ? substr($gekozenVoorstelling['Tijd'], 0, 5) : '') ?></span>
                        </div>
                        <div class="details-item">
                            <span class="details-label">Aantal Tickets</span>
                            <span class="details-value"><?= count($reservedTickets) ?> ticket(s)</span>
                        </div>
                    </div>

                    <hr class="divider-line">

                    <h3 class="details-label" style="margin-bottom: 12px;">Uw Tickets</h3>
                    <div class="ticket-list">
                        <?php foreach ($reservedTickets as $ticketItem): ?>
                            <div class="ticket-badge-card">
                                <div class="ticket-info">
                                    <span class="ticket-num">Ticket #<?= htmlspecialchars($ticketItem['Nummer']) ?></span>
                                    <span class="ticket-barcode-text"><?= htmlspecialchars($ticketItem['Barcode']) ?></span>
                                </div>
                                <!-- Barcode visual representation using simple styled blocks for standard look -->
                                <div style="display: flex; gap: 2px; height: 35px; align-items: center; opacity: 0.7;">
                                    <?php for($k = 0; $k < 15; $k++): ?>
                                        <div style="width: <?= rand(1, 4) ?>px; height: 100%; background: #131313;"></div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <a href="index.php" class="btn-reserve-action">
                        <span>Naar Ticketoverzicht</span>
                    </a>

                    <a href="../../informatie/home.php" class="link-back">Terug naar Home</a>
                </div>

            <?php else: ?>
                <!-- RESERVERINGSFORMULIER -->
                <div class="reserve-card">
                    <h1 class="reserve-title">Ticket Reserveren</h1>
                    <p class="reserve-subtitle">Kies een voorstelling en selecteer het aantal tickets dat u wilt boeken.</p>

                    <?php if (!empty($errorMessage)): ?>
                        <div class="error-message-box">
                            <?= htmlspecialchars($errorMessage) ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="reserveren.php">
                        <div class="form-group">
                            <label for="voorstelling_id">Kies een voorstelling</label>
                            <select id="voorstelling_id" name="voorstelling_id" class="form-select" required>
                                <option value="" disabled selected>Selecteer een voorstelling...</option>
                                <?php foreach ((isset($voorstellingen) ? $voorstellingen : []) as $vs): ?>
                                    <option value="<?= (int) $vs['Id'] ?>" <?= isset($_POST['voorstelling_id']) && (int)$_POST['voorstelling_id'] === (int)$vs['Id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($vs['Naam']) ?> — <?= htmlspecialchars(date('d-m-Y', strtotime($vs['Datum']))) ?> om <?= htmlspecialchars(substr($vs['Tijd'], 0, 5)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="aantal">Aantal tickets</label>
                            <input 
                                type="number" 
                                id="aantal" 
                                name="aantal" 
                                class="form-input" 
                                min="1" 
                                max="10" 
                                value="<?= htmlspecialchars($_POST['aantal'] ?? '1') ?>"
                                required
                            >
                        </div>

                        <button type="submit" class="btn-reserve-action">
                            <i class="fa-solid fa-ticket"></i>
                            <span>Reserveren</span>
                        </button>
                    </form>

                    <a href="index.php" class="link-back">Annuleren en terug naar overzicht</a>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <!-- Footer include -->
    <?php require_once __DIR__ . '/../../../includes/footer.php'; ?>

</body>
</html>
