<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#131313">
    <meta name="description" content="Bestaande melding versturen — Aurora beheerpaneel.">
    <title>Melding versturen — Aurora</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Meldingen.css">
</head>
<body>

    <!-- Navigatiebalk -->
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>

    <main class="dashboard-content">
        <div class="container">

<?php if (!empty($success) && $melding !== null): ?>

    <!-- ============================================================
         SUCCESS STATE — na succesvol versturen
         ============================================================ -->
    <div class="notif-page">

        <!-- Header bar -->
        <div class="notif-topbar">
            <a href="meldingen.php" class="notif-back-btn" title="Terug naar overzicht">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
            </a>
            <span class="notif-topbar-title">NOTIFICATIONS</span>
            <button class="notif-menu-btn" title="Opties">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;">
                    <circle cx="12" cy="5" r="1.5"></circle>
                    <circle cx="12" cy="12" r="1.5"></circle>
                    <circle cx="12" cy="19" r="1.5"></circle>
                </svg>
            </button>
        </div>

        <!-- Succes icoon + titel -->
        <div class="notif-hero">
            <div class="notif-check-circle">
                <svg viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round" style="width:28px;height:28px;">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <h1 class="notif-hero-title">MELDING SUCCESVOL VERZONDEN</h1>
            <p class="notif-hero-sub">Uw bericht is succesvol verwerkt door het systeem.</p>
        </div>

        <!-- Status kaart -->
        <div class="notif-card">
            <div class="notif-card-header">
                <span class="notif-card-label">STATUS UPDATE</span>
                <span class="notif-badge-verzonden">VERZONDEN</span>
            </div>

            <div class="notif-card-body">
                <div class="notif-field">
                    <span class="notif-field-label">REFERENTIE ID</span>
                    <span class="notif-field-value notif-ref">
                        #NTF-<?= htmlspecialchars($melding['Nummer']) ?>-TX
                    </span>
                </div>

                <div class="notif-field">
                    <span class="notif-field-label">BESTEMMING</span>
                    <span class="notif-field-value notif-highlight">Alle Systeemgebruikers</span>
                </div>

                <div class="notif-field">
                    <span class="notif-field-label">ONDERWERP</span>
                    <span class="notif-field-value notif-highlight">
                        <?= htmlspecialchars($melding['Bericht']) ?>
                    </span>
                </div>

                <div class="notif-field">
                    <span class="notif-field-label">TIJDSTIP VAN VERZENDING</span>
                    <span class="notif-field-value">
                        <?= htmlspecialchars(MeldingController::formatDutchDate(date('Y-m-d H:i:s'))) ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Actie knoppen -->
        <div class="notif-actions">
            <a href="meldingen.php" class="notif-btn notif-btn-primary" id="btnTerug">
                TERUG NAAR OVERZICHT
            </a>
            <a href="meldingen.php" class="notif-btn notif-btn-outline" id="btnLogboek">
                BEKIJK LOGBOEK
            </a>
            <a href="meldingen.php" class="notif-btn notif-btn-primary" id="btnNextTask">
                SYSTEM READY FOR NEXT TASK
            </a>
        </div>

    </div>

<?php elseif (!empty($errors) && $melding === null): ?>

    <!-- ============================================================
         DB FOUT — melding kon niet worden geladen
         ============================================================ -->
    <div class="new-melding-container">
        <div class="form-header-bar">
            <a href="meldingen.php" class="back-btn" title="Terug">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
            </a>
            <span class="form-header-title">Melding Versturen</span>
            <span style="width:20px;"></span>
        </div>
        <div class="form-body-wrapper">
            <div class="form-errors-container" role="alert">
                <ul class="form-errors-list">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <a href="meldingen.php" class="btn-submit-form" style="text-decoration:none;margin-top:0;">
                Terug naar overzicht
            </a>
        </div>
    </div>

<?php elseif ($melding !== null): ?>

    <!-- ============================================================
         BEVESTIGINGS FORMULIER — voor het versturen
         ============================================================ -->
    <div class="new-melding-container">

        <div class="form-header-bar">
            <a href="meldingen.php" class="back-btn" title="Terug naar overzicht">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
            </a>
            <span class="form-header-title">Melding Versturen</span>
            <span style="width:20px;"></span>
        </div>

        <div class="form-body-wrapper">
            <div class="form-category-label">Systeem berichten</div>
            <h2 class="form-main-title">Bevestig Verzending</h2>

            <?php if (!empty($errors)): ?>
                <div class="form-errors-container" role="alert">
                    <ul class="form-errors-list">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Preview kaart -->
            <div class="verstuur-preview-card">
                <div class="verstuur-preview-row">
                    <span class="verstuur-preview-label">Nummer</span>
                    <span class="verstuur-preview-value">#<?= htmlspecialchars($melding['Nummer']) ?></span>
                </div>
                <div class="verstuur-preview-row">
                    <span class="verstuur-preview-label">Type</span>
                    <span class="type-badge type-<?= htmlspecialchars(strtolower($melding['Type'])) ?>">
                        <?= htmlspecialchars(ucfirst($melding['Type'])) ?>
                    </span>
                </div>
                <div class="verstuur-preview-row">
                    <span class="verstuur-preview-label">Bericht</span>
                    <span class="verstuur-preview-value"><?= htmlspecialchars($melding['Bericht']) ?></span>
                </div>
                <?php if (!empty($melding['Opmerking'])): ?>
                <div class="verstuur-preview-row">
                    <span class="verstuur-preview-label">Opmerking</span>
                    <span class="verstuur-preview-value verstuur-opmerking"><?= htmlspecialchars($melding['Opmerking']) ?></span>
                </div>
                <?php endif; ?>
                <div class="verstuur-preview-row">
                    <span class="verstuur-preview-label">Datum</span>
                    <span class="verstuur-preview-value">
                        <?= htmlspecialchars(MeldingController::formatDutchDate($melding['DatumAangemaakt'])) ?>
                    </span>
                </div>
                <?php
                    $isActiefVal = $melding['IsActief'];
                    $isActief = ($isActiefVal === "\x01" || $isActiefVal === 1 || $isActiefVal === '1' || $isActiefVal === true);

                    // Afzender bepalen
                    if (!empty($melding['MedewerkerVoornaam'])) {
                        $afzender = trim(
                            $melding['MedewerkerVoornaam'] . ' ' .
                            ($melding['MedewerkerTussenvoegsel'] ?? '') . ' ' .
                            $melding['MedewerkerAchternaam']
                        );
                        $afzenderLabel = 'Medewerker';
                    } elseif (!empty($melding['BezoekerVoornaam'])) {
                        $afzender = trim(
                            $melding['BezoekerVoornaam'] . ' ' .
                            ($melding['BezoekerTussenvoegsel'] ?? '') . ' ' .
                            $melding['BezoekerAchternaam']
                        );
                        $afzenderLabel = 'Bezoeker';
                    } else {
                        $afzender = 'Systeem';
                        $afzenderLabel = '';
                    }
                ?>
                <!-- Afzender -->
                <div class="verstuur-preview-row">
                    <span class="verstuur-preview-label">Afzender</span>
                    <span class="verstuur-preview-value">
                        <?= htmlspecialchars($afzender) ?>
                        <?php if ($afzenderLabel): ?>
                            <span class="verstuur-afzender-rol">(<?= htmlspecialchars($afzenderLabel) ?>)</span>
                        <?php endif; ?>
                    </span>
                </div>
                <!-- Verzenden naar -->
                <div class="verstuur-preview-row">
                    <span class="verstuur-preview-label">Verzenden naar</span>
                    <span class="verstuur-preview-value">
                        <span class="verstuur-bestemming-icon">👥</span> Alle gebruikers
                    </span>
                </div>
                <!-- Huidige status -->
                <div class="verstuur-preview-row">
                    <span class="verstuur-preview-label">Huidige status</span>
                    <?php if ($isActief): ?>
                        <span class="status-indicator status-unread">
                            <span class="status-dot"></span> Ongelezen
                        </span>
                    <?php else: ?>
                        <span class="status-indicator status-verzonden">
                            <span class="status-dot"></span> Verzonden
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Info waarschuwingsblok -->
            <div class="verstuur-warning-block">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round"
                     style="width:18px;height:18px;flex-shrink:0;margin-top:2px;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <p>
                    Door op <strong>Versturen</strong> te klikken wordt de melding verzonden naar
                    <strong>alle gebruikers</strong>. De status wordt bijgewerkt naar
                    <strong>Verzonden</strong>. Dit kan niet ongedaan worden gemaakt.
                </p>
            </div>

            <?php if ($isActief): ?>
                <form id="verstuurForm" method="POST"
                      action="meldingen.php?action=verstuur&id=<?= (int) $melding['Id'] ?>">
                    <button type="submit" class="btn-submit-form btn-verstuur-confirm" id="btnVersturen">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                             stroke-linecap="round" stroke-linejoin="round"
                             style="width:18px;height:18px;margin-right:10px;">
                            <line x1="22" y1="2" x2="11" y2="13"></line>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                        </svg>
                        <span>Melding Versturen</span>
                    </button>
                </form>
            <?php else: ?>
                <div class="verstuur-already-sent">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round"
                         style="width:20px;height:20px;flex-shrink:0;">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>Deze melding is al eerder verzonden. De status kan niet opnieuw worden bijgewerkt.</span>
                </div>
                <a href="meldingen.php" class="btn-submit-form" style="text-decoration:none;margin-top:0;">
                    Terug naar overzicht
                </a>
            <?php endif; ?>

        </div>
    </div>

<?php endif; ?>

        </div>
    </main>

    <?php require_once __DIR__ . '/../../includes/footer.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('verstuurForm');
        const btn  = document.getElementById('btnVersturen');
        if (form && btn) {
            form.addEventListener('submit', function () {
                btn.disabled = true;
                btn.style.opacity   = '0.65';
                btn.style.cursor    = 'not-allowed';
                btn.querySelector('span').textContent = 'Bezig met versturen…';
            });
        }
    });
    </script>

</body>
</html>
