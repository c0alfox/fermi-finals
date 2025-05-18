<?php
require_once '../prelude.php';
require_once "$root/functions/project.php";

if (!isset($_GET['id'])) {
    (new Response(404, 'Progetto inesistente'))->die_if_error();
}

$project_id = $_GET['id'];
$user_id = Auth\get_user_id();
$can_edit = Project\user_can_edit($user_id, $project_id)
    ->die_if_error()
    ->data;

$project = Project\get($project_id)
    ->die_if_error()
    ->data;

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

    <h1 class="text-center mt-2"> <?= htmlspecialchars($project['project_data']['title']) ?> </h1>

    <h2> Abstract </h2>
    <?php if ($project['project_data']['abstract'] === null): ?>
        <p class="italic mx-5" id="abs" data-empty> Il progetto non ha un abstract </p>
    <?php else: ?>
        <p class="mx-5" id="abs"> <?= htmlspecialchars($project['project_data']['abstract']) ?></p>
    <?php endif; ?>

    <h2> Revisioni (<?= $project['project_data']['revision_count'] ?>) </h2>
    <?php if ($p_count == 0): ?>
        <p class="mx-5 muted italic"> Nessun progetto caricato </p>
    <?php else: ?>
        <div class="mx-5">
            <?php foreach ($projs as $p): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title m-0"> <?= htmlspecialchars($p['title']) ?> </h5>
                </div>
                <div class="card-body">
                    <div class="clearfix">
                        <p class="float-start">Totale revisioni: <?= $p['revision_count'] ?> </p>
                        <p class="float-end">Creato il <?= date('d/m/Y \a\l\l\e H:i:s', strtotime($p['project_datetime']))?> </p>
                    </div>
                    <?php if ($p['abstract'] === null): ?>
                        <p class="card-text italic"> Il progetto non ha abstract </p>
                    <?php else: ?>
                        <p class="card-text"> <?= htmlspecialchars($p['abstract']) ?> </p>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <a href="/app/project/details.php?id=<?= $p['project_id'] ?>" class="btn btn-primary float-end">Vai</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($can_edit): ?>
    <script src="/static/js/project.js" type="module"></script>
    <?php endif; ?>
</body>
</html>