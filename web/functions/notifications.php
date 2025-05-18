<?php
namespace Notifications;

require_once "functions_prelude.php";
require_once "$root/functions/user.php";

use \Response;
use \User;
use \PDO;

function fetch($user_id, $suppose_user_exists = true): Response {
    try {
        $pdo = connect();
        $s = $pdo->prepare("SELECT *
            FROM PrgNotifications
            WHERE user_id = :id");
        $s->execute(['id' => $user_id]);
        $data = $s->fetchAll(PDO::FETCH_ASSOC);
        $pdo = null;

        if (!$s->rowCount()) {
            if (!$suppose_user_exists && !User\exists($user_id)) {
                return new Response(404, 'Utente non trovato');
            }

            return new Response(200, 'Ricerca avvenuta con successo', []);
        }
    } catch(\PDOException $e) {
        return new Response(500, 'Errore nella ricerca');
    }

    return new Response(200, 'Ricerca avvenuta con successo', $data);
}

function count($user_id, $suppose_user_exists = true): Response {
    try {
        $pdo = connect();
        $s = $pdo->prepare("SELECT COUNT(*) AS count_notifications
            FROM PrgNotifications
            WHERE user_id = :id");
        $s->execute(['id' => $user_id]);
        $data = $s->fetchColumn();
        $pdo = null;

        if (!$s->rowCount()) {
            if (!$suppose_user_exists && !User\exists($user_id)) {
                return new Response(404, 'Utente non trovato');
            }

            return new Response(200, 'Ricerca avvenuta con successo', 0);
        }
    } catch(\PDOException $e) {
        return new Response(500, 'Errore nella ricerca');
    }

    return new Response(200, 'Ricerca avvenuta con successo', $data);
}