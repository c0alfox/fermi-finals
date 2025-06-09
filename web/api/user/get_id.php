<?php
require_once "../prelude.php";

require_once "$root/auth/authentication.php";

set_headers('GET, OPTIONS');
handle_options_method();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    (new Response(
        200,
        'ID Utente registrato',
        ["user_id" => Auth\get_user_id()]
    ))->api_response();
}

UNSUPPORTED_METHOD->api_response();