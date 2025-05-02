<?php
namespace Auth;

use function Perms\is_valid;

require_once 'jwt.php';
require_once 'permissions.php';

const AUTH_COOKIE_NAME = 'auth_token';

function set_token(string $token) {
    global $JWT_EXPIRY_TIME;

    setcookie(AUTH_COOKIE_NAME, $token, [
        "expires" => time() + $JWT_EXPIRY_TIME,
        "httponly" => true,
        "secure" => true,
    ]);
}

function has_token() {
    return isset($_COOKIE[AUTH_COOKIE_NAME]);
}

function get_jwtstring(): string | null {
    return isset($_COOKIE[AUTH_COOKIE_NAME])
        ? $_COOKIE[AUTH_COOKIE_NAME]
        : null;
}

function get_jwt() {
    $jwtstring = get_jwtstring();
    return $jwtstring === null
        ? null
        : \JWT::from_string($jwtstring);
}

function has_valid_jwt() {
    if (!isset($_SERVER['HTTP_AUTHORIZATION']))
        return false;

    $jwtstring = get_jwtstring();
    return \JWT::is_valid_string($jwtstring) == 1;
}

function has_valid_user() {
    global $pdo;

    try {
        if (!has_token() || !has_valid_jwt()) {
            return false;
        }

        $jwt = get_jwt();
        if (!isset($jwt->payload['permissions'])
            || !is_valid($jwt->payload['permissions'])) {
            return false;
        }

        if (isset($jwt->payload['user_id'])) {
            try {
                $s = $pdo->prepare('SELECT 1 FROM PrgUsers WHERE user_id = :user_id');
                $s->execute(['user_id' => $jwt->payload['user_id']]);
                return $s->rowCount() > 0;
            } catch(\PDOException $e) {
                return false;
            }
        }
        
        return false;

    } catch (\Exception $e) {
        return false;
    }
}

function get_user() {
    global $pdo;

    try {
        $jwt = get_jwt();
        $s = $pdo->prepare('SELECT user_id FROM PrgUsers WHERE user_id = :user_id');
        $s->execute(['user_id' => $jwt->payload['user_id']]);

        if ($s->rowCount()) {
            return $s->fetch(\PDO::FETCH_ASSOC);
        }

        return null;

    } catch(\PDOException $e) {
        return null;
    }
}

function get_permissions(): int {
    if (!has_valid_jwt()) {
        return 0;
    }

    $jwt = get_jwt();
    return $jwt->payload['permissions'];
}

function get_user_id() {
    if (!has_valid_jwt()) {
        return null;
    }

    $jwt = get_jwt();
    return $jwt->payload['user_id'];
}