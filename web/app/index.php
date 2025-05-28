<?php 
require_once 'prelude.php';
require_once "$root/functions/suggestions.php";

$resp = Suggestions\featured();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <?php
    include "$root/components/head.php";
    include "$root/components/navbar.php";
    include "$root/components/search.php";
    include "$root/components/footer.php";
    include "$root/components/jumbotrons/project.php";
    ?>
    <title> Pagina Principale </title>
</head>
<body>
    <?php navbar(0) ?>

    <h1 class="text-center mt-2">Test&Tell</h1>

    <?php search() ?>


    <?php
    if ($resp->is_ok()):
        $data = $resp->data;
    ?>
    <main class="container py-5">
        <div class="row clearfix">
            <?php if ($data['count'] >= 1): ?>
            <div class="col-7 jumbotron-lg position-relative">
                <?php Jumbotron\project($data['content'][0]) ?>
            </div>
            <?php endif; ?>

            <?php if ($data['count'] >= 2): ?>
                <div class="col-5 jumbotron position-relative align-self-end">
                    <?php Jumbotron\project($data['content'][2]) ?>
                </div>
            <?php endif; ?>

            <?php if ($data['count'] >= 3): ?>
                <div class="col-5 jumbotron position-relative">
                    <?php Jumbotron\project($data['content'][2]) ?>
                </div>
            <?php endif ?>

            <?php if ($data['count'] >= 2): ?>
                <div class="col-7 jumbotron-lg position-relative">
                    <?php Jumbotron\project($data['content'][1]) ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <?php else: var_dump($resp)?>
    <?php endif; ?>

    <?php footer() ?>
</body>
</html>