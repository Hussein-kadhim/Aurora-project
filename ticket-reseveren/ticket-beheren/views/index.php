<?php
$search = isset($search) ? $search : '';
$totalCount = isset($totalCount) ? $totalCount : 0;
$tickets = isset($tickets) ? $tickets : [];
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#D31027">
    <title>Ticketoverzicht — Aurora</title>
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
                    <h2>Ticketoverzicht</h2>
                    <p class="subtitle">Beheer en bekijk alle actieve reserveringen voor de komende evenementen.</p>
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
                            placeholder="Zoek tickets..." 
                            value="<?= htmlspecialchars($search) ?>"
                            autocomplete="off"
                        >
                        <?php if ($search !== ''): ?>
                            <a href="index.php" class="clear-search" title="Zoekopdracht wissen">&times;</a>
                        <?php endif; ?>
                    </div>
                </form>
                
                <div class="right-actions">
                    <button class="btn-filter" onclick="alert('Filteren is nog in ontwikkeling.'); return false;">
                        <i class="fa-solid fa-filter"></i>
                        <span>Filters</span>
                    </button>
                    
                    <a href="scan.php" class="btn-primary">
                        Scan Ticket
                    </a>
                    
                    <a href="reserveren.php" class="btn-primary">
                        + Nieuw Ticket
                    </a>
                </div>
            </div>

            <!-- Meldingen -->
            <?php if (!empty($_GET['success_delete']) && $_GET['success_delete'] == 1): ?>
                <div id="success-alert" class="alert alert-success" style="background-color: #ECFDF5; color: #065F46; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; border-left: 4px solid #10B981; transition: opacity 0.5s ease;">
                    <strong>Gelukt!</strong> Ticket succesvol geannuleerd.
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
                    <strong>Fout!</strong> Gebruikt ticket kan niet worden geannuleerd.
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


            <!-- Unhappy Scenario: Geen tickets in database of geen resultaten -->
            <?php if (empty($tickets)): ?>
                <div class="empty-state-card">
                    <div class="empty-icon-wrapper">
                        <!-- Ticket FontAwesome icon -->
                        <i class="fa-solid fa-ticket" style="font-size: 28px;"></i>
                    </div>
                    <?php if ($totalCount === 0): ?>
                        <h3>Geen tickets gevonden</h3>
                        <p>Er staan momenteel helemaal geen tickets geregistreerd in het systeem.</p>
                        <a href="../../informatie/home.php" class="btn-empty-home">
                            Terug naar homepage
                        </a>
                    <?php else: ?>
                        <h3>Geen zoekresultaten</h3>
                        <p>Er zijn geen tickets gevonden die overeenkomen met de zoekterm "<strong><?= htmlspecialchars($search) ?></strong>".</p>
                        <a href="index.php" class="btn-secondary" style="margin-top: 16px;">
                            Wissen en terug naar overzicht
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>

                <!-- Happy Scenario: Desktop Tabel Weergave -->
                <div class="table-card desktop-only">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Ticketnummer</th>
                                    <th>Voorstelling</th>
                                    <th>Bezoeker</th>
                                    <th>Status</th>
                                    <th style="text-align: right;">Acties</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tickets as $row): ?>
                                    <?php 
                                        $statusText = htmlspecialchars($row['TicketStatus']);
                                        $statusClass = '';
                                        if (strtolower($statusText) === 'gebruikt' || strtolower($statusText) === 'bezet') {
                                            $statusClass = 'status-used';
                                            $statusText = 'GEBRUIKT';
                                        } else if (strtolower($statusText) === 'gereserveerd') {
                                            $statusClass = 'status-reserved';
                                            $statusText = 'GERESERVEERD';
                                        } else {
                                            $statusClass = 'status-cancelled';
                                            $statusText = 'GEANNULEERD';
                                        }
                                    ?>
                                    <tr class="ticket-row">
                                        <td class="ticket-num-cell">#T-<?= htmlspecialchars($row['TicketNummer']) ?></td>
                                        <td class="show-cell"><?= htmlspecialchars($row['VoorstellingNaam']) ?></td>
                                        <td class="visitor-cell">
                                            <?= htmlspecialchars(trim($row['BezoekerVoornaam'] . ' ' . ($row['BezoekerTussenvoegsel'] ?? '') . ' ' . $row['BezoekerAchternaam'])) ?>
                                        </td>
                                        <td class="status-cell">
                                            <span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span>
                                        </td>
                                         <td style="text-align: right;" class="actions-cell">
                                             <?php if (strtolower($row['TicketStatus']) === 'gereserveerd'): ?>
                                                 <a href="wijzigen.php?id=<?= $row['Id'] ?>" class="action-btn btn-edit" title="Bewerken">
                                                      <i class="fa-solid fa-pen-to-square"></i>
                                                 </a>
                                                 <a href="#" class="action-btn btn-delete" title="Annuleren" onclick="openDeleteModal(<?= $row['Id'] ?>, '<?= htmlspecialchars($row['BezoekerAchternaam'], ENT_QUOTES, 'UTF-8') ?>', '<?= htmlspecialchars($row['TicketNummer'], ENT_QUOTES, 'UTF-8') ?>'); return false;">
                                                      <i class="fa-solid fa-trash-can"></i>
                                                 </a>
                                             <?php else: ?>
                                                 <span style="font-size: 0.85rem; color: #999; font-style: italic;">Geen acties</span>
                                             <?php endif; ?>
                                         </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Tabel Footer / Pagination -->
                    <div class="table-footer">
                        <div class="footer-info">
                            Resultaten 1-<?= count($tickets) ?> van <?= count($tickets) ?>
                        </div>
                        <div class="footer-pagination">
                            <span class="pagination-link" onclick="alert('Vorige pagina is nog in ontwikkeling.');">Vorige</span>
                            <span class="pagination-link" onclick="alert('Volgende pagina is nog in ontwikkeling.');">Volgende</span>
                        </div>
                    </div>
                </div>

                <!-- Happy Scenario: Mobiele Kaarten Weergave -->
                <div class="ticket-cards-list mobile-only">
                    <?php foreach ($tickets as $row): ?>
                        <?php 
                            $statusText = htmlspecialchars($row['TicketStatus']);
                            $statusClass = '';
                            if (strtolower($statusText) === 'gebruikt' || strtolower($statusText) === 'bezet') {
                                $statusClass = 'status-used';
                                $statusText = 'GEBRUIKT';
                            } else if (strtolower($statusText) === 'gereserveerd') {
                                $statusClass = 'status-reserved';
                                $statusText = 'GERESERVEERD';
                            } else {
                                $statusClass = 'status-cancelled';
                                $statusText = 'GEANNULEERD';
                            }
                        ?>
                        <div class="ticket-card-item">
                            <div class="ticket-card-header">
                                <span class="ticket-id">#T-<?= htmlspecialchars($row['TicketNummer']) ?></span>
                                <span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span>
                            </div>
                            <div class="ticket-card-body">
                                <h3 class="show-title"><?= htmlspecialchars($row['VoorstellingNaam']) ?></h3>
                            </div>
                            <div class="ticket-card-footer">
                                <div class="visitor-info">
                                    <span class="visitor-label">VISITOR</span>
                                    <span class="visitor-name">
                                        <?= htmlspecialchars(trim($row['BezoekerVoornaam'] . ' ' . ($row['BezoekerTussenvoegsel'] ?? '') . ' ' . $row['BezoekerAchternaam'])) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="ticket-card-actions">
                                <?php if (strtolower($row['TicketStatus']) === 'gereserveerd'): ?>
                                    <a href="wijzigen.php?id=<?= $row['Id'] ?>" class="mobile-action-btn btn-edit">Wijzigen</a>
                                    <a href="#" class="mobile-action-btn btn-delete" onclick="openDeleteModal(<?= $row['Id'] ?>, '<?= htmlspecialchars($row['BezoekerAchternaam'], ENT_QUOTES, 'UTF-8') ?>', '<?= htmlspecialchars($row['TicketNummer'], ENT_QUOTES, 'UTF-8') ?>'); return false;">Annuleren</a>
                                <?php else: ?>
                                    <span style="font-size: 0.85rem; color: #999; font-style: italic; width: 100%; text-align: center; padding: 10px 0;">Geen acties mogelijk</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <button class="btn-load-more" onclick="alert('Meer tickets laden is nog in ontwikkeling.');">LOAD MORE</button>
                </div>

            <?php endif; ?>

        </div>
    </main>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("searchInput");
        const searchForm = document.getElementById("searchForm");
        const tableRows = document.querySelectorAll(".ticket-row");
        const cardItems = document.querySelectorAll(".ticket-card-item");
        const tableCard = document.querySelector(".table-card");
        const cardsList = document.querySelector(".ticket-cards-list");
        
        // Dynamisch lege status element maken voor het instant filteren
        let instantEmptyState = document.getElementById("instant-empty-state");
        const contentContainer = document.querySelector(".container");
        
        if (!instantEmptyState && contentContainer && (tableCard || cardsList)) {
            instantEmptyState = document.createElement("div");
            instantEmptyState.id = "instant-empty-state";
            instantEmptyState.className = "empty-state-card";
            instantEmptyState.style.display = "none";
            instantEmptyState.style.marginTop = "40px";
            instantEmptyState.innerHTML = `
                <div class="empty-icon-wrapper">
                    <i class="fa-solid fa-ticket" style="font-size: 28px;"></i>
                </div>
                <h3>Geen tickets gevonden</h3>
                <p>Er zijn geen tickets gevonden die overeenkomen met je zoekopdracht.</p>
                <a href="#" id="clearInstantSearch" class="btn-secondary" style="margin-top: 16px;">Wissen en terug naar overzicht</a>
            `;
            contentContainer.appendChild(instantEmptyState);
            
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

                // Filter tabel rijen (desktop)
                tableRows.forEach(row => {
                    const rowText = row.textContent.toLowerCase();
                    if (rowText.includes(query)) {
                        row.style.display = "";
                        visibleCount++;
                    } else {
                        row.style.display = "none";
                    }
                });

                // Filter kaarten (mobiel)
                let mobileVisibleCount = 0;
                cardItems.forEach(card => {
                    const cardText = card.textContent.toLowerCase();
                    if (cardText.includes(query)) {
                        card.style.display = "";
                        mobileVisibleCount++;
                    } else {
                        card.style.display = "none";
                    }
                });

                // Update de footer informatie
                const footerInfo = document.querySelector(".table-footer .footer-info");
                if (footerInfo) {
                    footerInfo.textContent = `Resultaten 1-${visibleCount} van ${visibleCount}`;
                }

                // Bepaal de actieve view-telling op basis van schermgrootte
                const isMobile = window.innerWidth <= 768;
                const activeCount = isMobile ? mobileVisibleCount : visibleCount;

                // Toon of verberg de lege status
                if (activeCount === 0) {
                    if (tableCard) tableCard.style.display = "none";
                    if (cardsList) cardsList.style.display = "none";
                    if (instantEmptyState) instantEmptyState.style.display = "block";
                } else {
                    if (tableCard) tableCard.style.display = isMobile ? "none" : "block";
                    if (cardsList) cardsList.style.display = isMobile ? "flex" : "none";
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
                e.preventDefault(); // Voorkom herladen, we filteren direct!
            });
        }
    });
    </script>
    <!-- Custom Delete Confirmation Modal -->
    <div id="deleteModal" class="custom-modal-overlay" style="display: none;">
        <div class="custom-modal-card">
            <div class="custom-modal-header" style="border-bottom: 1px solid #E5D3B3; padding-bottom: 16px; margin-bottom: 20px;">
                <h3 style="margin: 0; font-size: 1.25rem; font-weight: 700; color: #131313;">Ticket annuleren</h3>
            </div>
            <div class="custom-modal-body">
                <p style="font-size: 0.95rem; color: #131313; line-height: 1.5; margin-bottom: 16px;">Weet je dit zeker? Voer de achternaam van de bezoeker (<strong id="modalTargetName"></strong>) in om de annulering van ticket <strong id="modalTargetTicket"></strong> te bevestigen.</p>
                <div class="form-group" style="gap: 8px; margin-top: 16px;">
                    <label for="confirmAchternaam" style="font-weight: 600; font-size: 0.9rem; color: #131313; display: block; margin-bottom: 8px;">Achternaam bezoeker</label>
                    <input type="text" id="confirmAchternaam" autocomplete="off" placeholder="Type achternaam..." style="width: 100%; padding: 12px 16px; border: 1px solid #E5D3B3; border-radius: 8px; background-color: #FFFFFF; color: #131313; font-size: 0.95rem; outline: none; transition: all 0.2s;">
                </div>
            </div>
            <div class="custom-modal-footer" style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <button type="button" class="btn-secondary" onclick="closeDeleteModal()" style="padding: 10px 20px; font-size: 0.9rem; border: 1px solid #E5D3B3; border-radius: 8px; cursor: pointer;">Annuleren</button>
                <button type="button" id="modalConfirmBtn" class="btn-primary" disabled style="padding: 10px 20px; font-size: 0.9rem; border-radius: 8px; cursor: pointer;">Bevestigen</button>
            </div>
        </div>
    </div>

    <script>
    let deleteTargetId = null;
    let deleteTargetLastName = "";

    function openDeleteModal(id, achternaam, ticketNummer) {
        deleteTargetId = id;
        deleteTargetLastName = achternaam;
        
        document.getElementById('modalTargetName').textContent = achternaam;
        document.getElementById('modalTargetTicket').textContent = '#T-' + ticketNummer;
        
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
        const typedValue = e.target.value.trim().toLowerCase();
        const confirmBtn = document.getElementById('modalConfirmBtn');
        
        if (typedValue === deleteTargetLastName.toLowerCase()) {
            confirmBtn.disabled = false;
        } else {
            confirmBtn.disabled = true;
        }
    });

    document.getElementById('modalConfirmBtn').addEventListener('click', function() {
        if (deleteTargetId && document.getElementById('confirmAchternaam').value.trim().toLowerCase() === deleteTargetLastName.toLowerCase()) {
            window.location.href = 'annuleren.php?id=' + deleteTargetId;
        }
    });

    // Close modal on click outside card
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
    </script>
    
    <!-- Footer include -->
    <?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
</body>
</html>
