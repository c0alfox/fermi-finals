<?php
if (@substr_compare($_SERVER['SERVER_NAME'], 'altervista.org', -strlen('altervista.org')) == 0) {
    $host='localhost';
    $dbname='my_giuseppepappalardo';
    $username='';
    $password='';
} else {
    $host = 'db';
    $dbname = 'database';
    $username = 'root';
    $password = 'db_Pwd@1';
}
