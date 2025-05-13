<?php
namespace Suggestions;

require_once 'functions_prelude.php';

use \Response;

function projects(string|null $query = null, int $limit = 3): Response {
    try {
        $pdo = connect();

        $query = $query === null
            ? "%"
            : "%$query%";
        $query = mb_strtolower($query);

        $s = $pdo->prepare('SELECT project_id, name, surname, email, title, abstract, project_datetime, COUNT(revision_id) AS revision_count
            FROM PrgProjects 
            JOIN PrgUsers USING(user_id)
            JOIN PrgRevisions USING (project_id)
            WHERE LOWER(title) LIKE :query
            GROUP BY project_id
            ORDER BY project_datetime DESC
            LIMIT :lim');
        $s->bindParam('query', $query, \PDO::PARAM_STR);
        $s->bindParam('lim', $limit, \PDO::PARAM_INT);
        $s->execute();

        $proj_data = $s->fetchAll(\PDO::FETCH_ASSOC);

    } catch (\PDOException $e) {
        return new Response(500, 'Ricerca fallita', [
            'count' => 0,
            'content' => []
        ]);
    }

    return new Response(200, 'Risultati della ricerca', [
        'count' => $s->rowCount(),
        'content' => $proj_data
    ]);
}

function users(string|null $query = null, int $limit = 3): Response {
    try {
        $pdo = connect();

        $query = $query === null
            ? "%"
            : "%$query%";
        $query = mb_strtolower($query);

        $s = $pdo->prepare("SELECT user_id, name, surname, user_datetime, bio
            FROM PrgUsers
            WHERE LOWER(CONCAT(name, ' ', surname)) LIKE :query
            ORDER BY user_datetime DESC
            LIMIT :lim");
        $s->bindParam('query', $query, \PDO::PARAM_STR);
        $s->bindParam('lim', $limit, \PDO::PARAM_INT);
        $s->execute();

        $data = $s->fetchAll(\PDO::FETCH_ASSOC);

    } catch (\PDOException $e) {
        return new Response(500, 'Ricerca fallita', [
            'count' => 0,
            'content' => []
        ]);
    }

    return new Response(200, 'Risultati della ricerca', [
        'count' => $s->rowCount(),
        'content' => $data
    ]);
}