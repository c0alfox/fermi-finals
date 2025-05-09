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
} elseif ($user_id === null) {
    header('Location: /app/auth/login.php');
    die();
}

Auth\refresh_token();

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
            <p class="mx-5 muted italic" id="bio" data-empty> Nessuna biografia inserita </p>
        <?php else: ?>
            <p class="mx-5" id="bio"> <?= htmlspecialchars($user['bio']) ?> </p>
        <?php endif; ?>
        
        <div class="clearfix">
            <h2 class="float-start"> Progetti <?= $p_count == 0 ? "": "($p_count)" ?> </h2>
            <?php if ($is_self): ?>
            <a href="/app/project/create.php" class="btn btn-primary btn-add float-end">Aggiungi un nuovo progetto</a>
            <?php endif; ?>
        </div>

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
                            <p class="float-end">Creato il: <?= date('d/m/Y H:i:s', strtotime($p['project_datetime']))?> </p>
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
    </main>

    <?php if ($is_self): ?>
    <script src="/static/js/profile.js" type="module"></script>
    <?php endif; ?>
</body>
</html>