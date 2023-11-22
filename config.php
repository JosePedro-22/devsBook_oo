<?php 
session_start();

$base = 'http://locahost/devsbook_OO';

$db_name = 'devsBook';
$db_host = 'localhost';
$db_user = 'postgres';
$db_password = 'admin123';

$pdo = new PDO('mysql:dbname='.$db_name.';host='.$db_host, $db_user, $db_password);