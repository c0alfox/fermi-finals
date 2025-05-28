<?php
require_once 'prelude.php';
set_headers('OPTIONS, HEAD, POST, GET, PUT, DELETE');
handle_options_method();

require_once '../auth/jwt.php';
require_once '../auth/authentication.php';
require_once '../auth/permissions.php';
require_once '../auth/validation.php';
require_once "$root/functions/user.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = get_json_contents([
        'password',
        'name',
        'surname',
        'email',
        'password_confirm'
    ])->respond_if_error()->data;

    if ($data['password'] != $data['password_confirm']) {
        http_response_code(422);
        die(json_encode(['message' => 'Le password non combaciano']));
    }

    if (!is_valid_email($data['email'])) {
        http_response_code(422);  # Unprocessable entity
        die(json_encode(['message' => 'Email non valida']));
    }

    if (!is_valid_password($data['password'])) {
        http_response_code(422);  # Unprocessable entity
        die(json_encode(['message' => 'Password non valida']));
    }

    if (!isset($data['bio'])) {
        $data['bio'] = NULL;
    }

    User\create($data['email'], $data['name'], $data['surname'], $data['password'], $data['bio'])
        ->api_response();
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

        $projects = User\projects($user_id)
            ->respond_if_error()
            ->data;
    } catch (PDOException $e) {
        http_response_code(500);  # Internal Server Error
        die(json_encode(['message' => 'Ricerca fallita']));
    }

    $output = [
        'message' => 'Risultati della ricerca',
        'user_data' => array_merge($user_data, ['project_count' => $num_proj]),
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
    if (!user_has_permissions(PERMISSION_ADMIN)) {
        FORBIDDEN->api_response();
    }

    $data = get_json_contents([])
        ->respond_if_error()
        ->data;

    if (empty($data['password']) && !isset($data['bio'])) {
        (new Response(422, 'Sono necessari campi da modificare'))
            ->api_response();
    }

    if (isset($data['bio'])) {
        $resp = User\edit_bio($user_id, empty($data['bio']) ? null : $data['bio'])
            ->respond_if_error();
    }

    if (isset($data['password'])) {
        if (!isset($data['old_password'])) {
            (new Response(422, 'Per modificare la password è richiesta la password precendente'))
                ->api_response();
        }

        $resp = User\edit_password(
            $user_id,
            $data['old_password'],
            $data['password']
        )->respond_if_error();
    }

    $resp->api_response();
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