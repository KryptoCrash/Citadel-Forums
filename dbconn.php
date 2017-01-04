<?php

$host = 'localhost';
$dbuser = 'root';
$dbpass = '';


try {
	$conn = new PDO('mysql:host='.$host.';', $dbuser, $dbpass);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) {
	echo 'Connection failed: ' . $e->getMessage();
	file_put_contents('connection.errors.txt', date('Y-m-d H:i:s')."> ".$e->getMessage().PHP_EOL,FILE_APPEND);
}

$conn->query("CREATE DATABASE IF NOT EXISTS forums");
$conn->query("CREATE DATABASE IF NOT EXISTS market");

$conn = null;

$host = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'forums';

try {
	$forumConn = new PDO('mysql:host=' . $host . ';dbname=' . $dbname, $dbuser, $dbpass);
	$forumConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) {
	echo 'Connection failed: ' . $e->getMessage();
	file_put_contents('connection.errors.txt', date('Y-m-d H:i:s')."> ".$e->getMessage().PHP_EOL,FILE_APPEND);
}

$dbname = 'market';

try {
	$marketConn = new PDO('mysql:host='.$host.';dbname='.$dbname, $dbuser, $dbpass);
	$marketConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$marketConn->exec("SET CHARACTER SET utf8");
}
catch(PDOException $e) {
	echo 'Connection failed: ' . $e->getMessage();
	file_put_contents('connection.errors.txt', date('Y-m-d H:i:s')."> ".$e->getMessage().PHP_EOL,FILE_APPEND);
}