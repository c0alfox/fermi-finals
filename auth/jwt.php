<?php

use function Auth\set_token;
$JWT_EXPIRY_TIME = (int)getenv("JWT_EXPIRY_TIME");

function base64url_encode($data) {
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
}

function base64url_decode($data) {
    return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
}

function sign($data) {
    return hash_hmac('sha256', $data, getenv("JWT_SECRET"), true);
}

function verify($data, $signature): bool {
    return hash_equals(sign($data), base64url_decode($signature));
}

class JWT {
    public $header;
    public $payload;

    public function __construct(array $header, array $payload, bool $auto_expiry = false) {
        if ($auto_expiry) {
            global $JWT_EXPIRY_TIME;
            $expiry = time() + $JWT_EXPIRY_TIME;
            $header = array_merge($header, ['exp' => $expiry]);
        }

        $this->header = array_merge([
            'typ' => 'JWT',
            'alg' => 'HS256',
            'iat' => time(),
            'iss' => 'giuseppepappalardo.altervista.org'
        ], $header);

        $this->payload = $payload;
    }

    public function set_cookie() {
        set_token($this->to_string());
    }

    public function to_string(): string {
        $header = base64url_encode(json_encode($this->header));
        $payload = base64url_encode(json_encode($this->payload));

        $signature = sign("$header.$payload");
        $signature = base64url_encode($signature);

        return "$header.$payload.$signature";
    }

    public static function from_string($str): JWT | null {
        try {
            $split = JWT::string_to_raw_parts($str);
            if ($split === null) {
                return null;
            }

            if (!JWT::is_valid_from_splits(
                    $split['header'],
                    $split['payload'],
                    $split['signature']
                )) {
                return null;
            }

            $header = json_decode(base64url_decode($split['header']), true);
            $payload = json_decode(base64url_decode($split['payload']), true);
            $signature = base64url_decode($split['signature']);


            return new JWT($header, $payload);
        } catch (Exception $e) {
            return null;
        }
    }

    public function is_not_expired(): bool {
        return !isset($this->header['exp']) || $this->header['exp'] > time();
    }

    public static function string_to_raw_parts($str): array | NULL {
        $arr = explode('.', $str, 3);
        if (count($arr) != 3) {
            return NULL;
        }

        return [
            'header' => $arr[0],
            'payload' => $arr[1],
            'signature' => $arr[2]
        ];
    }

    public static function is_valid_from_splits(string $header, string $payload, string $signature): int {
        $is_signed = verify("$header.$payload", $signature);

        try {
            $header = json_decode(base64url_decode($header), true);
        } catch (Exception $e) {
            return -1 * $is_signed;
        }

        $not_expired = !isset($header['exp']) || $header['exp'] > time();

        if (!$is_signed) {
            return -1;
        }

        if (!$not_expired) {
            return -2;
        }

        return 1;
    }

    public static function is_valid_string($str): int {
        $split = JWT::string_to_raw_parts($str);
        if ($split === null) {
            return 0;
        }

        $header = $split['header'];
        $payload = $split['payload'];
        $signature = $split['signature'];

        return JWT::is_valid_from_splits($header, $payload, $signature);
    }

    public function refresh($expiry_time = null): JWT {
        if ($expiry_time === null) {
            global $JWT_EXPIRY_TIME;
            $expiry_time = $JWT_EXPIRY_TIME;
        }

        return new JWT(
            array_merge($this->header, ['exp' => time() + $expiry_time]),
            $this->payload
        );
    }

    public function get_expiry() {
        return $this->header['exp'];
    }
}
