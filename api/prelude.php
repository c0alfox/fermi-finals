<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/prelude_commons.php';

require_once "$root/functions/response.php";
require_once "$root/functions/request.php";

const UNSUPPORTED_METHOD = new Response(405, "La risosrsa richiesta non consente il metodo selezionato");

function set_headers(string $allow_methods) {
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    header("Access-Control-Allow-Methods: $allow_methods");
}

function user_has_permissions(int $permtype) {
    global $root;
    require_once "$root/auth/authentication.php";
    require_once "$root/auth/permissions.php";

    return Perms\check(Auth\get_permissions(), $permtype);
}