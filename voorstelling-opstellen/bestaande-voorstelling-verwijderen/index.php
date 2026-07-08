<?php
// Bestaande voorstelling verwijderen logica (nog in ontwikkeling)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    // Verwijderingslogica hier
    
    // Redirect terug na verwijdering
    header('Location: ../voorstelling-beheren/index.php');
    exit;
} else {
    // Redirect als deze pagina per ongeluk wordt bezocht zonder POST verzoek
    header('Location: ../voorstelling-beheren/index.php');
    exit;
}
?>
