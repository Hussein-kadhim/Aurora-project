<?php
/*
  Auteur       : KadhimH (Modified by Antigravity)
  Beschrijving : Bevestigingspagina voor het verwijderen van een medewerker.
*/

$medewerker  = $medewerker ?? [];
$naam        = $naam ?? '';
$id          = $id ?? 0;
$dbFout      = $dbFout ?? false;
$foutmelding = $foutmelding ?? '';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#D31027">
    <meta name="description" content="Medewerker verwijderen — Aurora beheerpaneel.">
    <title>Medewerker verwijderen — Aurora</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Premium Navigatiebalk -->
    <?php require_once __DIR__ . '/../../../includes/navbar.php'; ?>

    <!-- Hoofdinhoud -->
    <main class="dashboard-content">
        <div class="container">

            <!-- Terug Link -->
            <div class="back-link-container">
                <a href="index.php" class="back-link">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 16px; height: 16px; margin-right: 6px; vertical-align: middle;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Terug naar overzicht
                </a>
            </div>

            <!-- Dashboard Kop -->
            <div class="dashboard-header-row" style="margin-bottom: 24px;">
                <div class="title-section">
                    <h2>Medewerker verwijderen</h2>
                    <p class="subtitle">Bevestig het verwijderen van de onderstaande medewerker.</p>
                </div>
            </div>

            <!-- Bevestigingskaart -->
            <div class="form-card">
                <div class="delete-warning-box" style="background: rgba(211, 16, 39, 0.06); border: 1px solid rgba(211, 16, 39, 0.2); border-radius: 10px; padding: 24px; margin-bottom: 24px; text-align: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#D31027" style="width: 48px; height: 48px; margin-bottom: 12px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h3 style="color: #D31027; margin: 0 0 8px 0; font-size: 1.1rem;">Let op! Deze actie kan niet ongedaan worden gemaakt.</h3>
                    <p style="color: #555; margin: 0; font-size: 0.95rem;">U staat op het punt om de onderstaande medewerker te verwijderen uit het systeem.</p>
                </div>

                <!-- Medewerker Gegevens Overzicht -->
                <div style="background: #FAFAF5; border: 1px solid #E5D3B3; border-radius: 10px; padding: 20px; margin-bottom: 24px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px 12px; font-weight: 600; color: #131313; width: 160px; vertical-align: top;">Naam</td>
                            <td style="padding: 8px 12px; color: #555;"><?= htmlspecialchars($naam) ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 12px; font-weight: 600; color: #131313; vertical-align: top;">Medewerkernummer</td>
                            <td style="padding: 8px 12px; color: #555;">#<?= (int)($medewerker['MedewerkerNummer'] ?? $medewerker['Nummer'] ?? 0) ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 12px; font-weight: 600; color: #131313; vertical-align: top;">E-mailadres</td>
                            <td style="padding: 8px 12px; color: #555;"><?= htmlspecialchars($medewerker['Email'] ?? $medewerker['Gebruikersnaam'] ?? '—') ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 12px; font-weight: 600; color: #131313; vertical-align: top;">Functie</td>
                            <td style="padding: 8px 12px; color: #555;"><?= htmlspecialchars($medewerker['Medewerkersoort'] ?? '—') ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 12px; font-weight: 600; color: #131313; vertical-align: top;">Systeemrol</td>
                            <td style="padding: 8px 12px; color: #555;"><?= htmlspecialchars($medewerker['Rol'] ?? '—') ?></td>
                        </tr>
                    </table>
                </div>

                <!-- Actieknoppen -->
                <form method="POST" action="index.php?action=delete">
                    <input type="hidden" name="id" value="<?= (int)$id ?>">
                    <div class="form-actions">
                        <button type="submit" class="btn-primary" style="background: #D31027; border-color: #D31027;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 18px; height: 18px; margin-right: 6px; vertical-align: middle;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Definitief verwijderen
                        </button>
                        <a href="index.php" class="btn-secondary">Annuleren</a>
                    </div>
                </form>
            </div>

        </div>
    </main>

    <?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
</body>
</html>
