<?php
/*
  Auteur       : KadhimH
  Datum        : 2026-05-30
  Beschrijving : Tijdelijke homepagina van Aurora. Toont de ingelogde gebruiker
                 en zijn rol. Wordt later uitgebreid met het volledige dashboard.
  Opmerkingen  : Niet ingelogde bezoekers zien een knop om te kunnen inloggen.
*/
session_start();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#D31027">
    <title>Home — Aurora</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #FFFFFF;
            color: #131313;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .kaart {
            background: #FFFFFF;
            border: 1px solid #E5D3B3;
            border-radius: 16px;
            padding: 48px 40px;
            width: 100%;
            max-width: 480px;
            text-align: center;
            box-shadow: 0 8px 40px rgba(19, 19, 19, 0.10);
        }

        .badge {
            display: inline-block;
            background: rgba(211, 16, 39, 0.10);
            color: #D31027;
            border-radius: 20px;
            padding: 4px 14px;
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 20px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #D31027;
            margin-bottom: 10px;
        }

        p {
            color: #131313;
            opacity: 0.6;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .info-rij {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #E5D3B3;
            font-size: 0.9rem;
            text-align: left;
        }

        .info-rij:last-of-type {
            border-bottom: none;
        }

        .info-rij span:first-child {
            color: #131313;
            opacity: 0.5;
        }

        .info-rij span:last-child {
            color: #131313;
            font-weight: 600;
        }

        .btn-uitloggen {
            display: inline-block;
            margin-top: 28px;
            background: transparent;
            color: #D31027;
            border: 1px solid rgba(211, 16, 39, 0.35);
            border-radius: 8px;
            padding: 10px 24px;
            font-size: 0.875rem;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.2s, border-color 0.2s;
        }

        .btn-uitloggen:hover {
            background: rgba(211, 16, 39, 0.08);
            border-color: #D31027;
        }

        .btn-inloggen {
            display: inline-block;
            margin-top: 20px;
            background: #D31027;
            color: #FFFFFF;
            border: none;
            border-radius: 8px;
            padding: 12px 28px;
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.2s;
        }

        .btn-inloggen:hover {
            background: #b50e21;
        }
    </style>
</head>
<body>

<div class="kaart">
    <?php if (!empty($_SESSION['ingelogd'])): ?>
        <div class="badge"><?= htmlspecialchars($_SESSION['rol'] ?? 'Gebruiker') ?></div>
        <h1>Welkom terug!</h1>
        <p>Je bent succesvol ingelogd in het Aurora systeem.</p>

        <div class="info-rij">
            <span>Naam</span>
            <span><?= htmlspecialchars($_SESSION['naam'] ?? '—') ?></span>
        </div>
        <div class="info-rij">
            <span>Gebruikersnaam</span>
            <span><?= htmlspecialchars($_SESSION['gebruikersnaam'] ?? '—') ?></span>
        </div>
        <div class="info-rij">
            <span>Rol</span>
            <span><?= htmlspecialchars($_SESSION['rol'] ?? '—') ?></span>
        </div>

        <a href="../uitloggen.php" class="btn-uitloggen">Uitloggen</a>

    <?php else: ?>
        <h1>Aurora</h1>
        <p>Je bent momenteel niet ingelogd. Log in om toegang te krijgen tot het systeem.</p>
        <a href="../login.php" class="btn-inloggen">Inloggen</a>
    <?php endif; ?>
</div>

</body>
</html>
