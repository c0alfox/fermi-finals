<?php
namespace User;

use Response;

require_once 'api/utils/pdo.php';
require_once 'response.php';

function fetch($user_id) {
    global $pdo;

    try {
        $s = $pdo->prepare("SELECT email, name, surname, user_datetime, bio
            FROM PrgUsers
            WHERE user_id = :id");
        $s->execute(['id' => $user_id]);
        $data = $s->fetch(\PDO::FETCH_ASSOC);

        if (!$s->rowCount()) {
            return new Response(404, 'Utente non trovato');
        }
    } catch(\PDOException $e) {
        return new Response(500, 'Errore nella ricerca');
    }

    return new Response(200, 'Ricerca avvenuta con successo', $data);
}