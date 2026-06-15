<?php
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#D31027">
    <meta name="description" content="Overzicht van alle geregistreerde medewerkers — Aurora beheerpaneel.">
    <title>Medewerker beheren — Aurora</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Premium Navigatiebalk -->
    <?php require_once __DIR__ . '/../../../includes/navbar.php'; ?>

    <!-- Hoofdinhoud -->
    <main class="dashboard-content">
        <div class="container">

            <!-- Dashboard Kop -->
            <div class="dashboard-header-row">
                <div class="title-section">
                    <h2>Medewerker beheren</h2>
                    <p class="subtitle">Overzicht van alle geregistreerde medewerkers binnen het systeem.</p>
                </div>
            </div>

            <?php if (!empty($_SESSION['success'])): ?>
                <div class="alert alert-success" role="alert">
                    <svg class="alert-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 20px; height: 20px; margin-right: 8px; flex-shrink: 0;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span><?= htmlspecialchars($_SESSION['success']) ?></span>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if ($dbFout): ?>
                <!-- Unhappy Scenario: Server niet bereikbaar -->
                <div class="empty-state-card" role="alert">
                    <div class="empty-icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3>Server niet bereikbaar</h3>
                    <p><?= htmlspecialchars($foutmelding) ?></p>
                </div>

            <?php elseif (empty($medewerkers)): ?>
                <!-- Zoekbalk & Actie Knoppen -->
                <div class="controls-row">
                    <form method="get" action="" class="search-form" id="searchForm">
                        <div class="search-input-wrapper">
                            <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input
                                type="text"
                                id="searchInput"
                                name="search"
                                placeholder="Zoek op naam, nummer of soort..."
                                value="<?= htmlspecialchars($search) ?>"
                                autocomplete="off"
                            >
                            <?php if ($search !== ''): ?>
                                <a href="index.php" class="clear-search" title="Zoekopdracht wissen">&times;</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Unhappy Scenario: Geen medewerkers -->
                <div class="empty-state-card">
                    <div class="empty-icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <?php if ($totalCount === 0): ?>
                        <h3>Geen medewerkers gevonden</h3>
                        <p>Er staan momenteel helemaal geen medewerkers geregistreerd in het systeem.</p>
                        <a href="index.php?action=create" class="btn-primary" style="margin-top: 16px;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 18px; height: 18px; margin-right: 6px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Nieuwe Medewerker Toevoegen
                        </a>
                    <?php else: ?>
                        <h3>Geen zoekresultaten</h3>
                        <p>Er zijn geen medewerkers gevonden die overeenkomen met de zoekterm "<strong><?= htmlspecialchars($search) ?></strong>".</p>
                        <a href="index.php" class="btn-secondary" style="margin-top: 16px;">Wissen en terug naar overzicht</a>
                    <?php endif; ?>
                </div>

            <?php else: ?>

                <!-- Zoekbalk & Actie Knoppen -->
                <div class="controls-row">
                    <form method="get" action="" class="search-form" id="searchForm">
                        <div class="search-input-wrapper">
                            <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input
                                type="text"
                                id="searchInput"
                                name="search"
                                placeholder="Zoek op naam, nummer of soort..."
                                value="<?= htmlspecialchars($search) ?>"
                                autocomplete="off"
                            >
                            <?php if ($search !== ''): ?>
                                <a href="index.php" class="clear-search" title="Zoekopdracht wissen">&times;</a>
                            <?php endif; ?>
                        </div>
                    </form>
                    <a href="index.php?action=create" class="btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 18px; height: 18px; margin-right: 6px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nieuwe Medewerker Toevoegen
                    </a>
                </div>

                <!-- Happy Scenario: Lijst van medewerkers in tabel -->
                <div class="table-card" id="tableCard">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nr.</th>
                                    <th>Naam</th>
                                    <th>E-mailadres</th>
                                    <th>Mobiel</th>
                                    <th>Soort</th>
                                    <th>Rol</th>
                                    <th>Status</th>
                                    <th>Aangemaakt</th>
                                    <th style="text-align: right;">Acties</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <?php foreach ($medewerkers as $m):
                                    $naam       = volledigeNaam($m['Voornaam'], $m['Tussenvoegsel'], $m['Achternaam']);
                                    $soort      = $m['Medewerkersoort'] ?? '—';
                                    $soortClass = 'soort-' . strtolower(preg_replace('/\s+/', '', $soort));
                                    $isOnline   = ($m['IsIngelogd'] === "\x01" || $m['IsIngelogd'] === 1 || $m['IsIngelogd'] === "1" || $m['IsIngelogd'] === true);
                                    $datum      = $m['DatumAangemaakt']
                                                    ? date('d-m-Y', strtotime($m['DatumAangemaakt']))
                                                    : '—';
                                ?>
                                <tr class="medewerker-rij">
                                    <td class="username-cell">
                                        #<?= (int)$m['MedewerkerNummer'] ?>
                                        <span class="full-name"><?= htmlspecialchars($m['Gebruikersnaam'] ?? '') ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($naam) ?></td>
                                    <td>
                                        <?php if (!empty($m['Email'])): ?>
                                            <a href="mailto:<?= htmlspecialchars($m['Email']) ?>" style="color:#131313;text-decoration:none;opacity:0.7;">
                                                <?= htmlspecialchars($m['Email']) ?>
                                            </a>
                                        <?php else: ?>
                                            <span style="opacity:.4">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($m['Mobiel'] ?? '—') ?></td>
                                    <td class="soort-cell">
                                        <span class="soort-badge <?= htmlspecialchars($soortClass) ?>">
                                            <?= htmlspecialchars($soort) ?>
                                        </span>
                                    </td>
                                    <td class="role-cell">
                                        <span class="role-badge role-<?= strtolower(htmlspecialchars($m['Rol'] ?? 'medewerker')) ?>">
                                            <?= htmlspecialchars($m['Rol'] ?? '—') ?>
                                        </span>
                                    </td>
                                    <td class="status-cell">
                                        <?php if ($isOnline): ?>
                                            <span class="status-indicator status-active">
                                                <span class="status-dot"></span>
                                                Online
                                            </span>
                                        <?php else: ?>
                                            <span class="status-indicator status-archived">
                                                <span class="status-dot"></span>
                                                Offline
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($datum) ?></td>
                                    <td class="actions-cell">
                                        <a href="#" class="action-btn btn-edit" title="Bewerken" onclick="alert('Bewerken is nog in ontwikkeling.'); return false;">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            <span class="btn-text">Wijzigen</span>
                                        </a>
                                        <a href="#" class="action-btn btn-delete" title="Verwijderen" onclick="alert('Verwijderen is nog in ontwikkeling.'); return false;">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            <span class="btn-text">Verwijderen</span>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Tabel Footer / Pagination -->
                    <div class="table-footer">
                        <div class="footer-info" id="footerInfo">
                            1-<?= count($medewerkers) ?> of <?= count($medewerkers) ?> medewerkers
                        </div>
                        <div class="footer-pagination">
                            <span class="rows-per-page">Rijen per pagina: 10</span>
                            <div class="pagination-arrows">
                                <span class="arrow disabled">&lt;</span>
                                <span class="arrow disabled">&gt;</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Geen zoekresultaten (dynamisch) -->
                <div id="instantEmptyState" class="empty-state-card" style="display:none;">
                    <div class="empty-icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3>Geen zoekresultaten</h3>
                    <p>Er zijn geen medewerkers gevonden die overeenkomen met je zoekopdracht.</p>
                    <a href="#" id="clearInstantSearch" class="btn-secondary" style="margin-top: 16px;">Wissen en terug naar overzicht</a>
                </div>

            <?php endif; ?>

        </div>
    </main>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("searchInput");
        const searchForm  = document.getElementById("searchForm");
        const tableRows   = document.querySelectorAll(".medewerker-rij");
        const tableCard   = document.getElementById("tableCard");
        const emptyState  = document.getElementById("instantEmptyState");
        const footerInfo  = document.getElementById("footerInfo");
        const totalCount  = tableRows.length;

        if (searchInput) {
            searchInput.addEventListener("input", function() {
                const query = searchInput.value.toLowerCase().trim();
                let visible = 0;

                tableRows.forEach(function(row) {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(query)) {
                        row.style.display = "";
                        visible++;
                    } else {
                        row.style.display = "none";
                    }
                });

                if (footerInfo) {
                    footerInfo.textContent = "1-" + visible + " of " + visible + " medewerkers";
                }

                if (visible === 0 && query !== "") {
                    if (tableCard)  tableCard.style.display  = "none";
                    if (emptyState) emptyState.style.display = "block";
                } else {
                    if (tableCard)  tableCard.style.display  = "block";
                    if (emptyState) emptyState.style.display = "none";
                }
            });

            if (searchInput.value !== "") {
                searchInput.dispatchEvent(new Event("input"));
            }
        }

        if (searchForm) {
            searchForm.addEventListener("submit", function(e) {
                e.preventDefault();
            });
        }

        const clearBtn = document.getElementById("clearInstantSearch");
        if (clearBtn && searchInput) {
            clearBtn.addEventListener("click", function(e) {
                e.preventDefault();
                searchInput.value = "";
                searchInput.dispatchEvent(new Event("input"));
                searchInput.focus();
            });
        }
    });
    </script>

    <?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
</body>
</html>
