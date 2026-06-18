<?php
// views/index.php
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#D31027">
    <meta name="description" content="Overzicht van alle voorstellingen — Aurora beheerpaneel.">
    <title>Voorstelling beheren — Aurora</title>
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
                    <h2>Voorstelling beheren</h2>
                    <p class="subtitle">Overzicht van alle theaterproducties en voorstellingen binnen de Aurora-faciliteit.</p>
                </div>
            </div>

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

            <?php elseif (empty($voorstellingen)): ?>
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
                                placeholder="Zoek op naam, status of datum..."
                                value="<?= htmlspecialchars($search) ?>"
                                autocomplete="off"
                            >
                            <?php if ($search !== ''): ?>
                                <a href="index.php" class="clear-search" title="Zoekopdracht wissen">&times;</a>
                            <?php endif; ?>
                        </div>
                    </form>
                    <a href="../nieuwe-voorstelling-toevoegen/index.php" class="btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 20px; height: 20px; margin-right: 8px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nieuwe voorstelling
                    </a>
                </div>

                <!-- Unhappy Scenario: Geen voorstellingen -->
                <div class="empty-state-card">
                    <div class="empty-icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <?php if ($totalCount === 0): ?>
                        <h3>Geen voorstellingen gevonden</h3>
                        <p>Er zijn momenteel geen actieve voorstellingen geregistreerd in het systeem.</p>
                    <?php else: ?>
                        <h3>Geen zoekresultaten</h3>
                        <p>Er zijn geen voorstellingen gevonden die overeenkomen met de zoekterm "<strong><?= htmlspecialchars($search) ?></strong>".</p>
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
                                placeholder="Zoek op naam, status of datum..."
                                value="<?= htmlspecialchars($search) ?>"
                                autocomplete="off"
                            >
                            <?php if ($search !== ''): ?>
                                <a href="index.php" class="clear-search" title="Zoekopdracht wissen">&times;</a>
                            <?php endif; ?>
                        </div>
                    </form>
                    <a href="../nieuwe-voorstelling-toevoegen/index.php" class="btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 20px; height: 20px; margin-right: 8px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nieuwe voorstelling
                    </a>
                </div>

                <!-- Happy Scenario: Lijst van voorstellingen in tabel -->
                <div class="table-card" id="tableCard">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nr.</th>
                                    <th>Naam</th>
                                    <th>Datum</th>
                                    <th>Tijd</th>
                                    <th>Capaciteit</th>
                                    <th>Beschikbaarheid</th>
                                    <th>Aangemaakt</th>
                                    <th style="text-align: right;">Acties</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <?php foreach ($voorstellingen as $v):
                                    $datum       = date('d-m-Y', strtotime($v['Datum']));
                                    $tijd        = date('H:i', strtotime($v['Tijd']));
                                    $aangemaakt  = $v['DatumAangemaakt'] ? date('d-m-Y', strtotime($v['DatumAangemaakt'])) : '—';
                                    
                                    $beschikbaarheid = $v['Beschikbaarheid'];
                                    
                                    // Bepaal de status badge obv de styling uit medewerker-beheren
                                    $statusClass = 'status-archived';
                                    if (strtolower($beschikbaarheid) === 'ingepland') {
                                        $statusClass = 'status-active';
                                    } elseif (strtolower($beschikbaarheid) === 'uitverkocht') {
                                        $statusClass = 'status-active'; // Or another visual style
                                    }
                                    
                                    // Use role-badge styling as a neat pill shape for availability
                                    $roleClass = 'role-medewerker'; 
                                    if (strtolower($beschikbaarheid) === 'uitverkocht') $roleClass = 'role-administrator';
                                    if (strtolower($beschikbaarheid) === 'geannuleerd') $roleClass = 'role-administrator';
                                ?>
                                <tr class="medewerker-rij">
                                    <td class="username-cell">
                                        #<?= (int)$v['Id'] ?>
                                    </td>
                                    <td>
                                        <span class="full-name"><?= htmlspecialchars($v['Naam']) ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($datum) ?></td>
                                    <td><?= htmlspecialchars($tijd) ?></td>
                                    <td><?= (int)$v['MaxAantalTickets'] ?> tickets</td>
                                    <td class="status-cell">
                                        <?php if (strtolower($beschikbaarheid) === 'ingepland'): ?>
                                            <span class="status-indicator status-active">
                                                <span class="status-dot"></span>
                                                Ingepland
                                            </span>
                                        <?php elseif (strtolower($beschikbaarheid) === 'geannuleerd'): ?>
                                            <span class="status-indicator status-archived">
                                                <span class="status-dot"></span>
                                                Geannuleerd
                                            </span>
                                        <?php else: ?>
                                            <span class="status-indicator status-archived" style="color: #d97706; background-color: rgba(245, 158, 11, 0.1);">
                                                <span class="status-dot" style="background-color: #d97706;"></span>
                                                <?= htmlspecialchars($beschikbaarheid) ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($aangemaakt) ?></td>
                                    <td style="text-align: right;" class="actions-cell">
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
                            1-<?= count($voorstellingen) ?> of <?= count($voorstellingen) ?> voorstellingen
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
                    <p>Er zijn geen voorstellingen gevonden die overeenkomen met je zoekopdracht.</p>
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
                    footerInfo.textContent = "1-" + visible + " of " + visible + " voorstellingen";
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
