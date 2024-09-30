<?php
    session_start();
	require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGesmanDb.php';

	$Bandera = false;
	if(isset($_SESSION['CliId'])){
		$Bandera = true;
	}

	$data['data'] = array();
	$data['res'] = false;
	$data['pag'] = 0;
	$data['msg'] = 'Error del sistema.';

	if($Bandera == true && $_SERVER['REQUEST_METHOD'] === 'POST'){
		$pagina = 0;
		$query = "";

		if(!empty($_POST['pagina'])){
			$pagina = (int)$_POST['pagina'];
		}
		
		if(!empty($_POST['orden'])){
			$query = " and ot='".$_POST['orden']."'";
		}else{
			if(!empty($_POST['equipo'])){
				$query .=" and idactivo=".$_POST['equipo'];
			}

			if(!empty($_POST['fechainicial']) && !empty($_POST['fechafinal'])){
				$query .= " and fechainicial between '".$_POST['fechainicial']."' and '".$_POST['fechafinal']."'";
			}
		}
		
		try{
			$conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt=$conmy->prepare("select idot, ot, activo, tipoot, fechainicial, actividad, estado from man_ots where idcliente=:IdCliente".$query." limit :Pagina, 20;");
			$stmt->bindParam(':IdCliente', $_SESSION['CliId'], PDO::PARAM_INT); //$stmt->bindParam(':IdCliente', $_POST['empresa'], PDO::PARAM_INT);
			$stmt->bindParam(':Pagina', $pagina, PDO::PARAM_INT);
			$stmt->execute();
			$n=$stmt->rowCount();		
			if($n>0){
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
					$data['data'][] = array(
						'id' => $row['idot'],
                        'ot' => $row['ot'],
                        'activo' => $row['activo'],
						'tipoot' => $row['tipoot'],
						'fecha' => $row['fechainicial'],
						'actividad' => $row['actividad'],
						'estado' => (int)$row['estado']
					);
				}
				$data['res'] = true;
				$data['msg'] = 'Ok.';
				$data['pag'] = $n;
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