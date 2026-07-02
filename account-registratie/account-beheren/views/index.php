<?php
$search = $search ?? '';
$accounts = $accounts ?? [];
$totalCount = $totalCount ?? 0;
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#D31027">
    <title>Accountoverzicht — Aurora</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
                    <h2>Account beheren</h2>
                    <p class="subtitle">Overzicht van alle geregistreerde accounts binnen het systeem.</p>
                </div>
            </div>

            <!-- Zoekbalk & Actie Knoppen -->
            <div class="controls-row">
                <form method="get" action="" class="search-form" id="searchForm">
                    <div class="search-input-wrapper">
                        <!-- Magnifying glass FontAwesome icon -->
                        <i class="fa-solid fa-magnifying-glass search-icon" style="line-height: 20px; font-size: 16px;"></i>
                        <input 
                            type="text" 
                            id="searchInput"
                            name="search" 
                            placeholder="Zoek op gebruikersnaam..." 
                            value="<?= htmlspecialchars($search) ?>"
                            autocomplete="off"
                        >
                        <?php if ($search !== ''): ?>
                            <a href="index.php" class="clear-search" title="Zoekopdracht wissen">&times;</a>
                        <?php endif; ?>
                    </div>
                </form>
                
                <a href="toevoegen.php" class="btn-primary">
                    <i class="fa-solid fa-plus" style="margin-right: 6px;"></i>
                    Nieuw Account Toevoegen
                </a>
            </div>

            <?php if (!empty($_GET['success']) && $_GET['success'] == 1): ?>
                <div id="success-alert" class="alert alert-success" style="background-color: #ECFDF5; color: #065F46; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; border-left: 4px solid #10B981; transition: opacity 0.5s ease;">
                    <strong>Gelukt!</strong> Account succesvol aangemaakt.
                </div>
                <script>
                    setTimeout(function() {
                        const alertBox = document.getElementById('success-alert');
                        if (alertBox) {
                            alertBox.style.opacity = '0';
                            setTimeout(() => alertBox.style.display = 'none', 500); // Wacht tot fade-out animatie klaar is
                        }
                    }, 3000); // 3 seconden zichtbaar
                </script>
            <?php elseif (!empty($_GET['success_edit']) && $_GET['success_edit'] == 1): ?>
                <div id="success-alert" class="alert alert-success" style="background-color: #ECFDF5; color: #065F46; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; border-left: 4px solid #10B981; transition: opacity 0.5s ease;">
                    <strong>Gelukt!</strong> Account succesvol gewijzigd.
                </div>
                <script>
                    setTimeout(function() {
                        const alertBox = document.getElementById('success-alert');
                        if (alertBox) {
                            alertBox.style.opacity = '0';
                            setTimeout(() => alertBox.style.display = 'none', 500); // Wacht tot fade-out animatie klaar is
                        }
                    }, 3000); // 3 seconden zichtbaar
                </script>
            <?php endif; ?>

            <!-- Unhappy Scenario: Geen accounts in database of geen resultaten -->
            <?php if (!empty($foutmelding)): ?>
                <div class="empty-state-card">
                    <div class="empty-icon-wrapper" style="background-color: #FEE2E2; color: #B91C1C;">
                        <i class="fa-solid fa-triangle-exclamation" style="font-size: 28px;"></i>
                    </div>
                    <h3 style="color: #B91C1C;">Systeemfout</h3>
                    <p><?= htmlspecialchars($foutmelding) ?></p>
                </div>
            <?php elseif (empty($accounts)): ?>
                <div class="empty-state-card">
                    <div class="empty-icon-wrapper">
                        <i class="fa-solid fa-triangle-exclamation" style="font-size: 28px;"></i>
                    </div>
                    <?php if ($totalCount === 0): ?>
                        <h3>Geen accounts gevonden</h3>
                        <p>Er staan momenteel helemaal geen accounts geregistreerd in het systeem.</p>
                        <a href="toevoegen.php" class="btn-primary" style="margin-top: 16px;">
                            Voeg je eerste account toe
                        </a>
                    <?php else: ?>
                        <h3>Geen zoekresultaten</h3>
                        <p>Er zijn geen gebruikers gevonden die overeenkomen met de zoekterm "<strong><?= htmlspecialchars($search) ?></strong>".</p>
                        <a href="index.php" class="btn-secondary" style="margin-top: 16px;">
                            Wissen en terug naar overzicht
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>

                <!-- Happy Scenario: Lijst van accounts in tabel -->
                <div class="table-card">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Gebruikersnaam</th>
                                    <th>Rol</th>
                                    <th>Status</th>
                                    <th style="text-align: right;">Acties</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($accounts as $row): ?>
                                    <?php 
                                        // IsIngelogd bitwaarde verwerken: wie is ingelogd moet je actief tonen
                                        $isIngelogdVal = $row['IsIngelogd'];
                                        $isIngelogd = ($isIngelogdVal === "\x01" || $isIngelogdVal === 1 || $isIngelogdVal === "1" || $isIngelogdVal === true);
                                    ?>
                                    <tr>
                                        <td class="username-cell">
                                            <?= htmlspecialchars($row['Gebruikersnaam']) ?>
                                            <span class="full-name"><?= htmlspecialchars(trim($row['Voornaam'] . ' ' . ($row['Tussenvoegsel'] ?? '') . ' ' . $row['Achternaam'])) ?></span>
                                        </td>
                                        <td class="role-cell">
                                            <span class="role-badge role-<?= strtolower(htmlspecialchars($row['RolNaam'] ?? 'Lid')) ?>">
                                                <?= htmlspecialchars($row['RolNaam'] ?? 'Bezoeker') ?>
                                            </span>
                                        </td>
                                        <td class="status-cell">
                                            <?php if ($isIngelogd): ?>
                                                <span class="status-indicator status-active">
                                                    <span class="status-dot"></span>
                                                    Actief
                                                </span>
                                            <?php else: ?>
                                                <span class="status-indicator status-archived">
                                                    <span class="status-dot"></span>
                                                    Niet actief
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="text-align: right;" class="actions-cell">
                                            <a href="wijzigen.php?id=<?= $row['Id'] ?>" class="action-btn btn-edit" title="Bewerken">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                                <span class="btn-text">Wijzigen</span>
                                            </a>
                                            <a href="#" class="action-btn btn-delete" title="Archiveren/Verwijderen" onclick="alert('Archiveren is nog in ontwikkeling.'); return false;">
                                                <i class="fa-solid fa-trash-can"></i>
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
                        <div class="footer-info">
                            1-<?= count($accounts) ?> of <?= count($accounts) ?> accounts
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

            <?php endif; ?>

        </div>
    </main>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Direct filteren zodra je typt (zonder pagina-reload!)
        const searchInput = document.getElementById("searchInput");
        const searchForm = document.getElementById("searchForm");
        const tableRows = document.querySelectorAll("tbody tr");
        const tableCard = document.querySelector(".table-card");
        
        // Dynamisch lege status element maken voor het instant filteren
        let instantEmptyState = document.getElementById("instant-empty-state");
        if (!instantEmptyState && tableCard) {
            instantEmptyState = document.createElement("div");
            instantEmptyState.id = "instant-empty-state";
            instantEmptyState.className = "empty-state-card";
            instantEmptyState.style.display = "none";
            instantEmptyState.innerHTML = `
                <div class="empty-icon-wrapper">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size: 28px;"></i>
                </div>
                <h3>Geen zoekresultaten</h3>
                <p>Er zijn geen gebruikers gevonden die overeenkomen met je zoekopdracht.</p>
                <a href="#" id="clearInstantSearch" class="btn-secondary" style="margin-top: 16px;">Wissen en terug naar overzicht</a>
            `;
            tableCard.parentNode.insertBefore(instantEmptyState, tableCard.nextSibling);
            
            document.getElementById("clearInstantSearch").addEventListener("click", function(e) {
                e.preventDefault();
                searchInput.value = "";
                searchInput.dispatchEvent(new Event('input'));
            });
        }

        if (searchInput) {
            searchInput.addEventListener("input", function() {
                const query = searchInput.value.toLowerCase().trim();
                let visibleCount = 0;

                tableRows.forEach(row => {
                    const usernameCell = row.querySelector(".username-cell");
                    if (usernameCell) {
                        const usernameText = usernameCell.textContent.toLowerCase();
                        if (usernameText.includes(query)) {
                            row.style.display = "";
                            visibleCount++;
                        } else {
                            row.style.display = "none";
                        }
                    }
                });

                // Update de footer informatie
                const footerInfo = document.querySelector(".footer-info");
                if (footerInfo) {
                    footerInfo.textContent = `1-${visibleCount} of ${visibleCount} accounts`;
                }

                // Toon of verberg de lege-tabel kaart
                if (visibleCount === 0) {
                    if (tableCard) tableCard.style.display = "none";
                    if (instantEmptyState) instantEmptyState.style.display = "block";
                } else {
                    if (tableCard) tableCard.style.display = "block";
                    if (instantEmptyState) instantEmptyState.style.display = "none";
                }
            });
            
            // Als er al een startwaarde in PHP is ingevuld, direct filteren
            if (searchInput.value !== "") {
                searchInput.dispatchEvent(new Event('input'));
            }
        }

        if (searchForm) {
            searchForm.addEventListener("submit", function(e) {
                e.preventDefault(); // Voorkom herladen, we filteren al direct!
            });
        }
    });
    </script>
    <?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
</body>
</html>
