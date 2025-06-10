<?php
$root = $_SERVER['DOCUMENT_ROOT'];

require_once 'constants.php';

function serve_json(): bool {
    return strtolower(getallheaders()['Content-Type']) === 'application/json';
}

function connect(): PDO|null {
    try {
        $host = getenv('DB_HOST');
        $dbname = getenv('DB_NAME');
        $username = getenv('DB_USER');
        $password = getenv('DB_PASSWORD');

        $pdo=new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        if (serve_json()) {
            global $root;
            require_once "$root/functions/response.php";
            (new Response(500, 'Errore nella connessione al database')
            )->api_response();
        } else {
            echo "<!-- " . $e->getMessage() . " -->";
            die("Errore nella connessione al database");
        }
        return null;
    }
}

function crop(string|null $str, int $len = 100): string|null {
    if ($str === null) {
        return $str;
    }

    $substr = mb_substr($str, 0, $len);
    return strlen($substr) == strlen($str) ? $str : "$substr...";
}

function first_line(string|null $str): string|null {
    if ($str === null) {
        return null;
    }

    $out = preg_split('#\r?\n#', $str, 2)[0];
    return strlen($out) == strlen($str) ? $str : "$out...";
}

function datetime_to_rust(string $target) {
    return str_replace(' ', 'T', $target);
}