<?php
require_once '../prelude.php';

$is_own = false;

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <?php 
        include "$root/components/head.php";
        include "$root/components/navbar.php";
    ?>
    <title> Dettagli Progetto </title>
</head>
<body class="container">
    <?php navbar() ?>

    <h1 class="text-center mt-2"> <?= htmlspecialchars("Titolo progetto") ?> </h1>
    <p> <?= htmlspecialchars("abstract") ?></p>

    <h2> Revisioni (42) </h2>
    <?php if ($is_own): ?>

    <?php else: ?>
    
    <?php endif; ?>
</body>
</html>