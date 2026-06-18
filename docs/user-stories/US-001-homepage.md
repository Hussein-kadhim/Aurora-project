# User Story US-001 — Homepagina met systeem informatie

## Beschrijving
Als gebruiker van de website wil ik op de homepagina informatie over het systeem kunnen bekijken, inclusief een afbeelding, zodat ik direct een duidelijk overzicht krijg van het doel en de belangrijkste informatie van de website.

---

## Acceptatiecriteria

### ✅ Happy Scenario
- **Gegeven** dat ik ben ingelogd
- **Wanneer** ik de website open
- **Dan** wordt de homepagina correct weergegeven met:
  - Informatieve tekst over het Aurora systeem
  - Een relevante theaterafbeelding
  - Snelkoppeling-kaarten naar de belangrijkste secties
  - Welkomsttekst met de naam van de ingelogde gebruiker

### ❌ Unhappy Scenario
- **Gegeven** dat de server niet bereikbaar is
- **Wanneer** ik de homepagina probeer te openen
- **Dan** verschijnt een gestileerde foutmelding die aangeeft dat de pagina niet geladen kan worden (zonder ruwe PHP-foutmeldingen)

---

## Technische implementatie

| Onderdeel | Bestand | Beschrijving |
|-----------|---------|--------------|
| Homepagina | `informatie/home.php` | Hoofd PHP-pagina met alle UI-componenten |
| Styling | `login.css` | Globale CSS met dark red/warm theme |
| Theaterafbeelding | `assets/images/theater-stage.png` | Hero afbeelding voor de systeem info sectie |
| Voorstelling afbeeldingen | `assets/images/voorstelling-*.png` | Afbeeldingen voor de voorstellingen-sectie |
| Font Awesome 6 | CDN in `home.php` | Iconen voor de snelkoppeling-kaarten |

---

## Status
- [x] Homepagina aangemaakt en gestyled
- [x] Systeem info sectie toegevoegd
- [x] Graceful database error handling geïmplementeerd
- [x] Font Awesome iconen geïntegreerd
- [x] Uitgebreide PHP-comments toegevoegd
- [x] Afbeeldingen toegevoegd aan `assets/images/`

---

## Gerelateerde bestanden
- `informatie/home.php`
- `login.css`
- `assets/images/theater-stage.png`
- `config.php` (database configuratie)
