<?php
require_once "../prelude.php";
require_once "$root/auth/authentication.php";
require_once "$root/components/navbar.php";
require_once "$root/functions/user.php";

$user_id = Auth\get_user_id();
$is_self = true;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
	$user_id = $_GET['id'];
    $is_self = false;
}

if ($is_self) {
    die();
}

$user = User\fetch($user_id)
    ->die_if_error()
    ->data;

$full_name = htmlspecialchars($user['name'] . ' ' . $user['surname']);
$p_count = User\project_count($user_id)->data;
$projs = User\projects($user_id)->data;
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <?php include "$root/components/head.php" ?>
    <title> Profilo di <?= $full_name ?> </title>
</head>
<body>
    <?php navbar() ?>
    <main class="container my-2">
        <h1 class="text-center"> <?= $full_name ?> </h1>
        <div class="clearfix">
            <p class="small muted text-center float-start"> <?= htmlspecialchars($user['email']) ?> </p>
            <p class="small mx-3 muted float-end">Account creato il: <?= date("d/m/Y", strtotime($user['user_datetime'])) ?></p>
        </div>
        <h2> Biografia </h2>
        <?php if (empty($user['bio'])): ?>
            <p class="mx-5 muted small"> <i> Nessuna biografia inserita </i> </p>
        <?php else: ?>
            <p class="mx-5"> <?= htmlspecialchars($user['bio']) ?> </p>
        <?php endif; ?>
        <?php if ($p_count == 0): ?>
            <h2> Progetti </h2>
            <p class="mx-5 muted small"> <i> Nessun progetto caricato </i> </p>
        <?php else: ?>
            <h2> Progetti ( <?= $p_count ?> ) </h2>
            <div class="mx-5">
                <?php foreach ($projs as $p): ?>
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title"> <?= htmlspecialchars($p['title']) ?> </h5>
                    </div>
                    <div class="card-body">
                        <div class="clearfix">
                            <p class="float-start">Totale revisioni: <?= $p['revision_count'] ?> </p>
                            <p class="float-end">Creato il: <?= date('d/m/Y H:i:s', strtotime($p['project_datetime']))?> </p>
                        </div>
                        <p class="card-text"> <?= $p['abstract'] ?> </p>
                    </div>
                    <div class="card-footer">
                        <a href="#" class="btn btn-primary float-end">Vai</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>