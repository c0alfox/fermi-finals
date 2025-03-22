<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: OPTIONS, HEAD, POST, GET, PUT, DELETE');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);  # No Content
    exit();
}

require_once('../utils/pdo.php');
require_once('../utils/jwt.php');
require_once('../utils/authentication.php');
require_once('../utils/authorization.php');
require_once('../utils/validation.php');

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

    if (authentication_has_valid_user()) {
        $user_id = authentication_get_jwt()['payload']['user_id'];
    }

    if (isset($_GET['id_utente'])) {
        $user_id = $_GET['id_utente'];
    }

    if (is_null($user_id)) {
        http_response_code(400);  # Bad Request
        die(json_encode(['message' => 'È necessario essere registrati o richiedere un account specifico']));
    }

    try {
        $s = $pdo->prepare('SELECT email, name, surname, user_datetime, bio
            FROM PrgUsers
            WHERE user_id = :id');
        $success = $s->execute(['id' => $user_id]);
        $user_data = $s->fetch(PDO::FETCH_ASSOC);

        if (!$s->rowCount()) {
            http_response_code(404);  # Not Found
            die(json_encode(['message' => 'Utente non trovato']));
        }

        $s = $pdo->prepare('SELECT user_id, COUNT(project_id) AS project_count
            FROM PrgUsers
            JOIN PrgProjects USING(user_id)
            WHERE user_id = :id
            GROUP BY user_id');
        $success &= $s->execute(['id' => $user_id]);
        $num_proj = $s->fetch(PDO::FETCH_ASSOC);
        $num_proj = $num_proj == false
            ? ['user_id' => $user_id, 'project_count' => 0]
            : $num_proj;

        $s = $pdo->prepare('SELECT project_id, title, abstract, project_datetime, COUNT(revision_id) AS revision_count
            FROM PrgProjects 
            JOIN PrgRevisions USING (project_id)
            WHERE user_id = :id
            GROUP BY project_id, title, abstract, project_datetime');
        $success &= $s->execute(['id' => $user_id]);

        if (!$success) {
            http_response_code(500);  # Internal Server Error
            die(json_encode(['message' => 'Ricerca fallita']));
        }

        $projects = $s->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        http_response_code(500);  # Internal Server Error
        die(json_encode(['message' => 'Ricerca fallita']));
    }

    http_response_code(200);  # OK
    echo (json_encode([
        'message' => 'Risultati della ricerca',
        'user_data' => array_merge($user_data, ['project_count' => $num_proj['project_count']]),
        'projects' => $projects,
        'jwt' => jwt_refresh(authentication_get_jwt())
    ]));
    exit();
}

if (!authentication_has_valid_user()) {
    http_response_code(401);  # Unauthorized
    die(json_encode(['message' => 'Token di autorizzazione non valido o mancante']));
}

$user_id = authentication_get_jwt()['payload']['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    global $PERMISSION_ADMIN;

    if (!permissions_check(authentication_get_permissions(), $PERMISSION_ADMIN)) {
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
        $params['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
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
    die(json_encode(['message' => 'Modifica avvenuta con successo', 'jwt' => jwt_refresh(authentication_get_jwt())]));

    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    global $PERMISSION_ADMIN;

    if (!permissions_check(authentication_get_permissions(), $PERMISSION_ADMIN)) {
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

http_response_code(405);  # Unsupported method
die(json_encode(['message' => 'Metodo non supportato']));
