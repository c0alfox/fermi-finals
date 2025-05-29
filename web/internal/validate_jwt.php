<?php
require_once "internal_prelude.php";
require_once "$root/api/prelude.php";
require_once "$root/auth/authentication";

set_headers("POST, OPTIONS");
handle_options_method();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uid = isset($_POST['jwt']);

    if ($uid === false) {
        (new Response(200, "Token non valido", [
            "valid" => false,
            "user_id" => null
        ]))->api_response();
    } else {
        (new Response(200, "Token valido", [
            "valid" => true,
            "user_id" => $uid
        ]))->api_response();
    }
}

UNSUPPORTED_METHOD->api_response();