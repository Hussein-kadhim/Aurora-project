<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

$isAdmin = (!empty($_SESSION['rol']) && $_SESSION['rol'] === 'Administrator');
$isStaff = (!empty($_SESSION['rol']) && ($_SESSION['rol'] === 'Administrator' || $_SESSION['rol'] === 'Medewerker'));
$isIngelogd = !empty($_SESSION['ingelogd']);
?>
<style>
.aurora-nav * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

.aurora-nav {
    background-color: #FFFFFF;
    border-bottom: 1px solid #E5D3B3;
    padding: 0 24px;
    height: 70px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-family: 'Inter', sans-serif;
    position: sticky;
    top: 0;
    z-index: 1000;
}

.aurora-nav .nav-brand a {
    font-size: 1.5rem;
    font-weight: 700;
    color: #D31027;
    text-decoration: none;
    letter-spacing: -0.5px;
    transition: opacity 0.2s;
}

.aurora-nav .nav-brand a:hover {
    opacity: 0.85;
}

.aurora-nav .nav-right {
    display: flex;
    align-items: center;
    gap: 32px;
    height: 100%;
}

.aurora-nav .nav-menu {
    display: flex;
    align-items: center;
    list-style: none;
    gap: 20px;
    height: 100%;
}

.aurora-nav .nav-item {
    display: flex;
    align-items: center;
    height: 100%;
}

.aurora-nav .nav-link {
    color: #131313;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 600;
    transition: color 0.2s;
    white-space: nowrap;
}

.aurora-nav .nav-link:hover {
    color: #D31027;
}

.aurora-nav .nav-user-area {
    display: flex;
    align-items: center;
    gap: 16px;
}

.aurora-nav .nav-user-info {
    font-size: 0.85rem;
    color: #131313;
    opacity: 0.6;
    white-space: nowrap;
}

.aurora-nav .btn-nav-action {
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
    white-space: nowrap;
}

.aurora-nav .btn-login {
    background-color: #D31027;
    color: #FFFFFF;
    border: 1px solid #D31027;
}

.aurora-nav .btn-login:hover {
    background-color: #b50e21;
    border-color: #b50e21;
}

.aurora-nav .btn-logout {
    background-color: transparent;
    color: #131313;
    border: 1px solid #E5D3B3;
}

.aurora-nav .btn-logout:hover {
    color: #D31027;
    border-color: #D31027;
    background-color: rgba(211, 16, 39, 0.02);
}

.aurora-nav .nav-toggle {
    display: none;
    flex-direction: column;
    justify-content: space-between;
    width: 24px;
    height: 18px;
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 0;
}

.aurora-nav .nav-toggle span {
    display: block;
    width: 100%;
    height: 2px;
    background-color: #131313;
    transition: all 0.3s ease;
}

@media (max-width: 1024px) {
    .aurora-nav .nav-toggle {
        display: flex;
    }
    
    .aurora-nav .nav-toggle.open span:nth-child(1) {
        transform: translateY(8px) rotate(45deg);
    }
    
    .aurora-nav .nav-toggle.open span:nth-child(2) {
        opacity: 0;
    }
    
    .aurora-nav .nav-toggle.open span:nth-child(3) {
        transform: translateY(-8px) rotate(-45deg);
    }
    
    .aurora-nav .nav-right {
        position: absolute;
        top: 70px;
        left: 0;
        right: 0;
        background-color: #FFFFFF;
        border-bottom: 1px solid #E5D3B3;
        flex-direction: column;
        align-items: stretch;
        padding: 24px;
        gap: 24px;
        height: auto;
        display: none;
        box-shadow: 0 10px 30px rgba(19, 19, 19, 0.08);
    }
    
    .aurora-nav .nav-right.show {
        display: flex;
    }
    
    .aurora-nav .nav-menu {
        flex-direction: column;
        align-items: stretch;
        gap: 16px;
        height: auto;
    }
    
    .aurora-nav .nav-item {
        height: auto;
    }
    
    .aurora-nav .nav-link {
        padding: 8px 0;
        display: block;
    }
    
    .aurora-nav .nav-user-area {
        border-top: 1px solid #E5D3B3;
        padding-top: 16px;
        flex-direction: column;
        align-items: stretch;
    }
}
</style>

<div class="aurora-nav">
    <div class="nav-brand">
        <a href="<?= $root_path ?>informatie/home.php">Aurora</a>
    </div>

    <button class="nav-toggle" id="navToggle" aria-label="Menu openen">
        <span></span>
        <span></span>
        <span></span>
    </button>

    <div class="nav-right" id="navRight">
        <ul class="nav-menu">
            <li class="nav-item">
                <a class="nav-link" href="<?= $root_path ?>informatie/home.php">Home</a>
            </li>
            
            <?php if ($isAdmin): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $root_path ?>account-registratie/account-beheren/index.php">Account beheren</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $root_path ?>medewerker-registratie/medewerker-beheren/index.php" onclick="alert('Medewerker beheren is nog in ontwikkeling.'); return false;">Medewerker beheren</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $root_path ?>melding/melding-beheren/index.php" onclick="alert('Melding beheren is nog in ontwikkeling.'); return false;">Melding beheren</a>
                </li>
            <?php endif; ?>
            <?php if ($isStaff): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $root_path ?>ticket-reseveren/ticket-beheren/index.php">Ticket beheren</a>
                </li>
            <?php endif; ?>
            <?php if ($isAdmin): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $root_path ?>voorstelling-opstellen/voorstelling-beheren/index.php" onclick="alert('Voorstelling beheren is nog in ontwikkeling.'); return false;">Voorstelling beheren</a>
                </li>
            <?php endif; ?>
        </ul>

        <div class="nav-user-area">
            <?php if ($isIngelogd): ?>
                <span class="nav-user-info">Welkom, <strong><?= htmlspecialchars($_SESSION['naam'] ?? 'Gebruiker') ?></strong></span>
                <a href="<?= $root_path ?>uitloggen.php" class="btn-nav-action btn-logout">Uitloggen</a>
            <?php else: ?>
                <a href="<?= $root_path ?>login.php" class="btn-nav-action btn-login">Inloggen</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const navToggle = document.getElementById("navToggle");
    const navRight = document.getElementById("navRight");

    if (navToggle && navRight) {
        navToggle.addEventListener("click", function() {
            navToggle.classList.toggle("open");
            navRight.classList.toggle("show");
        });
    }
});
</script>
