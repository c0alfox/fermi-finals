<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/authentication.php';

class Response {
    public int $response_code;
    public string $message;
    public $data;

    public function __construct(int $response_code, string $message, $data = []) {
        $this->response_code = $response_code;
        $this->message = $message;
        $this->data = $data;
    }

    public function api_response($refresh_jwt = false) {
        http_response_code($this->response_code);

        $next_jwt = Auth\get_jwt();

        if (!$refresh_jwt || $next_jwt === null) {
            echo json_encode(array_merge($this->data, [
                "message" => $this->message
            ]));
        } else {
            echo json_encode(array_merge($this->data, [
                "message" => $this->message,
                "jwt" => $next_jwt->refresh()->to_string()
            ]));
        }

        exit();
    }

    public function is_error() {
        return $this->response_code >= 300;
    }

    public function is_ok() {
        return !$this->is_error();
    }

    public function respond_if_error($refresh_jwt = false): Response {
        if ($this->is_error()) {
            $this->api_response($refresh_jwt);
            exit();
        } else {
            return $this;
        }
    }
}