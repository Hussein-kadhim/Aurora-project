<?php
$errorMessage = $errorMessage ?? '';
$successMessage = $successMessage ?? '';
$voorstellingen = $voorstellingen ?? [];
$ticket = $ticket ?? null;
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#D31027">
    <title>Ticket Wijzigen — Aurora</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .edit-card {
            background-color: #FFFFFF;
            border: 1px solid #E5D3B3;
            padding: 40px;
            width: 100%;
            max-width: 500px;
            margin: 40px auto;
            box-shadow: 0 4px 12px rgba(19, 19, 19, 0.05);
            border-radius: 12px;
        }

        @media (max-width: 576px) {
            .edit-card {
                padding: 20px 14px;
                margin: 20px auto;
                border-radius: 8px;
            }

            .form-select, .form-input, .form-textarea {
                padding: 10px 8px;
                font-size: 0.82rem;
            }
        }

        .edit-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: #131313;
            margin-bottom: 8px;
            text-align: center;
            letter-spacing: -0.5px;
        }

        .edit-subtitle {
            font-size: 0.95rem;
            color: #131313;
            opacity: 0.6;
            margin-bottom: 28px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 24px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            text-align: left;
        }

        .form-group label {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #131313;
        }

        .form-select, .form-input, .form-textarea {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #E5D3B3;
            border-radius: 8px;
            font-size: 0.95rem;
            outline: none;
            color: #131313;
            background-color: #FFFFFF;
            font-family: inherit;
            transition: border-color 0.2s;
        }

        .form-select:focus, .form-input:focus, .form-textarea:focus {
            border-color: #D31027;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.08);
            border-left: 4px solid #10B981;
            color: #059669;
            padding: 14px;
            border-radius: 8px;
            font-size: 0.95rem;
            margin-bottom: 24px;
            font-weight: 600;
            text-align: center;
        }

        .alert-danger {
            background-color: rgba(211, 16, 39, 0.05);
            border-left: 4px solid #D31027;
            color: #D31027;
            padding: 14px;
            border-radius: 8px;
            font-size: 0.95rem;
            margin-bottom: 24px;
            font-weight: 600;
            text-align: center;
        }

        .btn-submit {
            background-color: #D31027;
            color: #FFFFFF;
            border: none;
            border-radius: 8px;
            padding: 16px 24px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: background-color 0.2s;
        }

        .btn-submit:hover {
            background-color: #b50e21;
        }

        .link-cancel {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #131313;
            opacity: 0.6;
            text-decoration: none;
            font-size: 0.9rem;
            transition: opacity 0.2s;
        }

        .link-cancel:hover {
            opacity: 1;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- Premium Navigatiebalk -->
    <?php require_once __DIR__ . '/../../../includes/navbar.php'; ?>

    <main class="dashboard-content">
        <div class="container" style="max-width: 600px; padding: 0 16px;">

            <div class="edit-card">
                <h1 class="edit-title">Ticket Wijzigen</h1>
                <p class="edit-subtitle">Pas de reservering aan voor ticket #T-<?= htmlspecialchars($ticket['TicketNummer'] ?? '') ?></p>

                <?php if (!empty($successMessage)): ?>
                    <div class="alert-success">
                        <?= htmlspecialchars($successMessage) ?>
                    </div>
                    <script>
                        setTimeout(function() {
                            window.location.href = 'index.php';
                        }, 2000);
                    </script>
                <?php endif; ?>

                <?php if (!empty($errorMessage)): ?>
                    <div class="alert-danger">
                        <?= htmlspecialchars($errorMessage) ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="">
                    <div class="form-group">
                        <label>Bezoeker</label>
                        <input type="text" class="form-input" disabled value="<?= htmlspecialchars(trim(($ticket['BezoekerVoornaam'] ?? '') . ' ' . ($ticket['BezoekerTussenvoegsel'] ?? '') . ' ' . ($ticket['BezoekerAchternaam'] ?? ''))) ?>" style="background-color: #FAFAFA; cursor: not-allowed; opacity: 0.8;">
                    </div>

                    <div class="form-group">
                        <label for="voorstelling_id">Kies een voorstelling</label>
                        <select id="voorstelling_id" name="voorstelling_id" class="form-select" required>
                            <?php foreach ($voorstellingen as $vs): ?>
                                <option value="<?= (int) $vs['Id'] ?>" <?= (int)($ticket['VoorstellingId'] ?? 0) === (int)$vs['Id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($vs['Naam']) ?> — <?= htmlspecialchars(date('d-m-Y', strtotime($vs['Datum']))) ?> om <?= htmlspecialchars(substr($vs['Tijd'], 0, 5)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="opmerking">Opmerking / Foutcorrectie</label>
                        <textarea id="opmerking" name="opmerking" class="form-textarea" rows="3" placeholder="Voer eventuele opmerkingen of correcties in..."><?= htmlspecialchars($ticket['Opmerking'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Opslaan</span>
                    </button>
                </form>

                <a href="index.php" class="link-cancel">Annuleren en terug naar overzicht</a>
            </div>

        </div>
    </main>

    <!-- Footer include -->
    <?php require_once __DIR__ . '/../../../includes/footer.php'; ?>

</body>
</html>
