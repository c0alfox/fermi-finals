<?php
require_once "../prelude.php";
require_once "$root/functions/notifications.php";

set_headers("OPTIONS, DELETE");
handle_options_method();

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    if (!isset($_GET['id'])) {
        (new Response(
            400,
            'Richiesto parametro id'
        ))->api_response();
    }

    if (!is_numeric($_GET['id'])) {
        (new Response(
            422,
            'Il parametro id deve essere numerico'
        ))->api_response();
    }

    $user_id = Auth\get_user_id();

    if ($user_id === null) {
        UNAUTHORIZED->api_response();
    }

    $owned = Notifications\is_own($_GET['id'], $user_id)
        ->respond_if_error()
        ->data;
    
    if (!$owned) {
        FORBIDDEN->api_response();
    }

    Notifications\delete($_GET['id'])->api_response();
}

UNSUPPORTED_METHOD->api_response();