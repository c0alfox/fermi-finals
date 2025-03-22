<?php
require_once('jwt.php');
require_once('pdo.php');
require_once('authorization.php');

function authentication_has_token() {
    return isset($_SERVER['HTTP_AUTHORIZATION'])
        && str_starts_with($_SERVER['HTTP_AUTHORIZATION'], 'Bearer ');
}

function authentication_get_jwtstring() {
    return substr($_SERVER['HTTP_AUTHORIZATION'], strlen('Bearer '));
}

function authentication_get_jwt() {
    return jwt_decode(authentication_get_jwtstring());
}

function authentication_has_valid_jwt() {
    if (!isset($_SERVER['HTTP_AUTHORIZATION']))
        return false;

    $jwtstring = authentication_get_jwtstring();
    return jwt_is_valid($jwtstring) == 1;
}

function authentication_has_valid_user() {
    global $pdo;

    try {
        if (!authentication_has_token() || !authentication_has_valid_jwt()) {
            return false;
        }

        $jwt = authentication_get_jwt();
        if (!isset($jwt['payload']['permissions'])
            || !permissions_valid($jwt['payload']['permissions'])) {
            return false;
        }

        if (isset($jwt['payload']['user_id'])) {
            try {
                $s = $pdo->prepare('SELECT 1 FROM PrgUsers WHERE user_id = :user_id');
                $success = $s->execute(['user_id' => $jwt['payload']['user_id']]);
                return $s->rowCount() > 0;
            } catch(PDOException $e) {
                return false;
            }
        }
        
        return false;

    } catch (Exception $e) {
        return false;
    }
}

function authentication_get_user() {
    global $pdo;

    try {
        $jwt = authentication_get_jwt();
        $s = $pdo->prepare('SELECT user_id FROM PrgUsers WHERE user_id = :user_id');
        $success = $s->execute(['user_id' => $jwt['payload']['user_id']]);

        if ($s->rowCount()) {
            return $s->fetch(PDO::FETCH_ASSOC);
        }

        return null;

    } catch(PDOException $e) {
        return null;
    }
}

function authentication_get_permissions() {
    try {
        $jwt = authentication_get_jwt();
        return $jwt['payload']['permissions'];
    } catch(PDOException $e) {
        return null;
    }
}