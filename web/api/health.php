<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/prelude.php';

if (serve_json()) {
    set_headers("GET");
    (new Response(200, "OK", ["message" => "Il sito è attivo e funzionante."]))->api_response();
    exit();
}