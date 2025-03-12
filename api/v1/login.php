<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST');

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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($data['email']) || !isset($data['password'])) {
        http_response_code(422);  # Unprocessable Content
        die(json_encode(['message' => 'Parametri richiesti mancanti']));
    }

    try {
        $sql = "SELECT IDUtente, Password FROM PrgUtenti WHERE Email = :email";
        $s = $pdo->prepare($sql);
        $success = $s->execute(['email' => $data['email']]);
    } catch(PDOException $e) {
        http_response_code(500);  # Internal Server Error
        die(json_encode(['message' => 'Autenticazione fallita']));
    }

    if (!$s->rowCount()) {
        http_response_code(404);  # Not Found
        die(json_encode(['message' => 'Utente inesistente']));
    }

    $row = $s->fetch();
    if (!password_verify($data['password'], $row['Password'])) {
        http_response_code(401);  # Unauthorized
        die(json_encode(['message' => 'Password errata']));
    }

    if (!isset($data['permissions'])) {
        $data['permissions'] = 0b1;
    }

    if (!is_numeric($data['permissions'])) {
        http_response_code(400);  # Bad Request
        $pmax = permissions_get_all();
        die(json_encode(['message' => "I permessi devono essere un numero compreso tra 0 e $pmax"]));
    }

    $permissions = $data['permissions'];
    $permissions = max(0, $permissions);
    $permissions &= permissions_get_all();

    http_response_code(200);  # OK
    global $JWT_EXPIRY_TIME;
    $exp = time() + $JWT_EXPIRY_TIME;
    echo json_encode([
        'message' => 'Login effettuato con successo', 
        'jwt' => jwt_create(
            ['exp' => $exp],
            ['user_id' => $row['IDUtente'], 'permissions' => $permissions]
        ),
        'expiry' => $exp
    ]);
    exit();
} 

http_response_code(405);  # Unsupported method
die(json_encode(['message' => 'Metodo non supportato']));