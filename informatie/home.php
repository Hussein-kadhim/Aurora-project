<?php
/*
  Auteur       : KadhimH
  Datum        : 2026-06-02
  Beschrijving : Homepagina van het Aurora systeem. Toont welkomstbericht,
                 menu-opties op basis van rol en aankomende voorstellingen.
  Opmerkingen  : Happy scenario: ingelogd gebruiker ziet alle functies.
                 Unhappy scenario: databasefout toont foutmelding.
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$dbFout      = false;
$foutmelding = '';
$voorstellingen = [];

try {
    require_once __DIR__ . '/../config.php';

    // Haal de eerstvolgende actieve, niet-geannuleerde voorstellingen op
    $stmt = $pdo->prepare("
        SELECT Naam, Beschrijving, Datum, Tijd, MaxAantalTickets, Beschikbaarheid
        FROM voorstelling
        WHERE IsActief = 1 AND Beschikbaarheid != 'Geannuleerd' AND Datum >= CURDATE()
        ORDER BY Datum ASC, Tijd ASC
        LIMIT 6
    ");
    $stmt->execute();
    $voorstellingen = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $dbFout      = true;
    $foutmelding = 'De server is momenteel niet bereikbaar. Probeer het later opnieuw.';
} catch (Throwable $e) {
    $dbFout      = true;
    $foutmelding = 'Er is een onverwachte fout opgetreden. Probeer het later opnieuw.';
}

$isIngelogd = !empty($_SESSION['ingelogd']);
$rol        = $_SESSION['rol'] ?? '';
$naam       = $_SESSION['naam'] ?? '';
$isAdmin    = ($rol === 'Administrator');
$isStaff    = ($rol === 'Administrator' || $rol === 'Medewerker');
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#D31027">
    <meta name="description" content="Aurora — uw portaal voor het reserveren van tickets, beheren van voorstellingen en meer.">
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
            flex-direction: column;
        }

        /* ── Hero ── */
        .hero {
            background: linear-gradient(135deg, rgba(211,16,39,0.88) 0%, rgba(90,6,18,0.92) 100%),
                        url('../assets/images/theater-hero.png') center/cover no-repeat;
            color: #FFFFFF;
            padding: 80px 24px 72px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 70% 50%, rgba(229,211,179,0.10) 0%, transparent 65%);
            pointer-events: none;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.18);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 20px;
            padding: 5px 18px;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 22px;
        }

        .hero-badge svg {
            width: 16px;
            height: 16px;
        }

        .hero h1 {
            font-size: clamp(2rem, 5vw, 3.2rem);
            font-weight: 700;
            letter-spacing: -1px;
            margin-bottom: 14px;
            line-height: 1.15;
        }

        .hero p {
            font-size: 1.05rem;
            opacity: 0.85;
            max-width: 540px;
            margin: 0 auto 32px;
            line-height: 1.7;
        }

        .hero-acties {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-hero-primary {
            background: #FFFFFF;
            color: #D31027;
            border: none;
            border-radius: 8px;
            padding: 12px 28px;
            font-size: 0.95rem;
            font-weight: 700;
            text-decoration: none;
            transition: transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 4px 16px rgba(0,0,0,0.18);
        }

        .btn-hero-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.22);
        }

        .btn-hero-secondary {
            background: transparent;
            color: #FFFFFF;
            border: 1.5px solid rgba(255,255,255,0.6);
            border-radius: 8px;
            padding: 12px 28px;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.2s, border-color 0.2s;
        }

        .btn-hero-secondary:hover {
            background: rgba(255,255,255,0.12);
            border-color: #FFFFFF;
        }

        /* ── Foutmelding ── */
        .foutcontainer {
            max-width: 860px;
            margin: 40px auto 0;
            padding: 0 24px;
        }

        .foutmelding-blok {
            background: rgba(211, 16, 39, 0.07);
            border-left: 4px solid #D31027;
            border-radius: 10px;
            padding: 20px 24px;
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }

        .foutmelding-blok .fout-icoon {
            flex-shrink: 0;
            width: 24px;
            height: 24px;
            color: #D31027;
        }

        .foutmelding-blok .fout-tekst strong {
            display: block;
            color: #D31027;
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .foutmelding-blok .fout-tekst span {
            color: #131313;
            font-size: 0.88rem;
            opacity: 0.7;
        }

        /* ── Hoofd inhoud ── */
        .home-main {
            flex: 1;
            max-width: 1100px;
            margin: 0 auto;
            padding: 56px 24px 64px;
            width: 100%;
        }

        /* ── Sectie koptekst ── */
        .sectie-kop {
            margin-bottom: 28px;
        }

        .sectie-kop h2 {
            font-size: 1.35rem;
            font-weight: 700;
            color: #131313;
            letter-spacing: -0.3px;
        }

        .sectie-kop p {
            font-size: 0.88rem;
            color: #131313;
            opacity: 0.5;
            margin-top: 4px;
        }

        /* ── Snelkoppelingen kaarten ── */
        .kaart-raster {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
            gap: 16px;
            margin-bottom: 56px;
        }

        .actie-kaart {
            background: #FFFFFF;
            border: 1px solid #E5D3B3;
            border-radius: 14px;
            padding: 28px 24px;
            text-decoration: none;
            color: #131313;
            display: flex;
            flex-direction: column;
            gap: 12px;
            box-shadow: 0 2px 12px rgba(19,19,19,0.06);
            transition: transform 0.18s, box-shadow 0.18s, border-color 0.18s;
            cursor: pointer;
        }

        .actie-kaart:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 28px rgba(19,19,19,0.12);
            border-color: #D31027;
        }

        .actie-kaart .kaart-icoon {
            width: 44px;
            height: 44px;
            background: rgba(211, 16, 39, 0.09);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #D31027;
        }

        .actie-kaart .kaart-icoon svg {
            width: 22px;
            height: 22px;
        }

        .actie-kaart h3 {
            font-size: 0.95rem;
            font-weight: 700;
            color: #131313;
        }

        .actie-kaart p {
            font-size: 0.82rem;
            color: #131313;
            opacity: 0.5;
            line-height: 1.5;
        }

        .actie-kaart .pijl {
            margin-top: auto;
            font-size: 0.82rem;
            font-weight: 600;
            color: #D31027;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* ── Komende voorstellingen ── */
        .voorstelling-raster {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .voorstelling-kaart {
            background: #FFFFFF;
            border: 1px solid #E5D3B3;
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 2px 12px rgba(19,19,19,0.06);
            display: flex;
            flex-direction: column;
            gap: 10px;
            transition: box-shadow 0.18s, border-color 0.18s;
        }

        .voorstelling-kaart:hover {
            box-shadow: 0 8px 28px rgba(19,19,19,0.10);
            border-color: #c8b59a;
        }

        .vs-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 6px;
            padding: 3px 10px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            width: fit-content;
        }

        .vs-badge.beschikbaar {
            background: rgba(16, 185, 129, 0.10);
            color: #059669;
        }

        .vs-badge.beschikbaar .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #10B981;
        }

        .vs-badge.uitverkocht {
            background: rgba(19,19,19,0.08);
            color: #555;
        }

        .vs-badge.uitverkocht .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #999;
        }

        .voorstelling-kaart h3 {
            font-size: 1rem;
            font-weight: 700;
            color: #131313;
            line-height: 1.3;
        }

        .voorstelling-kaart .vs-beschrijving {
            font-size: 0.85rem;
            color: #131313;
            opacity: 0.55;
            line-height: 1.55;
        }

        .vs-info-rij {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-top: 4px;
        }

        .vs-info-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.82rem;
            color: #131313;
            opacity: 0.6;
        }

        .vs-info-item svg {
            width: 15px;
            height: 15px;
            opacity: 0.7;
        }

        .geen-voorstellingen {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px 20px;
            color: #131313;
            opacity: 0.4;
            font-size: 0.95rem;
        }

        /* ── Divider ── */
        .sectie-divider {
            height: 1px;
            background: #E5D3B3;
            margin-bottom: 48px;
        }

        /* ── Welkomstbanner (ingelogd) ── */
        .welkom-banner {
            background: linear-gradient(135deg, rgba(211,16,39,0.06) 0%, rgba(229,211,179,0.2) 100%);
            border: 1px solid #E5D3B3;
            border-radius: 14px;
            padding: 24px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 48px;
            flex-wrap: wrap;
        }

        .welkom-banner .welkom-tekst h2 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #131313;
            margin-bottom: 4px;
        }

        .welkom-banner .welkom-tekst p {
            font-size: 0.85rem;
            color: #131313;
            opacity: 0.55;
        }

        .welkom-banner .rol-badge {
            display: inline-block;
            background: #D31027;
            color: #FFFFFF;
            border-radius: 6px;
            padding: 5px 14px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.4px;
            white-space: nowrap;
        }

        @media (max-width: 600px) {
            .hero {
                padding: 52px 20px 48px;
            }
            .home-main {
                padding: 36px 16px 48px;
            }
            .welkom-banner {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<!-- Hero sectie -->
<section class="hero">
    <div class="hero-badge">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
        Welkom bij Aurora
    </div>
    <h1>Jouw podium voor<br>onvergetelijke ervaringen</h1>
    <p>Reserveer tickets, bekijk aankomende voorstellingen en beheer uw account — alles op een plek.</p>
    <div class="hero-acties">
        <?php if ($isIngelogd): ?>
            <a href="../ticket-reseveren/ticket-beheren/index.php" class="btn-hero-primary" id="btn-tickets-reserveren">Tickets reserveren</a>
            <a href="../uitloggen.php" class="btn-hero-secondary" id="btn-uitloggen-hero">Uitloggen</a>
        <?php else: ?>
            <a href="../login.php" class="btn-hero-primary" id="btn-inloggen-hero">Inloggen</a>
            <a href="#voorstellingen" class="btn-hero-secondary" id="btn-bekijk-voorstellingen">Bekijk voorstellingen</a>
        <?php endif; ?>
    </div>
</section>

<!-- Databasefout banner -->
<?php if ($dbFout): ?>
<div class="foutcontainer" role="alert" aria-live="assertive">
    <div class="foutmelding-blok">
        <div class="fout-icoon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div class="fout-tekst">
            <strong>Server niet bereikbaar</strong>
            <span><?= htmlspecialchars($foutmelding) ?></span>
        </div>
    </div>
</div>
<?php endif; ?>

<main class="home-main">

    <!-- Welkomstbanner voor ingelogde gebruikers -->
    <?php if ($isIngelogd): ?>
    <div class="welkom-banner" role="complementary" aria-label="Welkomstbericht">
        <div class="welkom-tekst">
            <h2>Welkom terug, <?= htmlspecialchars($naam) ?></h2>
            <p>U bent ingelogd en heeft toegang tot alle functies voor uw rol.</p>
        </div>
        <span class="rol-badge"><?= htmlspecialchars($rol) ?></span>
    </div>
    <?php endif; ?>

    <!-- Snelkoppelingen op basis van rol -->
    <section aria-labelledby="kop-snelkoppelingen">
        <div class="sectie-kop">
            <h2 id="kop-snelkoppelingen">Snelkoppelingen</h2>
            <p>Navigeer snel naar de meest gebruikte functies</p>
        </div>

        <div class="kaart-raster">

            <!-- Altijd zichtbaar: Voorstellingen bekijken -->
            <a href="#voorstellingen" class="actie-kaart" id="link-voorstellingen" aria-label="Ga naar aankomende voorstellingen">
                <div class="kaart-icoon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </div>
                <h3>Voorstellingen</h3>
                <p>Bekijk aankomende shows, ballets en theaterproducties.</p>
                <span class="pijl">Bekijken &rarr;</span>
            </a>

            <!-- Bezoeker / Ingelogd -->
            <?php if ($isIngelogd): ?>
            <a href="../ticket-reseveren/ticket-beheren/index.php" class="actie-kaart" id="link-tickets" aria-label="Ga naar ticket reserveren">
                <div class="kaart-icoon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                </div>
                <h3>Ticket reserveren</h3>
                <p>Reserveer uw tickets voor aankomende voorstellingen.</p>
                <span class="pijl">Reserveren &rarr;</span>
            </a>
            <?php else: ?>
            <a href="../login.php" class="actie-kaart" id="link-inloggen" aria-label="Ga naar inlogpagina">
                <div class="kaart-icoon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h3>Inloggen</h3>
                <p>Log in om tickets te reserveren en uw account te beheren.</p>
                <span class="pijl">Inloggen &rarr;</span>
            </a>
            <?php endif; ?>

            <!-- Medewerker & Beheerder -->
            <?php if ($isStaff): ?>
            <a href="../ticket-reseveren/ticket-beheren/index.php" class="actie-kaart" id="link-ticket-beheren" aria-label="Ga naar ticket beheren">
                <div class="kaart-icoon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                </div>
                <h3>Ticket beheren</h3>
                <p>Controleer en beheer gereserveerde tickets van bezoekers.</p>
                <span class="pijl">Beheren &rarr;</span>
            </a>
            <?php endif; ?>

            <!-- Alleen Beheerder -->
            <?php if ($isAdmin): ?>
            <a href="../account-registratie/account-beheren/index.php" class="actie-kaart" id="link-accounts" aria-label="Ga naar account beheren">
                <div class="kaart-icoon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <h3>Account beheren</h3>
                <p>Voeg gebruikers toe, wijzig rollen en beheer toegangsrechten.</p>
                <span class="pijl">Beheren &rarr;</span>
            </a>

            <a href="../voorstelling-opstellen/voorstelling-beheren/index.php" class="actie-kaart" id="link-voorstelling-beheren"
               onclick="alert('Voorstelling beheren is nog in ontwikkeling.'); return false;"
               aria-label="Ga naar voorstelling beheren (in ontwikkeling)">
                <div class="kaart-icoon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </div>
                <h3>Voorstelling beheren</h3>
                <p>Plan en bewerk theatervoorstellingen en programmaonderdelen.</p>
                <span class="pijl">Beheren &rarr;</span>
            </a>

            <a href="../melding/melding-beheren/index.php" class="actie-kaart" id="link-meldingen"
               onclick="alert('Melding beheren is nog in ontwikkeling.'); return false;"
               aria-label="Ga naar melding beheren (in ontwikkeling)">
                <div class="kaart-icoon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <h3>Melding beheren</h3>
                <p>Bekijk reviews, klachten en systeemnotificaties van bezoekers.</p>
                <span class="pijl">Beheren &rarr;</span>
            </a>

            <a href="../medewerker-registratie/medewerker-beheren/index.php" class="actie-kaart" id="link-medewerkers" aria-label="Ga naar medewerker beheren">
                <div class="kaart-icoon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h3>Medewerker beheren</h3>
                <p>Registreer en beheer medewerkers en hun functies.</p>
                <span class="pijl">Beheren &rarr;</span>
            </a>
            <?php endif; ?>

        </div>
    </section>

    <div class="sectie-divider"></div>

    <!-- Aankomende voorstellingen -->
    <section id="voorstellingen" aria-labelledby="kop-voorstellingen">
        <div class="sectie-kop">
            <h2 id="kop-voorstellingen">Aankomende voorstellingen</h2>
            <p>Een overzicht van de eerstvolgende shows in het Aurora theater</p>
        </div>

        <?php if ($dbFout): ?>
            <!-- Databasefout: voorstellingen kunnen niet worden geladen -->
            <div class="voorstelling-raster">
                <div class="geen-voorstellingen" role="status">
                    Voorstellingen kunnen momenteel niet worden geladen. Probeer het later opnieuw.
                </div>
            </div>

        <?php elseif (empty($voorstellingen)): ?>
            <div class="voorstelling-raster">
                <div class="geen-voorstellingen" role="status">
                    Er zijn momenteel geen aankomende voorstellingen gepland.
                </div>
            </div>

        <?php else: ?>
            <div class="voorstelling-raster">
                <?php foreach ($voorstellingen as $vs):
                    $datum = date('d F Y', strtotime($vs['Datum']));
                    $tijd  = substr($vs['Tijd'], 0, 5);
                    $isUitverkocht = ($vs['Beschikbaarheid'] === 'Uitverkocht');
                ?>
                <article class="voorstelling-kaart">
                    <div class="vs-badge <?= $isUitverkocht ? 'uitverkocht' : 'beschikbaar' ?>">
                        <span class="status-dot"></span>
                        <?= $isUitverkocht ? 'Uitverkocht' : 'Beschikbaar' ?>
                    </div>
                    <h3><?= htmlspecialchars($vs['Naam']) ?></h3>
                    <?php if (!empty($vs['Beschrijving'])): ?>
                        <p class="vs-beschrijving"><?= htmlspecialchars($vs['Beschrijving']) ?></p>
                    <?php endif; ?>
                    <div class="vs-info-rij">
                        <div class="vs-info-item">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span><?= htmlspecialchars($datum) ?></span>
                        </div>
                        <div class="vs-info-item">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span><?= htmlspecialchars($tijd) ?></span>
                        </div>
                        <div class="vs-info-item">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                            <span><?= (int)$vs['MaxAantalTickets'] ?> plaatsen</span>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>
