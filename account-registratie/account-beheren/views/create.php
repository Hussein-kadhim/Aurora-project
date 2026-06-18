<?php
// Voorkom undefined variable errors
$error = $error ?? '';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#D31027">
    <title>Nieuw Account Toevoegen — Aurora</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Premium Navigatiebalk -->
    <?php require_once __DIR__ . '/../../../includes/navbar.php'; ?>

    <main class="dashboard-content">
        <div class="container">
            
            <div class="dashboard-header-row">
                <div class="title-section">
                    <h2>Nieuw account toevoegen</h2>
                    <p class="subtitle">Vul de gegevens in om een nieuw account aan het systeem toe te voegen.</p>
                </div>
                <div class="controls-section" style="margin-top: 16px;">
                    <a href="index.php" class="btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 18px; height: 18px; margin-right: 6px; display: inline; vertical-align: text-bottom;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Terug naar overzicht
                    </a>
                </div>
            </div>

            <?php if (!empty($error)): ?>
                <div id="error-alert" class="alert alert-error" style="background-color: #FEE2E2; color: #B91C1C; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; border-left: 4px solid #EF4444; transition: opacity 0.5s ease;">
                    <strong>Fout!</strong> <?= htmlspecialchars($error) ?>
                </div>
                <script>
                    setTimeout(function() {
                        const alertBox = document.getElementById('error-alert');
                        if (alertBox) {
                            alertBox.style.opacity = '0';
                            setTimeout(() => alertBox.style.display = 'none', 500);
                        }
                    }, 4000); // 4 seconden zichtbaar zodat de gebruiker het kan lezen
                </script>
            <?php endif; ?>

            <div class="form-card">
                <form action="toevoegen.php" method="POST" class="premium-form">
                    <div class="form-grid">
                        
                        <div class="form-group">
                            <label for="Voornaam">Voornaam *</label>
                            <input type="text" id="Voornaam" name="Voornaam" required value="<?= htmlspecialchars($_POST['Voornaam'] ?? '') ?>" autocomplete="given-name">
                        </div>

                        <div class="form-group">
                            <label for="Tussenvoegsel">Tussenvoegsel</label>
                            <input type="text" id="Tussenvoegsel" name="Tussenvoegsel" value="<?= htmlspecialchars($_POST['Tussenvoegsel'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label for="Achternaam">Achternaam *</label>
                            <input type="text" id="Achternaam" name="Achternaam" required value="<?= htmlspecialchars($_POST['Achternaam'] ?? '') ?>" autocomplete="family-name">
                        </div>

                        <div class="form-group">
                            <label for="Gebruikersnaam">Gebruikersnaam *</label>
                            <input type="text" id="Gebruikersnaam" name="Gebruikersnaam" required value="<?= htmlspecialchars($_POST['Gebruikersnaam'] ?? '') ?>" autocomplete="username">
                        </div>

                        <div class="form-group">
                            <label for="Email">E-mailadres *</label>
                            <input type="email" id="Email" name="Email" required value="<?= htmlspecialchars($_POST['Email'] ?? '') ?>" autocomplete="email">
                        </div>

                        <div class="form-group">
                            <label for="Mobiel">Mobiel nummer *</label>
                            <input type="tel" id="Mobiel" name="Mobiel" required value="<?= htmlspecialchars($_POST['Mobiel'] ?? '') ?>" autocomplete="tel">
                        </div>

                        <div class="form-group">
                            <label for="Rol">Rol *</label>
                            <select id="Rol" name="Rol" required>
                                <option value="" disabled <?= empty($_POST['Rol']) ? 'selected' : '' ?>>Selecteer een rol...</option>
                                <option value="Bezoeker" <?= (isset($_POST['Rol']) && $_POST['Rol'] === 'Bezoeker') ? 'selected' : '' ?>>Bezoeker</option>
                                <option value="Medewerker" <?= (isset($_POST['Rol']) && $_POST['Rol'] === 'Medewerker') ? 'selected' : '' ?>>Medewerker</option>
                                <option value="Administrator" <?= (isset($_POST['Rol']) && $_POST['Rol'] === 'Administrator') ? 'selected' : '' ?>>Administrator</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="Wachtwoord">Wachtwoord *</label>
                            <input type="password" id="Wachtwoord" name="Wachtwoord" required autocomplete="new-password">
                        </div>

                    </div>

                    <div class="form-actions" style="margin-top: 48px; display: flex; gap: 16px;">
                        <button type="submit" class="btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 18px; height: 18px; margin-right: 6px; display: inline; vertical-align: text-bottom;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Opslaan
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </main>

    <?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
</body>
</html>
