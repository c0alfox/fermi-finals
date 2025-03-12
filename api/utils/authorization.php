<?php

$PERMISSION_READ = 0b1;
$PERMISSION_COMMENT = 0b10;
$PERMISSION_EDIT = 0b100;
$PERMISSION_EVAL = 0b1000;
$PERMISSION_ADMIN = 0b10000;

function permissions_get_all() {
    global $PERMISSION_READ;
    global $PERMISSION_COMMENT;
    global $PERMISSION_EDIT;
    global $PERMISSION_EVAL;
    global $PERMISSION_ADMIN;

    return $PERMISSION_READ
        | $PERMISSION_COMMENT
        | $PERMISSION_EDIT
        | $PERMISSION_EVAL
        | $PERMISSION_ADMIN;
}

function permissions_valid($perm) {
    return $perm >= 0 && $perm <= permissions_get_all();
}

function permissions_min($perma, $permb) {
    return $perma & $permb;
}

function permissions_check($perms, $permtype) {
    return (bool)($perms & $permtype);
}