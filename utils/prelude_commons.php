<?php
$root = $_SERVER['DOCUMENT_ROOT'];

require_once 'constants.php';

function connect(): PDO {
    require_once 'pdo.php';
    return $pdo;
}