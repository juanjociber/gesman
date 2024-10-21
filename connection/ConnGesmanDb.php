<?php
//$hostname = '192.168.40.8';
$hostname='localhost';
$database = 'bdgesman';
// $username = 'gpemsac';
// $password='gpemsac$';

$username = 'root';
$password='mysql';

try {
	$conmy = new PDO('mysql:host='.$hostname.';dbname='.$database, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	//echo 'Conectado a '.$con->getAttribute(PDO::ATTR_CONNECTION_STATUS);
}
catch (PDOException $ex) {
	echo 'Error de conexión. '.$ex->getMessage();
}
?>