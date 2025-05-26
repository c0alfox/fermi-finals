<?php
require_once '../prelude.php';
require_once "$root/functions/user.php";
require_once "$root/functions/request.php";

if ($_SERVER['REQUEST_METHOD'] != 'POST' && $_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);  # Method not allowed
    die();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (has_required_parameters($_POST, ['email', 'password'])) {
        if (User\login($_POST['email'], $_POST['password'], Perms\get_all())->is_ok()) {
            header("Location: /");
            exit();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="it">

<head>
    <?php include "$root/components/head.php" ?>
    <title> Registrati </title>
</head>

<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
    <div class="card shadow-md">
        <div class="card-header text-bg-primary bg-gradient">
            <h2 class="text-center mb-0"> Registrati </h2>
        </div>
        <form method="POST" action="signup.php">
            <div class="p-4">
                <div class="row mb-3">
                    <div class="col-6">
                        <label for="name" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="name" name="name">
                    </div>
                    <div class="col-6">
                        <label for="surname" class="form-label">Cognome</label>
                        <input type="text" class="form-control" id="surname" name="surname">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Indirizzo e-mail</label>
                    <input type="email" class="form-control" id="email" name="email">
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="col-6">
                        <label for="password_confirm" class="form-label">Conferma password</label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm">
                    </div>
                </div>

                <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
                    <p class="text-danger text-center mb-0"> Indirizzo e-mail o password non validi </p>
                <?php endif; ?>

            </div>
            <div class="card-footer p-4 d-flex align-items-center justify-content-between">
                <p class="small text-center text-muted m-0">Hai già un account? <a href="login.php"> Accedi </a></p>
                <button type="submit" class="btn btn-primary bg-gradient shadow-sm"> Iscriviti </button>
            </div>
        </form>
    </div>
</body>

</html>