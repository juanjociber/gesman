<?php
    session_start();
	require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGesmanDb.php';

	$Bandera = false;
	if(isset($_SESSION['CliId'])){
		$Bandera = true;
	}

	$data['data'] = array();
	$data['res'] = false;
	$data['msg'] = 'Error del sistema.';

	if($Bandera == true && $_SERVER['REQUEST_METHOD'] === 'POST'){
		if(!empty($_POST['recurso'])){
			$query = '';
			switch ($_POST['recurso']) {
	
				case 'sistema':
					if(!empty($_POST['nombre'])){
						$query = " and sistema like '%".$_POST['nombre']."%'";
					}
					try{
						$conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
						$stmt=$conmy->prepare("select idsistema, sistema from man_sistemas where idcliente=:IdCliente".$query.";");
						$stmt->bindParam(':IdCliente', $_SESSION['CliId'], PDO::PARAM_INT);
						$stmt->execute();	
						if($stmt->rowCount()>0){
							while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
								$data['data'][] = array(
									'id' => $row['idsistema'],
									'nombre' => $row['sistema']
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
					break;
				
				case 'origen':
					if(!empty($_POST['nombre'])){
						$query = " and origen like '%".$_POST['nombre']."%'";
					}
					try{
						$conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
						$stmt=$conmy->prepare("select idorigen, origen from man_origenes where idcliente=:IdCliente".$query.";");
						$stmt->bindParam(':IdCliente', $_SESSION['CliId'], PDO::PARAM_INT);
						$stmt->execute();	
						if($stmt->rowCount()>0){
							while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
								$data['data'][] = array(
									'id' => $row['idorigen'],
									'nombre' => $row['origen']
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
					break;
	
				case 'supervisor':
					if(!empty($_POST['nombre'])){
						$query = " and nombre like '%".$_POST['nombre']."%'";
					}
					try{
						$conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
						$stmt=$conmy->prepare("select idusuario, nombre from sis_usuarios where estado=1".$query.";");
						$stmt->execute();	
						if($stmt->rowCount()>0){
							while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
								$data['data'][] = array(
									'id' => $row['idusuario'],
									'nombre' => $row['nombre']
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
					break;
	
				case 'contacto':
					if(!empty($_POST['nombre'])){
						$query = " and supervisor like '%".$_POST['nombre']."%'";
					}
					try{
						$conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
						$stmt=$conmy->prepare("select idsupervisor, supervisor from cli_supervisores where idcliente=:IdCliente".$query." and estado=2;");
						$stmt->bindParam(':IdCliente', $_SESSION['CliId'], PDO::PARAM_INT);
						$stmt->execute();	
						if($stmt->rowCount()>0){
							while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
								$data['data'][] = array(
									'id' => $row['idsupervisor'],
									'nombre' => $row['supervisor']
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
					break;
				
				default:
					$data['msg'] = 'No se reconoce el comando.';
					break;
			}
		}else{
			$data['msg'] = 'Parámetros incompletos.';
		}
	}else{
		$data['msg'] = 'Usuario no autorizado.';
	}

	echo json_encode($data);
?>