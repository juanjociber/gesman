<?php
    session_start();
	require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGpemDb.php';

	$Bandera = false;
	if(isset($_SESSION['UserName'])){
		$Bandera = true;
	}

	$data['data'] = array();
	$data['res'] = false;
	$data['msg'] = 'Error del sistema.';

	if($Bandera == true && $_SERVER['REQUEST_METHOD'] === 'POST'){
		if(!empty($_POST['nombre'])){
			$query = $_POST['nombre'];
			try{
				$conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$stmt=$conmy->prepare("select pers_codigo, concat(pers_apellidos,', ', pers_nombres) as nombre from tblpersonal where pers_estado=:Estado and concat(pers_apellidos, pers_nombres) like :Nombre;");
				$stmt->execute(array(':Estado'=>1, ':Nombre'=>'%'.$query.'%'));	
				if($stmt->rowCount()>0){
					while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
						$data['data'][] = array(
							'id' => $row['pers_codigo'],
							'text' => $row['nombre']
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