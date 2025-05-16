<?php
require_once "../prelude.php";
require_once "$root/functions/suggestions.php";

use function Suggestions\projects;
use function Suggestions\users;

set_headers("GET, OPTIONS");
handle_options_method();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $q = $_GET['q'] ?? '';
    $q = trim($q);

    $projects = projects($q)
        ->respond_if_error()
        ->data;

    $users = users($q)
        ->respond_if_error()
        ->data;
    
    (new Response(200, '', [
        'projects' => $projects,
        'users' => $users,
    ]))->api_response();
}