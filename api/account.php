<?php
require_once 'prelude.php';
set_headers('OPTIONS, HEAD, POST, GET, PUT, DELETE');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);  # No Content
    exit();
}

require_once '../auth/jwt.php';
require_once '../auth/authentication.php';
require_once '../auth/permissions.php';
require_once '../auth/validation.php';
require_once "$root/functions/user.php";

try {
    $data = json_decode(file_get_contents("php://input"), true);
} catch (Exception $e) {
    http_response_code(400);  # Bad Request
    die(json_encode(['message' => 'Corpo della richiesta malformato']));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (
        !isset($data['password'])
        || !isset($data['name'])
        || !isset($data['surname'])
        || !isset($data['email'])
        || !isset($data['password_confirm'])
    ) {

        http_response_code(400);  # Bad Request
        die(json_encode(['message' => 'Parametri richiesti mancanti', 'data' => $data]));
    }

    if (!is_valid_email($data['email'])) {
        http_response_code(422);  # Unprocessable entity
        die(json_encode(['message' => 'Email non valida']));
    }

    if (!is_valid_password($data['password'])) {
        http_response_code(422);  # Unprocessable entity
        die(json_encode(['message' => 'Password non valida']));
    }

    if ($data['password'] != $data['password_confirm']) {
        http_response_code(422);
        die(json_encode(['message' => 'Le password non combaciano']));
    }

    if (!isset($data['bio'])) {
        $data['bio'] = NULL;
    }

    try {
        $sql = "INSERT INTO PrgUsers (email, name, surname, password, bio)
            VALUES (:email, :name, :surname, :password, :bio)";
        $s = $pdo->prepare($sql);
        $success = $s->execute([
            'email' => $data['email'],
            'name' => $data['name'],
            'surname' => $data['surname'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'bio' => $data['bio']
        ]);
    } catch (PDOException $e) {
        http_response_code(500);  # Internal Server Error
        die(json_encode(['message' => 'Utente non creato']));
    }

    if (!$success) {
        http_response_code(500);  # Internal Server Error
        die(json_encode(['message' => 'Utente non creato']));
    }

    http_response_code(201);  # Created
    echo (json_encode(['message' => 'Utente creato con successo']));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $user_id = null;

    if (Auth\has_valid_user()) {
        $user_id = Auth\get_jwt()->payload['user_id'];
    }

    if (isset($_GET['id_utente'])) {
        $user_id = $_GET['id_utente'];
    }

    if ($user_id === null) {
        http_response_code(400);  # Bad Request
        die(json_encode(['message' => 'È necessario essere registrati o richiedere un account specifico']));
    }

    try {
        $user_data = User\fetch($user_id)
            ->respond_if_error()
            ->data;

        $num_proj = User\project_count($user_id)
            ->respond_if_error()
            ->data;

        $num_proj = $num_proj == false
            ? ['user_id' => $user_id, 'project_count' => 0]
            : $num_proj;

        $projects = User\projects($user_id)
            ->respond_if_error()
            ->data;
    } catch (PDOException $e) {
        http_response_code(500);  # Internal Server Error
        die(json_encode(['message' => 'Ricerca fallita']));
    }

    $output = [
        'message' => 'Risultati della ricerca',
        'user_data' => array_merge($user_data, ['project_count' => $num_proj['project_count']]),
        'projects' => $projects
    ];

    if (Auth\has_valid_user()) {
        $output = array_merge($output, ["jwt" => Auth\get_jwt()->refresh()->to_string()]);
    }

    http_response_code(200);  # OK
    echo json_encode($output);
    exit();
}

if (!Auth\has_valid_user()) {
    http_response_code(401);  # Unauthorized
    die(json_encode(['message' => 'Token di autorizzazione non valido o mancante']));
}

$user_id = Auth\get_jwt()->payload['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    global $PERMISSION_ADMIN;

    if (!user_has_permissions(PERMISSION_ADMIN)) {
        http_response_code(403);  # Forbidden
        die(json_encode(['message' => 'Non hai i permessi per eseguire questa operazione']));
    }

    if (!isset($data['password']) && !isset($data['bio'])) {
        http_response_code(422);  # Unprocessable Content
        die(json_encode(['message' => 'Sono necessari dei campi da modificare']));
    }

    $items = [];
    $params = [];

    if (isset($data['password'])) {
        $items[] = 'password = :password';
        $params['password'] = password_hash($data['password'], PASSWORD_ARGON2ID);
    }

    if (isset($data['bio'])) {
        $items[] = 'bio = :bio';
        $params['bio'] = $data['bio'];
    }

    $setClause = implode(', ', $items);

    try {
        $sql = "UPDATE PrgUsers SET $setClause WHERE user_id = :user_id";
        $s = $pdo->prepare($sql);
        $success = $s->execute(array_merge(['user_id' => $user_id], $params));
    } catch (PDOException $e) {
        http_response_code(500);  # Internal Server Error
        die(json_encode(['message' => 'Modifica fallita']));
    }

    http_response_code(200);  # OK
    die(json_encode(['message' => 'Modifica avvenuta con successo', 'jwt' => Auth\get_jwt()->refresh()->to_string()]));
}

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    global $PERMISSION_ADMIN;

    if (!user_has_permissions($PERMISSION_ADMIN)) {
        http_response_code(403);  # Forbidden
        die(json_encode(['message' => 'Non hai i permessi per eseguire questa operazione']));
    }

    try {
        $sql = "DELETE FROM PrgUsers WHERE user_id = :id";
        $s = $pdo->prepare($sql);
        $success = $s->execute(['id' => $user_id]);
    } catch (PDOException $e) {
        http_response_code(500);  # Internal Server Error
        die(json_encode(['message' => 'Eliminazione fallita']));
    }

    http_response_code(200);  # OK
    echo (json_encode(['message' => 'Utente eliminato con successo']));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
    http_response_code(204);  # No Content
    exit();
}

UNSUPPORTED_METHOD->api_response();