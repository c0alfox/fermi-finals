<?php
require_once '../prelude.php';
require_once "$root/functions/user.php";
require_once "$root/functions/request.php";
require_once "$root/auth/validation.php";

if ($_SERVER['REQUEST_METHOD'] != 'POST' && $_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);  # Method not allowed
    die();
}

$fulfills_requirements = true;
$valid_password = true;
$valid_email = true;
$matching_passwords = true;
$resp = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $fulfills_requirements = has_required_parameters($_POST, [
        'name',
        'surname',
        'email',
        'password',
        'password_confirm'
    ]);
    if (!$fulfills_requirements) goto output;

    $valid_password = is_valid_password($_POST['password']);
    $valid_email = is_valid_email($_POST['email']);
    if (!$valid_password || !$valid_email) goto output;

    $matching_passwords = $_POST['password'] == $_POST['password_confirm'];
    if (!$fulfills_requirements) goto output;

    $_POST['bio'] = trim($_POST['bio']);

    $resp = User\create(
        $_POST['email'],
        $_POST['name'],
        $_POST['surname'],
        $_POST['password'],
        empty($_POST['bio']) ? null : $_POST['bio']
    );

    if ($resp->is_ok()) {
        header('Location: login.php');
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

    <title> Registrati </title>
</head>

<body class="bg-light">
    <div class="card shadow-md w-50 mx-auto mt-5">
        <div class="card-header text-bg-primary bg-gradient">
            <h2 class="text-center mb-0"> Registrati </h2>
        </div>
        <form method="POST" action="signup.php">
            <div class="p-4">
                <div class="row mb-3">
                    <div class="col-6">
                        <label for="name" class="form-label">Nome*</label>
                        <input type="text" class="form-control" id="name" name="name" required
                            value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                        >
                    </div>
                    <div class="col-6">
                        <label for="surname" class="form-label">Cognome*</label>
                        <input type="text" class="form-control" id="surname" name="surname" required
                            value="<?= htmlspecialchars($_POST['surname'] ?? '') ?>"
                        >
                    </div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Indirizzo e-mail*</label>
                    <input type="email" class="form-control" id="email" name="email" required
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    >
                    <?php if (!$valid_email): ?>
                        <p class="mb-0 small text-danger">Indirizzo e-mail non valido</p>
                    <?php endif; ?>
                    <?php if ($resp !== null): ?>
                        <p class="mb-0 small text-center text-danger"> Indirizzo email in uso </p>
                    <?php endif; ?>
                </div>

                <div class="row mb-1">
                    <div class="col-6">
                        <label for="password" class="form-label">Password*</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="col-6">
                        <label for="password_confirm" class="form-label">Conferma password*</label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                        <?php if (!$matching_passwords): ?>
                            <p class="mb-0 small text-danger">Le due password devono combaciare</p>
                        <?php endif; ?>
                    </div>
                </div>
                <p class="mb-3 small <?= $valid_password ? 'text-muted' : 'text-danger' ?>">
                    La password deve essere almeno di 8 caratteri e contenere una lettera minuscola, maiuscola, un numero e un carattere speciale tra @!_-/
                </p>

                <div class="mb-3">
                    <label for="bio" class="form-label">Biografia</label>
                    <textarea name="bio" id="bio" class="form-control">
                        <?= htmlspecialchars($_POST['bio'] ?? '') ?>
                    </textarea>
                </div>

                <p class="mb-0 small text-center <?= $fulfills_requirements ? 'text-muted' : 'text-danger' ?> ">
                    I campi contrassegnati da asterisco (*) sono obbligatori
                </p>
            </div>
            <div class="card-footer p-4 d-flex align-items-center justify-content-between">
                <p class="small text-center text-muted m-0">Hai già un account? <a href="login.php"> Accedi </a></p>
                <button type="submit" class="btn btn-primary bg-gradient shadow-sm"> Iscriviti </button>
            </div>
        </form>
    </div>
</body>

</html>