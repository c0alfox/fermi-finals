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
        include "$root/components/footer.php";
    ?>
    <title> Dettagli Progetto </title>
</head>
<body>
    <?php navbar() ?>

    <main class="container">
        <h1 class="text-center mt-2"> <?= htmlspecialchars($project['project_data']['title']) ?> </h1>

        <h2> Abstract </h2>
        <?php if ($project['project_data']['abstract'] === null): ?>
            <p class="italic mx-5" id="abs" data-empty> Il progetto non ha un abstract </p>
        <?php else: ?>
            <p class="mx-5" id="abs"> <?= htmlspecialchars($project['project_data']['abstract']) ?></p>
        <?php endif; ?>

        <h2> Revisioni (<?= $project['project_data']['revision_count'] ?>) </h2>
        <div class="mx-5">
            <?php foreach ($project['revisions'] as $r): ?>
            <div class="card mt-3">
                <div class="card-header clearfix">
                    <h5 class="card-title m-0 float-start"> Revisione <?= $r['revision_number'] ?> </h5>
                    <p class="float-end m-0">Del <?= date('d/m/Y \a\l\l\e H:i:s', strtotime($r['revision_datetime']))?> </p>
                </div>
                <div class="card-body">
                    <?php if ($r['motivations'] === null): ?>
                        <p class="card-text italic"> La revisione non presenta alcuna descrizione aggiuntiva </p>
                    <?php else: ?>
                        <p class="card-text"> <?= htmlspecialchars($r['motivations']) ?> </p>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <a href="#" class="btn btn-primary float-end">Vai</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if ($can_edit): ?>
        <script src="/static/js/project.js" type="module"></script>
        <?php endif; ?>

    </main>

    <?php footer() ?>
</body>
</html>