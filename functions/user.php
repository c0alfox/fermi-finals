<?php
namespace User;

require_once 'functions_prelude.php';
use Response;

function exists($user_id): bool {
    try {
        $pdo = connect();
        $s = $pdo->prepare("SELECT 1 FROM PrgUsers WHERE user_id = :id");
        $s->execute(['id' => $user_id]);
        $pdo = null;

        if (!$s->rowCount()) {
            return false;
        }
    } catch(\PDOException $e) {
        echo "Errore nella ricerca";
        return false;
    }

    return true;
}

function fetch($user_id) {
    try {
        $pdo = connect();
        $s = $pdo->prepare("SELECT email, name, surname, user_datetime, bio
            FROM PrgUsers
            WHERE user_id = :id");
        $s->execute(['id' => $user_id]);
        $data = $s->fetch(\PDO::FETCH_ASSOC);
        $pdo = null;

        if (!$s->rowCount()) {
            return new Response(404, 'Utente non trovato');
        }
    } catch(\PDOException $e) {
        return new Response(500, 'Errore nella ricerca');
    }

    return new Response(200, 'Ricerca avvenuta con successo', $data);
}

function get_userstring($user_id) {
    try {
        $pdo = connect();
        $s = $pdo->prepare("SELECT name, surname
            FROM PrgUsers
            WHERE user_id = :id");
        $s->execute(['id' => $user_id]);
        $data = $s->fetch(\PDO::FETCH_ASSOC);
        $pdo = null;

        if (!$s->rowCount()) {
            return null;
        }
    } catch(\PDOException $e) {
        return null;
    }

    $name = $data['name'];
    $surn = $data['surname'];
    $surn_initial = mb_substr($surn, 0, 1, "utf-8");

    return "$name $surn_initial.";
}

function login($email, $password, int $permissions = 0b1): Response {
    try {
        $pdo = connect();
        $s = $pdo->prepare("SELECT user_id, password FROM PrgUsers WHERE email = :email");
        $s->execute(['email' => $email]);
        $user_row = $s->fetch(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
        return new Response(500, 'Autenticazione Fallita');
    }

    if (!$s->rowCount()) {
        return new Response(404, 'Utente inesistente');
    }

    if (!password_verify($password, $user_row['password'])) {
        return new Response(401, 'Password errata');
    }

    $permissions = max(0, $permissions);
    $permissions &= \Perms\get_all();
    
    $jwt = new \Jwt([],
        ['user_id' => $user_row['user_id'], 'permissions' => $permissions],
        true
    );
    $jwt->set_cookie();

    return new Response(200, 'Login effettuato con successo', [
        'expiry' => $jwt->get_expiry(),
        'jwt' => $jwt->to_string()
    ]);
}

function edit_bio($user_id, $bio) {
    try {
        $pdo = connect();
        $s = $pdo->prepare("UPDATE PrgUsers SET bio = :bio WHERE user_id = :user_id");
        $s->execute(['user_id' => $user_id, 'bio' => $bio]);
    } catch (\PDOException $e) {
        return new Response(500, 'Modifica fallita');
    }

    return new Response(200, 'Modifica avvenuta con successo');
}

function edit_password($user_id, $old_password, $new_password) {
    try {
        $pdo = connect();
        $s = $pdo->prepare("SELECT password FROM PrgUsers WHERE user_id = :user_id");
        $s->execute(['user_id' => $user_id]);
        $p_hash = $s->fetchColumn();

        if (!password_verify($old_password, $p_hash)) {
            return new Response(400, "La password vecchia è errata");
        }

        $s = $pdo->prepare("UPDATE PrgUsers SET password = :password WHERE user_id = :user_id");
        $s->execute([
            'user_id' => $user_id,
            'password' => password_hash($new_password, PASSWORD_ARGON2ID)
        ]);
    } catch (\PDOException $e) {
        return new Response(500, 'Modifica fallita');
    }

    return new Response(200, 'Modifica avvenuta con successo');
}

function create(string $email, string $name, string $surname, string $password, string|null $bio) {
    try {
        $pdo = connect();
        $sql = "INSERT INTO PrgUsers (email, name, surname, password, bio)
            VALUES (:email, :name, :surname, :password, :bio)";
        $s = $pdo->prepare($sql);
        $s->execute([
            'email' => $email,
            'name' => $name,
            'surname' => $surname,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'bio' => $bio
        ]);
    } catch (\PDOException $e) {
        return new Response(500, 'Utente non creato');
    }

    return new Response(201, 'Utente creato con successo');
}

function project_count($user_id, $suppose_user_exists = true) {
    try {
        $pdo = connect();
        $s = $pdo->prepare('SELECT user_id, COUNT(project_id) AS project_count
            FROM PrgUsers
            JOIN PrgProjects USING(user_id)
            WHERE user_id = :id
            GROUP BY user_id');
        $s->execute(['id' => $user_id]);
        $num_proj = $s->fetchColumn(1);
        $pdo = null;

        if (!$s->rowCount()) {
            if (!$suppose_user_exists && !exists($user_id)) {
                return new Response(404, 'Utente non trovato');
            }

            return new Response(200, '', 0);
        }
    } catch(\PDOException $e) {
        return new Response(500, 'Errore nella ricerca');
    }

    return new Response(200, '', $num_proj);
}

function projects($user_id, $suppose_user_exists = true): Response {
    try {
        $pdo = connect();
        $s = $pdo->prepare('SELECT project_id, title, abstract, project_datetime, COUNT(revision_id) AS revision_count
            FROM PrgProjects 
            JOIN PrgRevisions USING (project_id)
            WHERE user_id = :id
            GROUP BY project_id, title, abstract, project_datetime');
        $s->execute(['id' => $user_id]);
        $projects = $s->fetchAll(\PDO::FETCH_ASSOC);
        $pdo = null;

        if (!$s->rowCount()) {
            if (!$suppose_user_exists && !exists($user_id)) {
                return new Response(404, 'Utente non trovato');
            }
        }
    } catch(\PDOException $e) {
        return new Response(500, 'Errore nella ricerca');
    }

    return new Response(200, '', $projects);

}