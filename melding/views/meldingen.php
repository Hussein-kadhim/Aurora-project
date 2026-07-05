<?php ?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#D31027">
    <meta name="description" content="Overzicht van alle meldingen — Aurora beheerpaneel.">
    <title>Meldingen beheren — Aurora</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Meldingen.css">
</head>
<body>

    <!-- Navigatiebalk -->
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>

    <!-- Hoofdinhoud -->
    <main class="dashboard-content">
        <div class="container">

            <!-- Dashboard Kop -->
            <div class="dashboard-header-row">
                <div class="title-section">
                    <h2>Meldingen beheren</h2>
                    <p class="subtitle">Overzicht van alle meldingen en systeemberichten binnen Aurora.</p>
                </div>
            </div>

            <?php if (!empty($_SESSION['success_message'])): ?>
                <div class="alert-success-banner">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; flex-shrink: 0; margin-right: 8px;">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span><?= htmlspecialchars($_SESSION['success_message']) ?></span>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if ($techError): ?>
                <!-- Unhappy Scenario: Technische fout -->
                <div class="empty-state-card" role="alert">
                    <div class="empty-icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3>Server niet bereikbaar</h3>
                    <p>De meldingen kunnen momenteel niet worden geladen. Probeer het later opnieuw.</p>
                    <a href="meldingen.php" class="btn-retry">Opnieuw proberen</a>
                </div>

            <?php else: ?>

                <!-- Rij 1: Zoekbalk + Nieuwe Melding knop -->
                <div class="controls-row">
                    <div class="search-form">
                        <div class="search-input-wrapper">
                            <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input
                                type="text"
                                id="searchInput"
                                placeholder="Zoek op omschrijving, type of datum..."
                                value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                                autocomplete="off"
                            >
                            <?php if (!empty($_GET['search'])): ?>
                                <a href="meldingen.php?type=<?= urlencode($filterType) ?>&status=<?= urlencode($filterStatus) ?>&date=<?= urlencode($filterDate) ?>"
                                    class="clear-search" title="Zoekopdracht wissen">&times;</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <a href="meldingen.php?action=nieuw" class="btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 18px; height: 18px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nieuwe Melding Toevoegen
                    </a>
                </div>

                <!-- Rij 2: Dropdown filters -->
                <div class="filter-row">
                    <form method="GET" class="filter-form" id="filterForm">
                        <?php if (!empty($_GET['search'])): ?>
                            <input type="hidden" name="search" value="<?= htmlspecialchars($_GET['search']) ?>">
                        <?php endif; ?>
                        <div class="filter-group">
                            <label for="filter-date">DATUM</label>
                            <input type="date" id="filter-date" name="date"
                                value="<?= htmlspecialchars($filterDate) ?>">
                        </div>
                        <div class="filter-group">
                            <label for="filter-type">TYPE</label>
                            <select id="filter-type" name="type">
                                <option value="">Alle types</option>
                                <option value="Bericht"      <?= $filterType === 'Bericht'      ? 'selected' : '' ?>>Bericht</option>
                                <option value="Klacht"       <?= $filterType === 'Klacht'       ? 'selected' : '' ?>>Klacht</option>
                                <option value="Notificatie"  <?= $filterType === 'Notificatie'  ? 'selected' : '' ?>>Notificatie</option>
                                <option value="Review"       <?= $filterType === 'Review'       ? 'selected' : '' ?>>Review</option>
                                <option value="Update"       <?= $filterType === 'Update'       ? 'selected' : '' ?>>Update</option>
                                <option value="Waarschuwing" <?= $filterType === 'Waarschuwing' ? 'selected' : '' ?>>Waarschuwing</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="filter-status">STATUS</label>
                            <select id="filter-status" name="status">
                                <option value="">Alle statussen</option>
                                <option value="unread" <?= $filterStatus === 'unread' ? 'selected' : '' ?>>Ongelezen</option>
                                <option value="read"   <?= $filterStatus === 'read'   ? 'selected' : '' ?>>Gelezen</option>
                            </select>
                        </div>
                        <div class="filter-actions">
                            <button type="submit" class="btn-apply">Filters toepassen</button>
                            <a href="meldingen.php" class="btn-clear">Wissen</a>
                        </div>
                    </form>
                </div>

                <?php if (empty($meldingen)): ?>
                    <!-- Unhappy Scenario: Geen meldingen -->
                    <div class="empty-state-card">
                        <div class="empty-icon-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <h3>Geen meldingen gevonden</h3>
                        <?php if ($filterType !== '' || $filterStatus !== '' || $filterDate !== ''): ?>
                            <p>Er zijn geen meldingen gevonden die overeenkomen met de geselecteerde filters.</p>
                            <a href="meldingen.php" class="btn-secondary">Wissen en terug naar overzicht</a>
                        <?php else: ?>
                            <p>Er staan momenteel helemaal geen meldingen geregistreerd in het systeem.</p>
                        <?php endif; ?>
                    </div>

                <?php else: ?>

                    <!-- Happy Scenario: Tabel -->
                    <div class="table-card" id="tableCard">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Datum</th>
                                        <th>Omschrijving</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th style="text-align: right;">Actie</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <?php foreach ($meldingen as $row):
                                        $isActiefVal = $row['IsActief'];
                                        $isActief = ($isActiefVal === "\x01" || $isActiefVal === 1 || $isActiefVal === "1" || $isActiefVal === true);

                                        $senderSub = 'Systeemmelding';
                                        if (!empty($row['BezoekerVoornaam'])) {
                                            $senderSub = 'Geplaatst door bezoeker: ' . trim(
                                                $row['BezoekerVoornaam'] . ' ' .
                                                ($row['BezoekerTussenvoegsel'] ?? '') . ' ' .
                                                $row['BezoekerAchternaam']
                                            );
                                        } elseif (!empty($row['MedewerkerVoornaam'])) {
                                            $senderSub = 'Geplaatst door medewerker: ' . trim(
                                                $row['MedewerkerVoornaam'] . ' ' .
                                                ($row['MedewerkerTussenvoegsel'] ?? '') . ' ' .
                                                $row['MedewerkerAchternaam']
                                            );
                                        }

                                        $descriptionSubtext = !empty($row['Opmerking'])
                                            ? htmlspecialchars($row['Opmerking'])
                                            : htmlspecialchars($senderSub);

                                        $displayDate = MeldingController::formatDutchDate($row['DatumAangemaakt']);
                                        $typeLower   = strtolower($row['Type']);
                                    ?>
                                    <tr class="melding-row">
                                        <td class="date-cell"><?= htmlspecialchars($displayDate) ?></td>
                                        <td class="omschrijving-cell">
                                            <div class="msg-title"><?= htmlspecialchars($row['Bericht']) ?></div>
                                            <div class="msg-subtext"><?= $descriptionSubtext ?></div>
                                        </td>
                                        <td class="type-cell">
                                            <span class="type-badge type-<?= htmlspecialchars($typeLower) ?>">
                                                <?= htmlspecialchars(ucfirst($row['Type'])) ?>
                                            </span>
                                        </td>
                                        <td class="status-cell">
                                            <?php if ($isActief): ?>
                                                <span class="status-indicator status-unread">
                                                    <span class="status-dot"></span>
                                                    Ongelezen
                                                </span>
                                            <?php else: ?>
                                                <span class="status-indicator status-verzonden">
                                                    <span class="status-dot"></span>
                                                    Verzonden
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="actions-cell">
                                            <a href="meldingen.php?action=verstuur&id=<?= (int) $row['Id'] ?>"
                                               class="action-btn action-btn-verstuur"
                                               title="Melding versturen"
                                               id="verstuur-btn-<?= (int) $row['Id'] ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <line x1="22" y1="2" x2="11" y2="13"></line>
                                                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Tabel Footer / Paginering -->
                        <div class="table-footer">
                            <div class="footer-info" id="footerInfo">
                                <?= $startRange ?>–<?= $endRange ?> van <?= $totalFiltered ?> meldingen
                            </div>
                            <?php if ($totalPages > 1): ?>
                                <div class="footer-pagination">
                                    <?php if ($page > 1): ?>
                                        <a href="?type=<?= urlencode($filterType) ?>&status=<?= urlencode($filterStatus) ?>&date=<?= urlencode($filterDate) ?>&page=<?= $page - 1 ?>"
                                            class="pagination-link">&lt;</a>
                                    <?php else: ?>
                                        <span class="pagination-link disabled">&lt;</span>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <?php if ($i === $page): ?>
                                            <span class="pagination-link active"><?= $i ?></span>
                                        <?php else: ?>
                                            <a href="?type=<?= urlencode($filterType) ?>&status=<?= urlencode($filterStatus) ?>&date=<?= urlencode($filterDate) ?>&page=<?= $i ?>"
                                                class="pagination-link"><?= $i ?></a>
                                        <?php endif; ?>
                                    <?php endfor; ?>

                                    <?php if ($page < $totalPages): ?>
                                        <a href="?type=<?= urlencode($filterType) ?>&status=<?= urlencode($filterStatus) ?>&date=<?= urlencode($filterDate) ?>&page=<?= $page + 1 ?>"
                                            class="pagination-link">&gt;</a>
                                    <?php else: ?>
                                        <span class="pagination-link disabled">&gt;</span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Geen zoekresultaten (dynamisch, client-side) -->
                    <div id="instantEmptyState" class="empty-state-card" style="display:none;">
                        <div class="empty-icon-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <h3>Geen zoekresultaten</h3>
                        <p>Er zijn geen meldingen gevonden die overeenkomen met je zoekopdracht.</p>
                        <a href="#" id="clearInstantSearch" class="btn-secondary" style="margin-top: 16px;">Wissen en terug</a>
                    </div>

                <?php endif; ?>

            <?php endif; ?>

        </div>
    </main>

    <?php require_once __DIR__ . '/../../includes/footer.php'; ?>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const searchInput   = document.getElementById("searchInput");
        const tableRows     = document.querySelectorAll(".melding-row");
        const tableCard     = document.getElementById("tableCard");
        const emptyState    = document.getElementById("instantEmptyState");
        const footerInfo    = document.getElementById("footerInfo");
        const totalCount    = tableRows.length;
        const clearBtn      = document.getElementById("clearInstantSearch");

        if (searchInput) {
            searchInput.addEventListener("input", function () {
                const query = searchInput.value.toLowerCase().trim();
                let visible = 0;

                tableRows.forEach(function (row) {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(query)) {
                        row.style.display = "";
                        visible++;
                    } else {
                        row.style.display = "none";
                    }
                });

                if (footerInfo) {
                    footerInfo.textContent = visible + " van " + totalCount + " meldingen";
                }

                if (visible === 0 && query !== "") {
                    if (tableCard)  tableCard.style.display  = "none";
                    if (emptyState) emptyState.style.display = "block";
                } else {
                    if (tableCard)  tableCard.style.display  = "";
                    if (emptyState) emptyState.style.display = "none";
                }
            });

            // Trigger on load if value pre-filled
            if (searchInput.value !== "") {
                searchInput.dispatchEvent(new Event("input"));
            }
        }

        if (clearBtn && searchInput) {
            clearBtn.addEventListener("click", function (e) {
                e.preventDefault();
                searchInput.value = "";
                searchInput.dispatchEvent(new Event("input"));
                searchInput.focus();
            });
        }
    });
    </script>

</body>
</html>
