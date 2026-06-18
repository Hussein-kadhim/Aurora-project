<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Dynamisch root path bepalen
$script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_FILENAME']));
$root_path = '';
$temp_path = $script_dir;
while (!file_exists($temp_path . '/config.php') && $temp_path !== '/' && strlen($temp_path) > 3) {
    $temp_path = dirname($temp_path);
    $root_path .= '../';
}
if ($root_path === '') {
    $root_path = './';
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#D31027">
    <title>Geen toegang Aurora</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #FAFAFA;
            color: #131313;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .error-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .error-card {
            background: #FFFFFF;
            border: 1px solid #E5D3B3;
            border-radius: 16px;
            max-width: 500px;
            width: 100%;
            padding: 40px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(19, 19, 19, 0.04);
        }

        .icon-wrapper {
            width: 80px;
            height: 80px;
            background-color: #FEE2E2;
            color: #D31027;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px auto;
            font-size: 36px;
        }

        h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #131313;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        p {
            font-size: 1rem;
            color: #131313;
            opacity: 0.7;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .btn-home {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #D31027;
            color: #FFFFFF;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.2s, transform 0.1s;
            font-size: 0.95rem;
            border: none;
            cursor: pointer;
        }

        .btn-home:hover {
            background-color: #b50e21;
        }

        .btn-home:active {
            transform: scale(0.98);
        }

        .btn-home i {
            margin-right: 8px;
        }
    </style>
</head>
<body>

    <!-- Eventueel de navbar erboven tonen indien gewenst, maar een schone pagina is vaak duidelijker bij ontbreken van rechten. -->
    <?php require_once $root_path . 'includes/navbar.php'; ?>

    <div class="error-container">
        <div class="error-card">
            <div class="icon-wrapper">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h1>Geen toegang</h1>
            <p>Je hebt geen toegang tot deze pagina.</p>
            <a href="<?= $root_path ?>informatie/home.php" class="btn-home">
                <i class="fa-solid fa-house"></i> Terug naar Home
            </a>
        </div>
    </div>

    <?php require_once $root_path . 'includes/footer.php'; ?>

</body>
</html>
