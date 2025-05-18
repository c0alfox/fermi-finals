<?php
require_once 'functions_prelude.php';

function get_json_contents(array $required_params): Response {
    try {
        $data = json_decode(file_get_contents("php://input"), true);
    } catch (Exception $e) {
        return new Response(400, 'Corpo della richiesta malformato');
    }

    foreach ($required_params as $param) {
        if (!isset($data[$param])) {
            return new Response(422, "Parametro $param mancante");
        }
    }

    return new Response(200, "", $data);
}

function has_required_parameters(array $source, array $required): bool {
    foreach ($required as $param) {
        if (!isset($source[$param])) {
            return false;
        }
    }

    return true;
}