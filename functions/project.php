<?php
namespace Project;

require_once 'prelude.php';

function get(int $project_id): \Response {
    $pdo = connect();
    try {
        $s = $pdo->prepare('SELECT name, surname, email, title, abstract, project_datetime, COUNT(revision_id) AS revision_count
            FROM PrgProjects 
            JOIN PrgUsers USING(user_id)
            JOIN PrgRevisions USING (project_id)
            WHERE project_id = :id
            GROUP BY name, surname, email, title, abstract, project_datetime');
        $s->execute(['id' => $project_id]);

        if (!$s->rowCount()) {
            return new \Response(404, 'Progetto non trovato');
        }
        
        $proj_data = $s->fetch(\PDO::FETCH_ASSOC);

        $s = $pdo->prepare('SELECT revision_number, revision_datetime, start_date, end_date
            FROM PrgRevisions 
            WHERE project_id = :id AND (permissions_id & 0b00001) != 0');
        $s->execute(['id' => $project_id]);

        $rev_data = $s->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        return new \Response(500, 'Ricerca fallita');
    }

    $resp_code = $s->rowCount() == $proj_data['revision_count']
        ? 200
        : 206;  # Partial Content

    return new \Response($resp_code, 'Risultati della ricerca', [
        'project_data' => $proj_data,
        'revisions' => $rev_data
    ]);
}

function create(int $uid, string $title, string|null $abstract): \Response {
    try {
        $pdo = connect();
        $pdo->beginTransaction();
        $s = $pdo->prepare("INSERT INTO PrgProjects (title, abstract, user_id) 
            VALUES (:titolo, :abs, :id_utente);");
        $s->execute([
            'titolo' => $title,
            'abs' => $abstract,
            'id_utente' => $uid
        ]);

        $s = $pdo->prepare("INSERT INTO PrgRevisions (revision_number, project_id)
            VALUES (1, (SELECT MAX(project_id) AS max_id FROM PrgProjects));");
        $s->execute();
        $pdo->commit();
    } catch(\PDOException $e) {
        $pdo->rollBack();
        return new \Response(500, 'Creazione fallita');
    }

    return new \Response(201, 'Creazione avvenuta con successo');
}