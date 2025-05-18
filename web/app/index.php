<?php require_once 'prelude.php'?>
<!DOCTYPE html>
<html lang="it">
<head>
    <?php
    include "$root/components/head.php";
    include "$root/components/navbar.php";
    include "$root/components/search.php";
    ?>
    <title> Pagina Principale </title>
</head>
<body>
    <?php navbar() ?>

    <h1 class="text-center mt-2">Test&Tell</h1>

    <?php search() ?>
</body>
</html>