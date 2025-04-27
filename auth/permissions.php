<?php
namespace Perms;

require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/perms_constants.php';

function get_all() {
    return PERMISSION_READ
        | PERMISSION_COMMENT
        | PERMISSION_EDIT
        | PERMISSION_EVAL
        | PERMISSION_ADMIN;
}

function is_valid(int $perm) {
    return $perm >= 0 && $perm <= get_all();
}

function min(int $perma, int $permb) {
    return $perma & $permb;
}

function check(int $perms, int $permtype) {
    return (bool)($perms & $permtype);
}