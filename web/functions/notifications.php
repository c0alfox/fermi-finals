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
            WHERE user_id = :id
            ORDER BY notification_datetime DESC");
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

function is_own($notif_id, $user_id, $suppose_user_exists = true): Response {
    try {
        $pdo = connect();
        $s = $pdo->prepare("SELECT *
            FROM PrgNotifications
            WHERE notification_id = :id");
        $s->execute(['id' => $notif_id]);
        $data = $s->fetch(PDO::FETCH_ASSOC);
        $pdo = null;

        if (!$s->rowCount()) {
            return new Response(404, 'Notifica non trovata');
        }

        if (!$suppose_user_exists && !User\exists($user_id)) {
            return new Response(422, 'Utente inesistente');
        }

    } catch(\PDOException $e) {
        return new Response(500, 'Errore nella ricerca');
    }

    return new Response(
        200,
        'Risultato della ricerca',
        $data['user_id'] == $user_id
    );
}

function exists($notif_id): Response {
    try {
        $pdo = connect();
        $s = $pdo->prepare("SELECT 1
            FROM PrgNotifications
            WHERE notification_id = :id");
        $s->execute(['id' => $notif_id]);
        $pdo = null;

        if (!$s->rowCount()) {
            return new Response(404, 'Notifica non trovata', false);
        }
    } catch(\PDOException $e) {
        return new Response(500, 'Errore nella ricerca');
    }

    return new Response(
        200,
        'Notifica esistente',
        true
    );
}

function delete($notif_id, $suppose_notif_exists = true): Response {
    if (!$suppose_notif_exists) {
        $exists = exists($notif_id);
        if ($exists->response_code == 500) {
            return $exists;
        }

        if (!$exists->data) {
            return new Response(422, 'Notifica inesistente');
        }
    }

    try {
        $pdo = connect();
        $s = $pdo->prepare("DELETE
            FROM PrgNotifications
            WHERE notification_id = :id");
        $s->execute(['id' => $notif_id]);
        $data = $s->fetch(PDO::FETCH_ASSOC);
        $pdo = null;
    } catch(\PDOException $e) {
        return new Response(500, "Errore nell'eliminazione");
    }

    return new Response(
        200,
        'Notifica eliminata con successo',
    );
}

function create(string $title, string|null $desc, string|null $link, int $id) {
    try {
        $pdo = connect();
        $pdo->beginTransaction();

        $sql = "INSERT INTO PrgNotifications (title, description, action_link, user_id) 
            VALUES (:title, :description, :link, :id)";
        $s = $pdo->prepare($sql);
        $s->execute([
            'title' => $title,
            'description' => $desc,
            'link' => $link,
            'id' => $id,
        ]);

        $lastNotifId = $pdo->lastInsertId();
        $sql = "SELECT * FROM PrgNotifications WHERE notification_id = :id";
        $s = $pdo->prepare($sql);
        $s->execute(['id' => $lastNotifId]);
        $notif = $s->fetch(PDO::FETCH_ASSOC);

        $pdo->commit();
    } catch (\PDOException $e) {
        $pdo->rollBack();
        return new Response(500, 'Notifica non creata');
    }

    return new Response(201, 'Notifica creata con successo', $notif);
}