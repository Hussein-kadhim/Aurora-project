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
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
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
            <?php elseif (!empty($_GET['success_delete']) && $_GET['success_delete'] == 1): ?>
                <div id="success-alert" class="alert alert-success" style="background-color: #ECFDF5; color: #065F46; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; border-left: 4px solid #10B981; transition: opacity 0.5s ease;">
                    Account succesvol verwijderd
                </div>
                <script>
                    setTimeout(function() {
                        const alertBox = document.getElementById('success-alert');
                        if (alertBox) {
                            alertBox.style.opacity = '0';
                            setTimeout(() => alertBox.style.display = 'none', 500);
                        }
                    }, 3000);
                </script>
            <?php elseif (!empty($_GET['error_delete']) && $_GET['error_delete'] == 1): ?>
                <div id="error-alert" class="alert alert-error" style="background-color: #FEE2E2; color: #B91C1C; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; border-left: 4px solid #EF4444; transition: opacity 0.5s ease;">
                    Account kan niet worden verwijderd vanwege gekoppelde tickets
                </div>
                <script>
                    setTimeout(function() {
                        const alertBox = document.getElementById('error-alert');
                        if (alertBox) {
                            alertBox.style.opacity = '0';
                            setTimeout(() => alertBox.style.display = 'none', 500);
                        }
                    }, 4000);
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
                                             <a href="#" class="action-btn btn-delete" title="Verwijderen" onclick="openDeleteModal(<?= $row['Id'] ?>, '<?= htmlspecialchars($row['Achternaam'], ENT_QUOTES, 'UTF-8') ?>'); return false;">
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
    <!-- Custom Delete Confirmation Modal -->
    <div id="deleteModal" class="custom-modal-overlay" style="display: none;">
        <div class="custom-modal-card">
            <div class="custom-modal-header" style="border-bottom: 1px solid #E5D3B3; padding-bottom: 16px; margin-bottom: 20px;">
                <h3 style="margin: 0; font-size: 1.25rem; font-weight: 700; color: #131313;">Account verwijderen</h3>
            </div>
            <div class="custom-modal-body">
                <p style="font-size: 0.95rem; color: #131313; line-height: 1.5; margin-bottom: 16px;">Weet je dit zeker? Voer de achternaam <strong id="modalTargetName"></strong> in om het verwijderen te bevestigen.</p>
                <div class="form-group" style="gap: 8px; margin-top: 16px;">
                    <label for="confirmAchternaam" style="font-weight: 600; font-size: 0.9rem; color: #131313; display: block; margin-bottom: 8px;">Achternaam</label>
                    <input type="text" id="confirmAchternaam" autocomplete="off" placeholder="Type achternaam..." style="width: 100%; padding: 12px 16px; border: 1px solid #E5D3B3; border-radius: 8px; background-color: #FFFFFF; color: #131313; font-size: 0.95rem; outline: none; transition: all 0.2s;">
                </div>
            </div>
            <div class="custom-modal-footer" style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <button type="button" class="btn-secondary" onclick="closeDeleteModal()" style="padding: 10px 20px; font-size: 0.9rem; border: 1px solid #E5D3B3; border-radius: 8px; cursor: pointer;">Annuleren</button>
                <button type="button" id="modalConfirmBtn" class="btn-primary" disabled style="padding: 10px 20px; font-size: 0.9rem; border-radius: 8px; cursor: pointer;">Verwijderen</button>
            </div>
        </div>
    </div>

    <script>
    let deleteTargetId = null;
    let deleteTargetLastName = "";

    function openDeleteModal(id, achternaam) {
        deleteTargetId = id;
        deleteTargetLastName = achternaam;
        
        document.getElementById('modalTargetName').textContent = achternaam;
        const confirmInput = document.getElementById('confirmAchternaam');
        confirmInput.value = "";
        
        const confirmBtn = document.getElementById('modalConfirmBtn');
        confirmBtn.disabled = true;
        
        document.getElementById('deleteModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
        confirmInput.focus();
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
        document.body.style.overflow = '';
        deleteTargetId = null;
        deleteTargetLastName = "";
    }

    document.getElementById('confirmAchternaam').addEventListener('input', function(e) {
        const typedValue = e.target.value.trim();
        const confirmBtn = document.getElementById('modalConfirmBtn');
        
        if (typedValue === deleteTargetLastName) {
            confirmBtn.disabled = false;
        } else {
            confirmBtn.disabled = true;
        }
    });

    document.getElementById('modalConfirmBtn').addEventListener('click', function() {
        if (deleteTargetId && document.getElementById('confirmAchternaam').value.trim() === deleteTargetLastName) {
            window.location.href = 'verwijderen.php?id=' + deleteTargetId;
        }
    });

    // Close modal on click outside card
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
    </script>

    <?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
</body>
</html>
