<?php
namespace Perms;

use \Auth as Auth;

require_once 'prelude.php';
require_once "$root/auth/authentication.php";

function get(int $default = PERMISSION_READ) {
    $perms = $default;
    if (true || Auth\has_valid_user()) {
        $perms = Auth\get_permissions();
    };
    return $perms;
}