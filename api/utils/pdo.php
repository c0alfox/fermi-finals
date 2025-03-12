<?php
require_once('connection.php');

try {
	$pdo=new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
	echo "<!-- " . $e->getMessage() . " -->";
	die("Errore nella connessione al database");
}
