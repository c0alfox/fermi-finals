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
    if (!isset($data['password'])
        || !isset($data['nome'])
        || !isset($data['cognome'])
        || !isset($data['email'])
        || !isset($data['password_confirm'])) {
        
        http_response_code(400);  # Bad Request
        die(json_encode(['message' => 'Parametri richiesti mancanti']));
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
        $sql = "INSERT INTO PrgUtenti (Email, Nome, Cognome, Password, Bio)
            VALUES (:email, :nome, :cognome, :password, :bio)";
        $s = $pdo->prepare($sql);
        $success = $s->execute([
            'email' => $data['email'],
            'nome' => $data['nome'],
            'cognome' => $data['cognome'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'bio' => $data['bio']
        ]);
    } catch(PDOException $e) {
        http_response_code(500);  # Internal Server Error
        die(json_encode(['message' => 'Utente non creato']));
    }

    if (!$success) {
        http_response_code(500);  # Internal Server Error
        die(json_encode(['message' => 'Utente non creato']));
    }

    http_response_code(201);  # Created
    echo(json_encode(['message' => 'Utente creato con successo']));
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
        $s = $pdo->prepare('SELECT Email, Nome, Cognome, DataOraUtente, Bio
            FROM PrgUtenti
            WHERE IDUtente = :id');
        $success = $s->execute(['id' => $user_id]);
        $user_data = $s->fetch(PDO::FETCH_ASSOC);

        if (!$s->rowCount()) {
            http_response_code(404);  # Not Found
            die(json_encode(['message' => 'Utente non trovato']));
        }

        $s = $pdo->prepare('SELECT IDUtente, COUNT(IDProgetto) AS NumProgetti
            FROM PrgUtenti
            JOIN PrgProgetti USING(IDUtente)
            WHERE IDUtente = :id
            GROUP BY IDUtente');
        $success &= $s->execute(['id' => $user_id]);
        $num_proj = $s->fetch(PDO::FETCH_ASSOC);
        $num_proj = $num_proj == false 
            ? ['IDUtente' => $user_id, 'NumProgetti' => 0]
            : $num_proj;

        $s = $pdo->prepare('SELECT IDProgetto, Titolo, Abstract, DataOraProgetto, COUNT(IDRevisione) AS NumRevisioni
            FROM PrgProgetti 
            JOIN PrgRevisioni USING (IDProgetto)
            WHERE IDUtente = :id
            GROUP BY IDProgetto, Titolo, Abstract, DataOraProgetto');
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
        'user_data' => array_merge($user_data, ['NumProgetti' => $num_proj['NumProgetti']]),
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
        $items[] = 'Password = :password';
        $params['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    }

    if (isset($data['bio'])) {
        $items[] = 'Bio = :bio';
        $params['bio'] = $data['bio'];
    }

    $setClause = implode(', ', $items);

    try {
        $sql = "UPDATE PrgUtenti SET $setClause WHERE IDUtente = :id_utente";
        $s = $pdo->prepare($sql);
        $success = $s->execute(array_merge(['id_utente' => $user_id], $params));
    } catch(PDOException $e) {
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
        $sql = "DELETE FROM PrgUtenti WHERE IDUtente = :id_utente";
        $s = $pdo->prepare($sql);
        $success = $s->execute(['id_utente' => $user_id]);
    } catch(PDOException $e) {
        http_response_code(500);  # Internal Server Error
        die(json_encode(['message' => 'Eliminazione fallita']));
    }

    http_response_code(200);  # OK
    echo(json_encode(['message' => 'Utente eliminato con successo']));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
    http_response_code(204);  # No Content
    exit();
}

http_response_code(405);  # Unsupported method
die(json_encode(['message' => 'Metodo non supportato']));
