<?php
// views/edit.php
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#D31027">
    <meta name="description" content="Wijzig een bestaande voorstelling in het Aurora-systeem.">
    <title>Voorstelling wijzigen — Aurora</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../nieuwe-voorstelling-toevoegen/style.css">
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
                    <div class="breadcrumb">
                        <a href="../voorstelling-beheren/index.php">Voorstelling beheren</a>
                        <span class="breadcrumb-sep">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </span>
                        <span>Voorstelling wijzigen</span>
                    </div>
                    <h2>Voorstelling wijzigen</h2>
                    <p class="subtitle">Pas de gegevens aan van de geselecteerde theaterproductie (<?= htmlspecialchars($voorstelling['Naam'] ?? '') ?>).</p>
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
                    <h3>Er ging iets mis</h3>
                    <p><?= htmlspecialchars($foutmelding) ?></p>
                </div>

            <?php elseif ($succes): ?>
                <!-- Succes Scenario -->
                <div class="empty-state-card succes-card" role="status">
                    <div class="empty-icon-wrapper succes-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3>Voorstelling gewijzigd!</h3>
                    <p>De gegevens van "<strong><?= htmlspecialchars($voorstelling['Naam'] ?? '') ?></strong>" zijn succesvol bijgewerkt in het systeem.</p>
                    <div class="succes-actions">
                        <a href="../voorstelling-beheren/index.php" class="btn-primary">Terug naar overzicht</a>
                    </div>
                </div>

            <?php else: ?>
                <!-- Formulier -->
                <div class="form-card">
                    <form method="post" action="" id="voorstellingForm" novalidate>
                        <!-- ID doorgeven -->
                        <input type="hidden" name="id" value="<?= (int)$voorstellingId ?>">

                        <!-- Sectie 1: Basisgegevens -->
                        <div class="form-section">
                            <h3 class="form-section-title">Basisgegevens</h3>

                            <!-- Naam -->
                            <div class="form-group <?= isset($errors['naam']) ? 'has-error' : '' ?>">
                                <label for="naam">Naam van de voorstelling <span class="required">*</span></label>
                                <input
                                    type="text"
                                    id="naam"
                                    name="naam"
                                    placeholder="Bijv. Romeo en Julia"
                                    value="<?= htmlspecialchars($_POST['naam'] ?? $voorstelling['Naam'] ?? '') ?>"
                                    autocomplete="off"
                                    maxlength="255"
                                >
                                <?php if (isset($errors['naam'])): ?>
                                    <span class="field-error"><?= htmlspecialchars($errors['naam']) ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- Beschrijving -->
                            <div class="form-group">
                                <label for="beschrijving">Beschrijving <span class="optional">(optioneel)</span></label>
                                <textarea
                                    id="beschrijving"
                                    name="beschrijving"
                                    placeholder="Korte omschrijving van de voorstelling..."
                                    rows="4"
                                    maxlength="1000"
                                ><?= htmlspecialchars($_POST['beschrijving'] ?? $voorstelling['Beschrijving'] ?? '') ?></textarea>
                                <span class="char-counter" id="charCounter">0 / 1000</span>
                            </div>
                        </div>

                        <div class="form-divider"></div>

                        <!-- Sectie 2: Datum & Tijd -->
                        <div class="form-section">
                            <h3 class="form-section-title">Datum &amp; Tijd</h3>

                            <div class="form-row">
                                <!-- Datum -->
                                <div class="form-group <?= isset($errors['datum']) ? 'has-error' : '' ?>">
                                    <label for="datum">Datum <span class="required">*</span></label>
                                    <input
                                        type="date"
                                        id="datum"
                                        name="datum"
                                        value="<?= htmlspecialchars($_POST['datum'] ?? $voorstelling['Datum'] ?? '') ?>"
                                    >
                                    <?php if (isset($errors['datum'])): ?>
                                        <span class="field-error"><?= htmlspecialchars($errors['datum']) ?></span>
                                    <?php endif; ?>
                                </div>

                                <!-- Tijd -->
                                <?php 
                                    $tijdVal = $_POST['tijd'] ?? $voorstelling['Tijd'] ?? '';
                                    if(strlen($tijdVal) > 5) {
                                        $tijdVal = substr($tijdVal, 0, 5); // 14:30:00 -> 14:30
                                    }
                                ?>
                                <div class="form-group <?= isset($errors['tijd']) ? 'has-error' : '' ?>">
                                    <label for="tijd">Aanvangstijd <span class="required">*</span></label>
                                    <input
                                        type="time"
                                        id="tijd"
                                        name="tijd"
                                        value="<?= htmlspecialchars($tijdVal) ?>"
                                    >
                                    <?php if (isset($errors['tijd'])): ?>
                                        <span class="field-error"><?= htmlspecialchars($errors['tijd']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-divider"></div>

                        <!-- Sectie 3: Capaciteit & Status -->
                        <div class="form-section">
                            <h3 class="form-section-title">Capaciteit &amp; Status</h3>

                            <div class="form-row">
                                <!-- Max tickets -->
                                <div class="form-group <?= isset($errors['max_tickets']) ? 'has-error' : '' ?>">
                                    <label for="max_tickets">Maximaal aantal tickets <span class="required">*</span></label>
                                    <div class="input-with-icon">
                                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                        </svg>
                                        <input
                                            type="number"
                                            id="max_tickets"
                                            name="max_tickets"
                                            placeholder="Bijv. 250"
                                            min="1"
                                            max="10000"
                                            value="<?= htmlspecialchars($_POST['max_tickets'] ?? $voorstelling['MaxAantalTickets'] ?? '') ?>"
                                        >
                                    </div>
                                    <?php if (isset($errors['max_tickets'])): ?>
                                        <span class="field-error"><?= htmlspecialchars($errors['max_tickets']) ?></span>
                                    <?php endif; ?>
                                </div>

                                <!-- Beschikbaarheid -->
                                <?php $huidigeBeschikbaarheid = $_POST['beschikbaarheid'] ?? $voorstelling['Beschikbaarheid'] ?? ''; ?>
                                <div class="form-group <?= isset($errors['beschikbaarheid']) ? 'has-error' : '' ?>">
                                    <label for="beschikbaarheid">Beschikbaarheid <span class="required">*</span></label>
                                    <div class="select-wrapper">
                                        <select id="beschikbaarheid" name="beschikbaarheid">
                                            <option value="" disabled <?= empty($huidigeBeschikbaarheid) ? 'selected' : '' ?>>Selecteer status...</option>
                                            <option value="Ingepland"  <?= $huidigeBeschikbaarheid === 'Ingepland'  ? 'selected' : '' ?>>Ingepland</option>
                                            <option value="Uitverkocht" <?= $huidigeBeschikbaarheid === 'Uitverkocht' ? 'selected' : '' ?>>Uitverkocht</option>
                                            <option value="Geannuleerd" <?= $huidigeBeschikbaarheid === 'Geannuleerd' ? 'selected' : '' ?>>Geannuleerd</option>
                                        </select>
                                        <svg class="select-arrow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                    <?php if (isset($errors['beschikbaarheid'])): ?>
                                        <span class="field-error"><?= htmlspecialchars($errors['beschikbaarheid']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Formulier Actieknoppen -->
                        <div class="form-actions">
                            <a href="../voorstelling-beheren/index.php" class="btn-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width:18px;height:18px;margin-right:6px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Annuleren
                            </a>
                            <button type="submit" class="btn-primary" id="submitBtn">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width:18px;height:18px;margin-right:6px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                </svg>
                                Wijzigingen opslaan
                            </button>
                        </div>

                    </form>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <script>
    document.addEventListener("DOMContentLoaded", function () {

        // Teken-teller voor beschrijving
        const textarea    = document.getElementById("beschrijving");
        const charCounter = document.getElementById("charCounter");
        if (textarea && charCounter) {
            function updateCounter() {
                charCounter.textContent = textarea.value.length + " / 1000";
            }
            textarea.addEventListener("input", updateCounter);
            updateCounter();
        }

        // Prevent double-submit
        const form      = document.getElementById("voorstellingForm");
        const submitBtn = document.getElementById("submitBtn");
        if (form && submitBtn) {
            form.addEventListener("submit", function () {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width:18px;height:18px;margin-right:6px;animation:spin 1s linear infinite"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>Opslaan...';
            });
        }
    });
    </script>

    <?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
</body>
</html>
