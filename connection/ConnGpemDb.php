<?php
//MySQL PDO
$hostname = 'localhost';
$database = 'gpemsac';
$username = 'gpemsac';
$password='gpemsac$';

try {
	$conmy = new PDO('mysql:host='.$hostname.';dbname='.$database, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	//echo 'Conectado a '.$con->getAttribute(PDO::ATTR_CONNECTION_STATUS);
}
catch (PDOException $ex) {
	echo 'Error de conexión. '.$ex->getMessage();
}
?>