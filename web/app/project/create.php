<?php
require_once '../prelude.php';
require_once "$root/functions/project.php";
require_once "$root/functions/request.php";
require_once "$root/auth/validation.php";

if ($_SERVER['REQUEST_METHOD'] != 'POST' && $_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);  # Method not allowed
    die();
}

$uid = Auth\get_user_id();
if ($uid === null) {
    header('Location: /app/auth/login.php');
}

$fulfills_requirements = true;
$request_success = true;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $fulfills_requirements = has_required_parameters($_POST, [
        'title',
    ]);
    $fulfills_requirements &= !empty($_POST['title']);
    if (!$fulfills_requirements) goto output;

    $resp = Project\create(
        $uid,
        $_POST['title'],
        empty($_POST['abstract']) ? null : $_POST['abstract'],
    );

    $request_success = $resp->is_ok();

    if ($request_success) {
        header('Location: /app/user/profile.php');
        exit();
    }
}

output: 
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <?php include "$root/components/head.php" ?>

    <style>
        body, html {
            min-height: 100%;
        }
    </style>

    <title> Crea un nuovo progetto </title>
</head>

<body class="bg-light">
    <div class="card shadow-md w-50 mx-auto mt-5">
        <div class="card-header text-bg-primary bg-gradient">
            <h2 class="text-center mb-0"> Crea un nuovo progetto </h2>
        </div>
        <form method="POST">
            <div class="p-4">
                <div class="mb-3">
                    <label for="title" class="form-label">Titolo*</label>
                    <input type="text" class="form-control" id="title" name="title" required
                        value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                    >
                </div>

                <div class="mb-3">
                    <label for="bio" class="form-label">Abstract</label>
                    <textarea name="bio" id="bio" class="form-control">
                        <?= htmlspecialchars($_POST['bio'] ?? '') ?>
                    </textarea>
                </div>

                <?php if (!$request_success): ?>
                    <p class="small text-center text-danger">
                        Errore durante la creazione del progetto. Riprova più tardi.
                    </p>
                <?php endif; ?>

                <p class="mb-0 small text-center <?= $fulfills_requirements ? 'text-muted' : 'text-danger' ?> ">
                    I campi contrassegnati da asterisco (*) sono obbligatori
                </p>
            </div>
            <button type="submit" class="btn btn-primary bg-gradient shadow-sm mb-3 d-block mx-auto"> Crea </button>
        </form>
    </div>
</body>

</html>