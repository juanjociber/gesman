<?php
    session_start();
	require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGesmanDb.php';

	$Bandera = false;
	if(isset($_SESSION['CliId'])){
		$Bandera = true;
	}

	$data['data'] = array();
	$data['res'] = true;
	$data['msg'] = 'Error del servidor.';

	if($Bandera==true && $_SERVER['REQUEST_METHOD']==='POST'){
		$query="";
		if(!empty($_POST['nombre'])){
			$query = $_POST['nombre'];
		}
		try{
			$conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt=$conmy->prepare("select idactivo, codigo from man_activos where idcliente=:IdCliente and estado=:Estado and codigo like :Nombre;");
			$stmt->execute(array(':IdCliente'=>$_SESSION['CliId'], ':Estado'=>2, ':Nombre'=>'%'.$query.'%'));		
			if($stmt->rowCount()>0){
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
					$data['data'][] = array(
						'id' => $row['idactivo'],
                        'text' => $row['codigo']
					);
				}
				$data['res'] = true;
				$data['msg'] = 'Ok.';
			}else{
				$data['msg'] = 'No se encontró resultados.';
			}
			$stmt = null;
		}catch(PDOException $e){
			$stmt = null;
			$data['msg'] = $e->getMessage();
		}
	}else{
		$data['msg'] = 'Usuario no autorizado.';
	}

	echo json_encode($data);
?>