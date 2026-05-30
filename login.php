<?php
session_start();
require_once __DIR__ . '/config.php';

// Al ingelogd → doorsturen naar home
if (!empty($_SESSION['ingelogd']) && !empty($_SESSION['gebruiker_id'])) {
    header('Location: informatie/home.php');
    exit();
}

$foutmelding = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gebruikersnaam = trim($_POST['gebruikersnaam'] ?? '');
    $wachtwoord     = $_POST['wachtwoord'] ?? '';

    if ($gebruikersnaam === '' || $wachtwoord === '') {
        $foutmelding = 'Vul gebruikersnaam en wachtwoord in.';
    } else {
        try {
            $stmt = $pdo->prepare("
                SELECT Id, Gebruikersnaam, Wachtwoord, Voornaam, Tussenvoegsel, Achternaam
                FROM gebruiker
                WHERE Gebruikersnaam = ? AND IsActief = 1
                LIMIT 1
            ");
            $stmt->execute([$gebruikersnaam]);
            $gebruiker = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$gebruiker) {
                $foutmelding = 'Onjuiste gebruikersnaam of wachtwoord.';
            } else {
                $wachtwoordOk = false;
                $hash         = $gebruiker['Wachtwoord'];

                // Bcrypt-hash (nieuwe wachtwoorden) of plain (testdata)
                if (strlen($hash) >= 60 && strpos($hash, '$2y$') === 0) {
                    $wachtwoordOk = password_verify($wachtwoord, $hash);
                } else {
                    $wachtwoordOk = ($wachtwoord === $hash);
                }

                if (!$wachtwoordOk) {
                    $foutmelding = 'Onjuiste gebruikersnaam of wachtwoord.';
                } else {
                    $_SESSION['ingelogd']      = true;
                    $_SESSION['gebruiker_id']  = (int) $gebruiker['Id'];
                    $_SESSION['gebruikersnaam'] = $gebruiker['Gebruikersnaam'];
                    $_SESSION['naam']          = trim(
                        $gebruiker['Voornaam'] . ' ' .
                        ($gebruiker['Tussenvoegsel'] ?? '') . ' ' .
                        $gebruiker['Achternaam']
                    );

                    $rolStmt = $pdo->prepare("SELECT Naam FROM rol WHERE GebruikerId = ? AND IsActief = 1 LIMIT 1");
                    $rolStmt->execute([$gebruiker['Id']]);
                    $_SESSION['rol'] = $rolStmt->fetchColumn() ?: 'Bezoeker';

                    $update = $pdo->prepare("UPDATE gebruiker SET IsIngelogd = 1, Ingelogd = CURRENT_TIMESTAMP WHERE Id = ?");
                    $update->execute([$gebruiker['Id']]);

                    header('Location: informatie/home.php');
                    exit();
                }
            }
        } catch (PDOException $e) {
            $foutmelding = 'Er ging iets mis. Probeer het later opnieuw.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#D31027">
    <title>Inloggen — Aurora</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
</head>
<body>

<div class="login-kaart">
    <h1>Aurora</h1>
    <p class="subtitel">Log in op je account</p>

    <?php if ($foutmelding !== ''): ?>
        <div class="foutmelding"><?= htmlspecialchars($foutmelding) ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <label for="gebruikersnaam">Gebruikersnaam</label>
        <input
            type="text"
            id="gebruikersnaam"
            name="gebruikersnaam"
            value="<?= htmlspecialchars($_POST['gebruikersnaam'] ?? '') ?>"
            placeholder="jouw@email.nl"
            required
            autofocus
            autocomplete="username"
        >

        <label for="wachtwoord">Wachtwoord</label>
        <input
            type="password"
            id="wachtwoord"
            name="wachtwoord"
            placeholder="••••••••"
            required
            autocomplete="current-password"
        >

        <button type="submit">Inloggen</button>
    </form>

    <a href="informatie/home.php" class="login-terug">Verder zonder inloggen</a>
</div>

</body>
</html>
