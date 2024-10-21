<?php
    session_start();
	require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGesmanDb.php';
	require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/EquiposData.php";

	$datos=array('res'=>false, 'msg'=>'Error General.', 'data'=>array());

	try{
		$conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		if(empty($_SESSION['CliId'])){throw new Exception("Se ha perdido la conexión.");}

		$nombre=empty($_POST['nombre']) ? "" : $_POST['nombre'];
		$data=FnListarClienteFlotas2($conmy, $_SESSION['CliId'], $nombre);
		
		$datos['res']=true;
		$datos['msg']='Ok.';
		$datos['data']=$data;

		$conmy=null;
	} catch(PDOException $ex){
		$datos['msg']=$ex->getMessage();
		$conmy=null;
	} catch (Exception $ex) {
		$datos['msg']=$ex->getMessage();
		$conmy=null;
	}

	echo json_encode($datos);
?>