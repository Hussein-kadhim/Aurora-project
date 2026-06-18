<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#D31027">
    <meta name="description" content="Nieuwe melding opstellen — Aurora beheerpaneel.">
    <title>Nieuwe melding opstellen — Aurora</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Meldingen.css">
</head>
<body>

    <!-- Navigatiebalk -->
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>

    <!-- Hoofdinhoud -->
    <main class="dashboard-content">
        <div class="container">

            <div class="new-melding-container">
                
                <!-- Formulier Header Bar -->
                <div class="form-header-bar">
                    <a href="meldingen.php" class="back-btn" title="Terug naar overzicht">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                    </a>
                    <span class="form-header-title">Nieuwe Melding</span>
                    <button type="button" class="more-btn" title="Opties">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                            <circle cx="12" cy="5" r="1.5"></circle>
                            <circle cx="12" cy="12" r="1.5"></circle>
                            <circle cx="12" cy="19" r="1.5"></circle>
                        </svg>
                    </button>
                </div>

                <!-- Formulier Body -->
                <div class="form-body-wrapper">
                    <div class="form-category-label">Systeem berichten</div>
                    <h2 class="form-main-title">Configureer Alert</h2>

                    <!-- Foutmeldingen tonen -->
                    <?php if (!empty($errors)): ?>
                        <div class="form-errors-container" role="alert">
                            <ul class="form-errors-list">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form id="nieuwMeldingForm" method="POST" action="meldingen.php?action=nieuw">
                        
                        <!-- Titel input -->
                        <div class="form-field-group">
                            <label for="titel">Titel</label>
                            <input 
                                type="text" 
                                id="titel" 
                                name="titel" 
                                class="form-input-text" 
                                placeholder="Voer de titel in..." 
                                value="<?= htmlspecialchars($titel ?? '') ?>" 
                                required
                                maxlength="250"
                            >
                        </div>

                        <!-- Inhoud textarea -->
                        <div class="form-field-group">
                            <label for="inhoud">Inhoud</label>
                            <textarea 
                                id="inhoud" 
                                name="inhoud" 
                                class="form-textarea" 
                                placeholder="Beschrijf de melding in detail..."
                                maxlength="250"
                            ><?= htmlspecialchars($inhoud ?? '') ?></textarea>
                        </div>

                        <!-- Prioriteit select -->
                        <div class="form-field-group">
                            <label for="type">Prioriteit</label>
                            <select id="type" name="type" class="form-select">
                                <option value="" disabled <?= empty($type) ? 'selected' : '' ?>>Kies prioriteit</option>
                                <option value="Bericht"      <?= ($type ?? '') === 'Bericht'      ? 'selected' : '' ?>>Bericht</option>
                                <option value="Klacht"       <?= ($type ?? '') === 'Klacht'       ? 'selected' : '' ?>>Klacht</option>
                                <option value="Notificatie"  <?= ($type ?? '') === 'Notificatie'  ? 'selected' : '' ?>>Notificatie</option>
                                <option value="Review"       <?= ($type ?? '') === 'Review'       ? 'selected' : '' ?>>Review</option>
                                <option value="Update"       <?= ($type ?? '') === 'Update'       ? 'selected' : '' ?>>Update</option>
                                <option value="Waarschuwing" <?= ($type ?? '') === 'Waarschuwing' ? 'selected' : '' ?>>Waarschuwing</option>
                            </select>
                        </div>

                        <!-- Delivery mode (Instant / Logboek) -->
                        <input type="hidden" id="deliveryInput" name="delivery" value="<?= htmlspecialchars($delivery ?? 'INSTANT') ?>">
                        
                        <div class="delivery-options-grid">
                            <!-- Instant Card -->
                            <div 
                                id="cardInstant" 
                                class="delivery-card-option <?= ($delivery ?? 'INSTANT') === 'INSTANT' ? 'active' : '' ?>"
                                onclick="setDelivery('INSTANT')"
                            >
                                <svg class="option-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px;">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <div class="option-title">INSTANT</div>
                                <div class="option-desc">Directe verzending</div>
                            </div>

                            <!-- Logboek Card -->
                            <div 
                                id="cardLogboek" 
                                class="delivery-card-option <?= ($delivery ?? 'INSTANT') === 'LOGBOEK' ? 'active' : '' ?>"
                                onclick="setDelivery('LOGBOEK')"
                            >
                                <svg class="option-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px;">
                                    <path d="M12 8v4l3 3"></path>
                                    <path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5"></path>
                                </svg>
                                <div class="option-title">LOGBOEK</div>
                                <div class="option-desc">Blijvend bewaard</div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn-submit-form">
                            <span>Versturen</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px; margin-left: 8px;">
                                <line x1="22" y1="2" x2="11" y2="13"></line>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                            </svg>
                        </button>
                    </form>

                </div>

            </div>

        </div>
    </main>

    <!-- Footer -->
    <?php require_once __DIR__ . '/../../includes/footer.php'; ?>

    <script>
    function setDelivery(mode) {
        document.getElementById('deliveryInput').value = mode;
        
        const cardInstant = document.getElementById('cardInstant');
        const cardLogboek = document.getElementById('cardLogboek');
        
        if (mode === 'INSTANT') {
            cardInstant.classList.add('active');
            cardLogboek.classList.remove('active');
        } else {
            cardInstant.classList.remove('active');
            cardLogboek.classList.add('active');
        }
    }
    </script>

</body>
</html>
