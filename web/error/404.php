<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/prelude.php';

if (serve_json()) {
    set_headers("GET");
    (new Response(404, "Risorsa Inesistente"))->api_response();
    exit();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/components/head.php" ?>
    <title>Errore 404</title>
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
    <div class="text-center">
        <h1 class="display-1 fw-bold text-primary">404</h1>
        <h2 class="mb-3"> Pagina non trovata </h2>
        <p class="lead mb-4"> La pagina che stai cercando non esiste o è stata spostata </p>
        <a href="/" class="btn btn-primary btn-lg"> Torna alla home </a>
    </div>
</body>
</html>