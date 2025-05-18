<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/prelude.php';

if (serve_json()) {
    set_headers("GET");
    (new Response(403, "Accesso Negato"))->api_response();
    exit();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/components/head.php" ?>
    <title>Errore 403</title>
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
    <div class="text-center">
        <h1 class="display-1 fw-bold text-danger">403</h1>
        <h2 class="mb-3"> Accesso negato </h2>
        <p class="lead mb-4"> Non hai il permesso di accedere a questa risorsa </p>
        <a href="/" class="btn btn-danger btn-lg"> Torna alla home </a>
    </div>
</body>
</html>