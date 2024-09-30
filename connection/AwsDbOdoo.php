<?php
//Aws Producción
$user_pgsql = 'gpemuser';
$password_pgsql = 'GpemUser1*';
$database_pgsql = 'gpem';
$port_pgsql = 5432;
$host_pgsql = 'gpem.icreat.pe';

try {
	$conpg = new PDO("pgsql:host=".$host_pgsql.";port=".$port_pgsql.";dbname=".$database_pgsql.";options='-c client_encoding=utf8'",$user_pgsql,$password_pgsql);
	//$con = new PDO('pgsql:host='.$hostname.';port=5432;dbname='.$database, $username, $password);
	//echo 'Conectado a '.$conpg->getAttribute(PDO::ATTR_CONNECTION_STATUS);
}
catch (PDOException $ex) {
	echo 'Error conectando a la BBDD. '.$ex->getMessage(); 
	die();
	$conpg =null;
}
?>