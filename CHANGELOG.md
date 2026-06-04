# Changelog — feature-home-pagina-maken

Alle wijzigingen voor de `feature-home-pagina-maken` branch worden hier bijgehouden.

---

## [Unreleased] — 2026-06-04

### Toegevoegd
- **Homepagina (`informatie/home.php`)**
  - Nieuwe "Over het Aurora Systeem" sectie met beschrijving en theaterafbeelding
  - Snelkoppeling-kaarten met Font Awesome 6 iconen (Voorstellingen, Medewerkers, Kaartverkoop, Rapporten)
  - Welkomsttekst met naam van de ingelogde gebruiker
  - Gestileerde database-foutpagina (unhappy scenario) zonder ruwe PHP-foutmeldingen
  - Uitgebreide inline PHP-comments voor onderhoudbaarheid

- **Afbeeldingen (`assets/images/`)**
  - `theater-stage.png` — hero afbeelding voor de systeem info sectie
  - `voorstelling-cinderella-ballet.png`
  - `voorstelling-romeo-julia.png`
  - `voorstelling-sound-of-music.png`

- **Documentatie (`docs/user-stories/`)**
  - `US-001-homepage.md` — user story documentatie met acceptatiecriteria

### Gewijzigd
- **Styling (`login.css`)**
  - Dark red / warm tone kleurenpalet verfijnd
  - Login card border-radius en schaduw verbeterd

### Technische details
- Font Awesome 6 CDN geïntegreerd (vervangt inline SVG iconen)
- `ALLOW_DB_FAILURE` constante gebruikt voor graceful database error handling
- Responsive grid layout voor systeem info sectie (CSS Grid)

---

## Gerelateerde User Stories
- [US-001](docs/user-stories/US-001-homepage.md) — Homepagina met systeem informatie
