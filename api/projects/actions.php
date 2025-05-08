<?php
require_once '../prelude.php';
require_once "$root/functions/project.php";

set_headers('OPTIONS, POST, GET, PUT, DELETE');
handle_options_method();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $data = get_json_contents(['id'])
        ->respond_if_error()
        ->data;

    $project_id = $_GET['id'];

    Project\get($project_id)
        ->api_response();

    exit();
}

if (!Auth\has_valid_user()) {
    UNAUTHORIZED->api_response();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = get_json_contents(['title'])
        ->respond_if_error()
        ->data;

    if (!user_has_permissions(PERMISSION_READ)) {
        FORBIDDEN->api_response();
    }

    Project\create(
            Auth\get_user_id(),
            $data['title'],
            !empty($data['abstract']) ? $data['abstract'] : null
        )->api_response();

    exit();
}

/*
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    global $PERMISSION_EDIT;

    if (!permissions_check(authentication_get_permissions(), $PERMISSION_EDIT)) {
        http_response_code(403);  # Forbidden
        die(json_encode(['message' => 'Non hai i permessi per eseguire questa operazione']));
    }

    if (!isset($data['project_id'])) {
        http_response_code(400);  # Bad Request
        die(json_encode(['message' => 'È richiesto un id di progetto']));
    }

    if (!isset($data['title']) && !isset($data['abstract'])) {
        http_response_code(422);  # Unprocessable Content
        die(json_encode(['message' => 'Sono necessari dei campi da modificare']));
    }

    $items = [];
    $params = [];

    if (isset($data['title'])) {
        $items[] = 'title = :title';
        $params['title'] = $data['title'];
    }

    if (isset($data['abstract'])) {
        $items[] = 'abstract = :abstract';
        $params['abstract'] = $data['abstract'];
    }

    $setClause = implode(', ', $items);

    try {
        $sql = "UPDATE PrgProjects SET $setClause WHERE project_id = :project_id";
        $s = $pdo->prepare($sql);
        $success = $s->execute(array_merge(['project_id' => $data['project_id']], $params));

        if (!$success) {
            http_response_code(500);  # Internal Server Error
            die(json_encode(['message' => 'Modifica fallita']));
        }

        if (!$s->rowCount()) {
            http_response_code(404);  # Not Found
            die(json_encode(['message' => 'Progetto non trovato o non modificato']));
        }
    } catch(PDOException $e) {
        http_response_code(500);  # Internal Server Error
        die(json_encode(['message' => 'Modifica fallita']));
    }

    http_response_code(200);  # OK
    die(json_encode(['message' => 'Modifica avvenuta con successo', 'jwt' => jwt_refresh(authentication_get_jwt())]));
}

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    global $PERMISSION_ADMIN;

    if (!permissions_check(authentication_get_permissions(), $PERMISSION_ADMIN)) {
        http_response_code(403);  # Forbidden
        die(json_encode(['message' => 'Non hai i permessi per eseguire questa operazione']));
    }

    if (!isset($data['id_progetto'])) {
        http_response_code(422);  # Unprocessable Content
        die(json_encode(['message' => 'È richiesto un id di progetto']));
    }

    try {
        $s = $pdo->prepare('DELETE FROM PrgProjects WHERE project_id = :id');
        $success = $s->execute(['id' => $data['project_id']]);

        if (!$success) {
            http_response_code(500);  # Internal Server Error
            die(json_encode(['message' => 'Errore durante la cancellazione del progetto']));
        }
    } catch (PDOException $e) {
        http_response_code(500);  # Internal Server Error
        die(json_encode(['message' => 'Eliminazione fallita']));
    }

    http_response_code(200);  # OK
    echo json_encode(['message' => 'Progetto eliminato con successo', 'jwt' => jwt_refresh(authentication_get_jwt())]);
    exit();
}


http_response_code(405);  # Unsupported method
die(json_encode(['message' => 'Metodo non supportato']));
*/