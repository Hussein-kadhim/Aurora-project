<?php
$voornaam        = $voornaam ?? '';
$tussenvoegsel   = $tussenvoegsel ?? '';
$achternaam      = $achternaam ?? '';
$email           = $email ?? '';
$mobiel          = $mobiel ?? '';
$medewerkersoort = $medewerkersoort ?? 'Beheerder';
$rol             = $rol ?? 'Medewerker';
$opmerking       = $opmerking ?? '';
$fouten          = $fouten ?? [];
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#D31027">
    <meta name="description" content="Nieuwe medewerker toevoegen — Aurora beheerpaneel.">
    <title>Medewerker toevoegen — Aurora</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
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
                    <h2>Nieuwe medewerker toevoegen</h2>
                    <p class="subtitle">Vul het formulier in om een nieuwe medewerker toegang te geven tot het systeem.</p>
                </div>
            </div>

            <!-- Foutmeldingen -->
            <?php if (!empty($fouten)): ?>
                <div class="alert alert-danger" role="alert">
                    <div style="display: flex; align-items: flex-start;">
                        <svg class="alert-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 20px; height: 20px; margin-right: 8px; flex-shrink: 0; color: #D31027;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <strong style="display: block; margin-bottom: 4px;">Niet alle velden zijn correct ingevuld:</strong>
                            <ul style="margin: 0; padding-left: 20px; font-size: 0.9rem; line-height: 1.5;">
                                <?php foreach ($fouten as $fout): ?>
                                    <li><?= htmlspecialchars($fout) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Formulier Kaart -->
            <div class="form-card">
                <form method="post" action="index.php?action=create" id="addMedewerkerForm" novalidate>
                    
                    <h3 class="form-section-title">Persoonlijke gegevens</h3>
                    <div class="form-grid">
                        <div class="form-group col-2">
                            <label for="voornaam">Voornaam <span class="required-indicator">*</span></label>
                            <input 
                                type="text" 
                                id="voornaam" 
                                name="voornaam" 
                                value="<?= htmlspecialchars($voornaam) ?>" 
                                placeholder="bijv. Jan" 
                                required
                            >
                        </div>
                        <div class="form-group col-1">
                            <label for="tussenvoegsel">Tussenvoegsel</label>
                            <input 
                                type="text" 
                                id="tussenvoegsel" 
                                name="tussenvoegsel" 
                                value="<?= htmlspecialchars($tussenvoegsel) ?>" 
                                placeholder="van de"
                            >
                        </div>
                        <div class="form-group col-3">
                            <label for="achternaam">Achternaam <span class="required-indicator">*</span></label>
                            <input 
                                type="text" 
                                id="achternaam" 
                                name="achternaam" 
                                value="<?= htmlspecialchars($achternaam) ?>" 
                                placeholder="Jansen" 
                                required
                            >
                        </div>
                    </div>

                    <h3 class="form-section-title" style="margin-top: 32px;">Contact &amp; Systeemrol</h3>
                    <div class="form-grid">
                        <div class="form-group col-3">
                            <label for="email">E-mailadres (Gebruikersnaam) <span class="required-indicator">*</span></label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                value="<?= htmlspecialchars($email) ?>" 
                                placeholder="naam@aurora.nl" 
                                required
                            >
                        </div>
                        <div class="form-group col-3">
                            <label for="mobiel">Mobiel telefoonnummer <span class="required-indicator">*</span></label>
                            <input 
                                type="tel" 
                                id="mobiel" 
                                name="mobiel" 
                                value="<?= htmlspecialchars($mobiel) ?>" 
                                placeholder="0612345678" 
                                required
                            >
                        </div>
                        <div class="form-group col-3">
                            <label for="medewerkersoort">Medewerkersoort (Functie) <span class="required-indicator">*</span></label>
                            <select id="medewerkersoort" name="medewerkersoort" required>
                                <option value="Beheerder" <?= $medewerkersoort === 'Beheerder' ? 'selected' : '' ?>>Beheerder</option>
                                <option value="Ticketcontroleur" <?= $medewerkersoort === 'Ticketcontroleur' ? 'selected' : '' ?>>Ticketcontroleur</option>
                                <option value="Planner" <?= $medewerkersoort === 'Planner' ? 'selected' : '' ?>>Planner</option>
                            </select>
                        </div>
                        <div class="form-group col-3">
                            <label for="rol">Systeemrol <span class="required-indicator">*</span></label>
                            <select id="rol" name="rol" required>
                                <option value="Medewerker" <?= $rol === 'Medewerker' ? 'selected' : '' ?>>Medewerker</option>
                                <option value="Administrator" <?= $rol === 'Administrator' ? 'selected' : '' ?>>Administrator</option>
                            </select>
                        </div>
                    </div>

                    <h3 class="form-section-title" style="margin-top: 32px;">Toegangswachtwoord</h3>
                    <div class="form-grid">
                        <div class="form-group col-3">
                            <label for="wachtwoord">Wachtwoord <span class="required-indicator">*</span></label>
                            <input 
                                type="password" 
                                id="wachtwoord" 
                                name="wachtwoord" 
                                placeholder="••••••••" 
                                required
                            >
                            <span class="input-helper">Minimaal 6 tekens.</span>
                        </div>
                        <div class="form-group col-3">
                            <label for="wachtwoord_bevestigen">Wachtwoord bevestigen <span class="required-indicator">*</span></label>
                            <input 
                                type="password" 
                                id="wachtwoord_bevestigen" 
                                name="wachtwoord_bevestigen" 
                                placeholder="••••••••" 
                                required
                            >
                        </div>
                    </div>

                    <h3 class="form-section-title" style="margin-top: 32px;">Overige opmerkingen</h3>
                    <div class="form-grid">
                        <div class="form-group col-6">
                            <label for="opmerking">Opmerking</label>
                            <textarea 
                                id="opmerking" 
                                name="opmerking" 
                                rows="3" 
                                placeholder="Eventuele opmerking over deze medewerker..."
                            ><?= htmlspecialchars($opmerking) ?></textarea>
                        </div>
                    </div>

                    <!-- Knoppen -->
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 18px; height: 18px; margin-right: 6px; vertical-align: middle;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            Opslaan
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
