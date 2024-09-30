<?php
    session_start();
	require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGesmanDb.php';

	$Bandera = false;
	if(isset($_SESSION['UserName'])){
		$Bandera = true;
	}

	$data['data'] = array();
	$data['res'] = false;
	$data['msg'] = 'Error del sistema.';

	$IdActividad=0;//0:Actividades no programadas
	$Estado=2;//2:Estado Activo

	if($Bandera == true && $_SERVER['REQUEST_METHOD'] === 'POST'){
		if(!empty($_POST['nombre'])){
			//$query = $_POST['nombre'];
			try{
				$conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$stmt=$conmy->prepare("select id, idodoo, idlista, codigo, nombre, cantidad, medida from tblproductosprogramados where idcliente=:IdCliente and idactividad=:IdActividad and estado=:Estado and concat(codigo, nombre) like :Nombre;");
				$stmt->execute(array(':IdCliente'=>$_SESSION['CliId'], ':IdActividad'=>$IdActividad, ':Estado'=>$Estado, ':Nombre'=>'%'.$_POST['nombre'].'%'));	
				if($stmt->rowCount()>0){
					while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
						$data['data'][] = array(
							'id' => $row['id'],
							'idodoo'=>$row['idodoo'],
							'idlista'=>$row['idlista'],
							'codigo'=>$row['codigo'],
							'text' => $row['nombre'],
							'cantidad'=>$row['cantidad'],
							'medida'=>$row['medida']
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
			$data['msg'] = 'Parámetros incompletos.';
		}
	}else{
		$data['msg'] = 'Usuario no autorizado.';
	}

	echo json_encode($data);
?>