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
    <title> Accedi </title>
</head>

<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
    <div class="card shadow-md">
        <div class="card-header text-bg-primary bg-gradient">
            <h2 class="text-center mb-0"> Accedi </h2>
        </div>
        <div class="card-body">
            <form class="p-4" method="POST" action="login.php">
                <div class="mb-3">
                    <label for="email" class="form-label">Indirizzo e-mail</label>
                    <input type="email" class="form-control" id="email" name="email">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
                    <p class="text-danger text-center mb-0"> Indirizzo e-mail o password non validi </p>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary bg-gradient shadow-sm d-block mx-auto">Submit</button>
            </form>
        </div>
    </div>
</body>

</html>