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
