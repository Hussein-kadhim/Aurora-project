<?php
// Bestaande voorstelling wijzigen logica (nog in ontwikkeling)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    // Wijzigingslogica of redirect naar een formulier hier
    
    // Redirect terug na wijziging
    header('Location: ../voorstelling-beheren/index.php');
    exit;
} else {
    // Redirect als deze pagina per ongeluk wordt bezocht zonder POST verzoek
    header('Location: ../voorstelling-beheren/index.php');
    exit;
}
?>
