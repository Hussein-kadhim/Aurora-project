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
            background: linear-gradient(135deg, #D31027 0%, #7a0817 100%);
            color: #FFFFFF;
            padding: 72px 24px 64px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 70% 50%, rgba(229,211,179,0.15) 0%, transparent 65%);
            pointer-events: none;
        }

        .hero-badge {
            display: inline-block;
            background: rgba(255,255,255,0.18);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 20px;
            padding: 4px 16px;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 20px;
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
            font-size: 1.5rem;
            line-height: 1;
            flex-shrink: 0;
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
            font-size: 1.3rem;
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
            gap: 5px;
            background: rgba(211,16,39,0.09);
            color: #D31027;
            border-radius: 6px;
            padding: 3px 10px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            width: fit-content;
        }

        .vs-badge.uitverkocht {
            background: rgba(19,19,19,0.08);
            color: #555;
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

        .vs-info-item span.icoon {
            font-size: 0.9rem;
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
    <div class="hero-badge">🎭 Welkom bij Aurora</div>
    <h1>Jouw podium voor<br>onvergetelijke ervaringen</h1>
    <p>Reserveer tickets, bekijk aankomende voorstellingen en beheer uw account — alles op één plek.</p>
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
        <div class="fout-icoon">⚠️</div>
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
            <h2>Welkom terug, <?= htmlspecialchars($naam) ?>!</h2>
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
                <div class="kaart-icoon">🎭</div>
                <h3>Voorstellingen</h3>
                <p>Bekijk aankomende shows, ballets en theaterproducties.</p>
                <span class="pijl">Bekijken →</span>
            </a>

            <!-- Bezoeker / Ingelogd -->
            <?php if ($isIngelogd): ?>
            <a href="../ticket-reseveren/ticket-beheren/index.php" class="actie-kaart" id="link-tickets" aria-label="Ga naar ticket reserveren">
                <div class="kaart-icoon">🎟️</div>
                <h3>Ticket reserveren</h3>
                <p>Reserveer uw tickets voor aankomende voorstellingen.</p>
                <span class="pijl">Reserveren →</span>
            </a>
            <?php else: ?>
            <a href="../login.php" class="actie-kaart" id="link-inloggen" aria-label="Ga naar inlogpagina">
                <div class="kaart-icoon">🔐</div>
                <h3>Inloggen</h3>
                <p>Log in om tickets te reserveren en uw account te beheren.</p>
                <span class="pijl">Inloggen →</span>
            </a>
            <?php endif; ?>

            <!-- Medewerker & Beheerder -->
            <?php if ($isStaff): ?>
            <a href="../ticket-reseveren/ticket-beheren/index.php" class="actie-kaart" id="link-ticket-beheren" aria-label="Ga naar ticket beheren">
                <div class="kaart-icoon">🗂️</div>
                <h3>Ticket beheren</h3>
                <p>Controleer en beheer gereserveerde tickets van bezoekers.</p>
                <span class="pijl">Beheren →</span>
            </a>
            <?php endif; ?>

            <!-- Alleen Beheerder -->
            <?php if ($isAdmin): ?>
            <a href="../account-registratie/account-beheren/index.php" class="actie-kaart" id="link-accounts" aria-label="Ga naar account beheren">
                <div class="kaart-icoon">👤</div>
                <h3>Account beheren</h3>
                <p>Voeg gebruikers toe, wijzig rollen en beheer toegangsrechten.</p>
                <span class="pijl">Beheren →</span>
            </a>

            <a href="../voorstelling-opstellen/voorstelling-beheren/index.php" class="actie-kaart" id="link-voorstelling-beheren"
               onclick="alert('Voorstelling beheren is nog in ontwikkeling.'); return false;"
               aria-label="Ga naar voorstelling beheren (in ontwikkeling)">
                <div class="kaart-icoon">📋</div>
                <h3>Voorstelling beheren</h3>
                <p>Plan en bewerk theatervoorstellingen en programmaonderdelen.</p>
                <span class="pijl">Beheren →</span>
            </a>

            <a href="../melding/melding-beheren/index.php" class="actie-kaart" id="link-meldingen"
               onclick="alert('Melding beheren is nog in ontwikkeling.'); return false;"
               aria-label="Ga naar melding beheren (in ontwikkeling)">
                <div class="kaart-icoon">📣</div>
                <h3>Melding beheren</h3>
                <p>Bekijk reviews, klachten en systeemnotificaties van bezoekers.</p>
                <span class="pijl">Beheren →</span>
            </a>

            <a href="../medewerker-registratie/medewerker-beheren/index.php" class="actie-kaart" id="link-medewerkers"
               onclick="alert('Medewerker beheren is nog in ontwikkeling.'); return false;"
               aria-label="Ga naar medewerker beheren (in ontwikkeling)">
                <div class="kaart-icoon">🧑‍💼</div>
                <h3>Medewerker beheren</h3>
                <p>Registreer en beheer medewerkers en hun functies.</p>
                <span class="pijl">Beheren →</span>
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
                    ⚠️ Voorstellingen kunnen momenteel niet worden geladen. Probeer het later opnieuw.
                </div>
            </div>

        <?php elseif (empty($voorstellingen)): ?>
            <div class="voorstelling-raster">
                <div class="geen-voorstellingen" role="status">
                    🎭 Er zijn momenteel geen aankomende voorstellingen gepland.
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
                    <div class="vs-badge <?= $isUitverkocht ? 'uitverkocht' : '' ?>">
                        <?= $isUitverkocht ? '🔴 Uitverkocht' : '🟢 Beschikbaar' ?>
                    </div>
                    <h3><?= htmlspecialchars($vs['Naam']) ?></h3>
                    <?php if (!empty($vs['Beschrijving'])): ?>
                        <p class="vs-beschrijving"><?= htmlspecialchars($vs['Beschrijving']) ?></p>
                    <?php endif; ?>
                    <div class="vs-info-rij">
                        <div class="vs-info-item">
                            <span class="icoon">📅</span>
                            <span><?= htmlspecialchars($datum) ?></span>
                        </div>
                        <div class="vs-info-item">
                            <span class="icoon">🕐</span>
                            <span><?= htmlspecialchars($tijd) ?></span>
                        </div>
                        <div class="vs-info-item">
                            <span class="icoon">🎫</span>
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
