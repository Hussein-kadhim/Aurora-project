<?php
/*
  Auteur       : KadhimH (Modified by Antigravity)
  Beschrijving : Formulier voor het wijzigen van een bestaande medewerker.
*/

$id              = $id ?? 0;
$voornaam        = $voornaam ?? '';
$tussenvoegsel   = $tussenvoegsel ?? '';
$achternaam      = $achternaam ?? '';
$email           = $email ?? '';
$mobiel          = $mobiel ?? '';
$medewerkersoort = $medewerkersoort ?? 'Beheerder';
$rol             = $rol ?? 'Medewerker';
$opmerking       = $opmerking ?? '';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#D31027">
    <meta name="description" content="Medewerker wijzigen — Aurora beheerpaneel.">
    <title>Medewerker wijzigen — Aurora</title>
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
                    <h2>Medewerker gegevens wijzigen</h2>
                    <p class="subtitle">Pas de gegevens van de medewerker hieronder aan en sla ze op.</p>
                </div>
            </div>

            <!-- Formulier Kaart -->
            <div class="form-card">
                <form method="post" action="index.php?action=edit&id=<?= (int)$id ?>" id="editMedewerkerForm" novalidate>
                    
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
