<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: OPTIONS, GET');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);  # No Content
    exit();
}

require_once('../utils/pdo.php');
require_once('../utils/jwt.php');
require_once('../utils/authentication.php');
require_once('../utils/authorization.php');

try {
    $data = json_decode(file_get_contents("php://input"), true);
} catch (Exception $e) {
    http_response_code(400);  # Bad Request
    die(json_encode(['message' => 'Corpo della richiesta malformato']));
}

exit();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!isset($data['limite'])) {
        $data['limite'] = 10;
    }

    if (!isset($data['pagina'])) {
        $data['pagina'] = 1;
    }

    $sql = "SELECT * FROM PrgProgetti";

    $whereOptions = [];
    $whereClause = implode(' AND ', $whereOptions);

    
}