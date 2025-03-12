<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: OPTIONS, POST, GET, PUT, DELETE');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);  # No Content
    exit();
}

require_once('../../utils/pdo.php');
require_once('../../utils/jwt.php');
require_once('../../utils/authentication.php');
require_once('../../utils/authorization.php');

try {
    $data = json_decode(file_get_contents("php://input"), true);
} catch (Exception $e) {
    http_response_code(400);  # Bad Request
    die(json_encode(['message' => 'Corpo della richiesta malformato']));
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    global $PERMISSION_READ;

    if (!isset($_GET['id_progetto'])) {
        http_response_code(400);  # Bad Request
        die(json_encode(['message' => 'È richiesto un id di progetto']));
    }

    $perms = $PERMISSION_READ;
    if (authentication_has_valid_user()) {
        $perms = authentication_get_permissions();
    }

    try {
        $s = $pdo->prepare('SELECT Nome, Cognome, Email, Titolo, Abstract, DataOraProgetto, COUNT(IDRevisione) AS NumRevisioni
            FROM PrgProgetti 
            JOIN PrgUtenti USING(IDUtente)
            JOIN PrgRevisioni USING (IDProgetto)
            WHERE IDProgetto = :id
            GROUP BY Nome, Cognome, Email, Titolo, Abstract, DataOraProgetto');
        $success = $s->execute(['id' => $_GET['id_progetto']]);

        if (!$s->rowCount()) {
            http_response_code(404);  # Not Found
            die(json_encode(['message' => 'Progetto non trovato']));
        }
        
        $proj_data = $s->fetch(PDO::FETCH_ASSOC);

        $s = $pdo->prepare('SELECT Numero, DataOraRevisione, DataInizio, DataFine
            FROM PrgRevisioni 
            WHERE IDProgetto = :id AND (IDPermessi & 0b00001) != 0');
        $success &= $s->execute(['id' => $_GET['id_progetto']]);

        if (!$success) {
            http_response_code(500);  # Internal Server Error
            die(json_encode(['message' => 'Ricerca fallita']));
        }

        $rev_data = $s->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        http_response_code(500);  # Internal Server Error
        die(json_encode(['message' => 'Ricerca fallita']));
    }

    http_response_code(
        $s->rowCount() == $proj_data['NumRevisioni']
        ? 200  # OK
        : 206  # Partial Content
    );

    echo (json_encode([
        'message' => 'Risultati della ricerca',
        'project_data' => $proj_data,
        'revisions' => $rev_data,
        'jwt' => jwt_refresh(authentication_get_jwt())
    ]));
    exit();
}

if (!authentication_has_valid_user()) {
    http_response_code(401);  # Unauthorized
    die(json_encode(['message' => 'Token di autorizzazione non valido o mancante']));
}

$user_id = authentication_get_jwt()['payload']['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    global $PERMISSION_EDIT;

    if (!permissions_check(authentication_get_permissions(), $PERMISSION_EDIT)) {
        http_response_code(403);  # Forbidden
        die(json_encode(['message' => 'Non hai i permessi per eseguire questa operazione']));
    }

    if (!isset($data['titolo'])) {
        http_response_code(422);  # Unprocessable Content
        die(json_encode(['message' => 'È richiesto un titolo per il progetto']));
    }

    if (!isset($data['abstract'])) {
        $data['abstract'] = null;
    }

    try {
        $pdo->beginTransaction();
        $s = $pdo->prepare("INSERT INTO PrgProgetti (Titolo, Abstract, IDUtente) 
            VALUES (:titolo, :abs, :id_utente);");
        $success = $s->execute([
            'titolo' => $data['titolo'],
            'abs' => $data['abstract'],
            'id_utente' => $user_id
        ]);

        $s = $pdo->prepare("INSERT INTO PrgRevisioni (Numero, IDProgetto)
            VALUES (1, (SELECT MAX(IDProgetto) AS MaxID FROM PrgProgetti));");
        $success &= $s->execute();

        if (!$success) {
            $pdo->rollBack();
            http_response_code(500);  # Internal Server Error
            die(json_encode(['message' => 'Creazione fallita']));
        }

        $pdo->commit();
    } catch(PDOException $e) {
        $pdo->rollBack();
        http_response_code(500);  # Internal Server Error
        die(json_encode(['message' => 'Creazione fallita']));
    }

    http_response_code(200);  # OK
    echo (json_encode(['message' => 'Creazione avvenuta con successo', 'jwt' => jwt_refresh(authentication_get_jwt())]));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    global $PERMISSION_EDIT;

    if (!permissions_check(authentication_get_permissions(), $PERMISSION_EDIT)) {
        http_response_code(403);  # Forbidden
        die(json_encode(['message' => 'Non hai i permessi per eseguire questa operazione']));
    }

    if (!isset($data['id_progetto'])) {
        http_response_code(400);  # Bad Request
        die(json_encode(['message' => 'È richiesto un id di progetto']));
    }

    if (!isset($data['titolo']) && !isset($data['abstract'])) {
        http_response_code(422);  # Unprocessable Content
        die(json_encode(['message' => 'Sono necessari dei campi da modificare']));
    }

    $items = [];
    $params = [];

    if (isset($data['titolo'])) {
        $items[] = 'Titolo = :titolo';
        $params['titolo'] = $data['titolo'];
    }

    if (isset($data['abstract'])) {
        $items[] = 'Abstract = :abstract';
        $params['abstract'] = $data['abstract'];
    }

    $setClause = implode(', ', $items);

    try {
        $sql = "UPDATE PrgProgetti SET $setClause WHERE IDProgetto = :id_progetto";
        $s = $pdo->prepare($sql);
        $success = $s->execute(array_merge(['id_progetto' => $data['id_progetto']], $params));

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
        $s = $pdo->prepare('DELETE FROM PrgProgetti WHERE IDProgetto = :id');
        $success = $s->execute(['id' => $data['id_progetto']]);

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
