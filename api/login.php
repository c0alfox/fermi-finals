<?php
require_once 'prelude.php';
set_headers('POST');

require_once "$root/functions/user.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = get_json_contents(['email', 'password'])
        ->respond_if_error()
        ->data;

    if (!isset($data['permissions'])) {
        $data['permissions'] = 0b1;
    }

    if (!is_numeric($data['permissions'])) {
        http_response_code(400);  # Bad Request
        $pmax = Perms\get_all();
        die(json_encode(['message' => "I permessi devono essere un numero compreso tra 0 e $pmax"]));
    }

    User\login($data['email'], $data['password'], $data['permissions'])
        ->respond_if_error()
        ->api_response();
} 

UNSUPPORTED_METHOD->api_response();