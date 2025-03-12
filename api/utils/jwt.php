<?php
$JWT_EXPIRY_TIME = 600;

function base64url_encode($data) {
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
}

function base64url_decode($data) {
    return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
}

function sign($data) {
    return hash_hmac('sha256', $data, "íãï«é1u&«ôÄÍô¬Ñd6AÌË;oÕ¨ÏPxïDÕ0É]Ãîºø¼·úQì[³ÌöBÛ!î", true);
}

function verify($data, $signature) {
    return hash_equals(sign($data), base64url_decode($signature));
}

function jwt_create($header_data, $payload_data) {
    $header = array_merge([
        'typ' => 'JWT',
        'alg' => 'HS256',
        'iat' => time(),
        'iss' => 'giuseppepappalardo.altervista.org'
    ], $header_data);

    $header = base64url_encode(json_encode($header));
    $payload = base64url_encode(json_encode($payload_data));
    $signature = sign("$header.$payload");
    $signature = base64url_encode($signature);
    return "$header.$payload.$signature";
}

function jwt_split($jwtstring) {
    try {
        $arr = explode('.', $jwtstring, 3);
        if (count($arr) != 3) {
            return NULL;
        }

        return ['header' => $arr[0], 'payload' => $arr[1], 'signature' => $arr[2]];
    } catch (Exception $e) {
        return NULL;
    }
    return $arr;
}

function jwt_decode($jwtstring) {
    try {
        $split = jwt_split($jwtstring);
        $res = [];
        $res['header'] = json_decode(base64url_decode($split['header']), true);
        $res['payload'] = json_decode(base64url_decode($split['payload']), true);
        $res['signature'] = base64url_decode($split['signature']);
    } catch (Exception $e) {
        return NULL;
    }

    return $res;
}

function jwt_is_valid($jwtstring) {
    $split = jwt_split($jwtstring);

    if ($split === NULL) {
        return 0;
    }

    $header = $split['header'];
    $payload = $split['payload'];
    $signature = $split['signature'];

    $ver_sign = verify("$header.$payload", $signature);

    try {
        $header = json_decode(base64url_decode($header), true);
    } catch (Exception $e) {
        return -1 * $ver_sign;
    }

    $ver_exp = !isset($header['exp']) || $header['exp'] > time();

    if ($ver_sign && $ver_exp) {
        return 1;
    }
    if (!$ver_sign) {
        return -1;
    }
    if (!$ver_exp) {
        return -2;
    }
}

function jwt_refresh($jwt, $expiry_time = null) {
    if (is_null($expiry_time)) {
        global $JWT_EXPIRY_TIME;
        $expiry_time = $JWT_EXPIRY_TIME;
    }

    return jwt_create(array_merge($jwt['header'], ['exp' => time() + $expiry_time]), $jwt['payload']);
}