<?php
    session_start();
	require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/AwsDbOdoo.php';

	$Bandera = false;
	if(isset($_SESSION['UserName'])){
		$Bandera = true;
	}

	$data['data'] = array();
	$data['res'] = false;
	$data['msg'] = 'Error del sistema.';

	if($Bandera == true && $_SERVER['REQUEST_METHOD'] === 'POST'){
		if(!empty($_POST['id'])){
			try{
				$conpg->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$stmt=$conpg->prepare("select d.id, p.default_code, t.name, d.product_uom_qty, u.name as medida, d.state from sale_order_line d inner join product_product p on d.product_id=p.id 
				inner join product_template t on p.product_tmpl_id=t.id inner join uom_uom u on d.product_uom=u.id where d.order_id=:OrderId;");
				$stmt->execute(array(':OrderId'=>$_POST['id']));	
				if($stmt->rowCount()>0){
					while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
						$data['data'][] = array(
							'proid' => $row['id'],
							'procodigo'=>$row['default_code'],
							'pronombre'=>$row['name'],
							'procantidad'=>$row['product_uom_qty'],
							'promedida' => $row['medida'],
							'proestado'=>$row['state']
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
			$data['msg'] = 'No se reconoce el Vale.';
		}
	}else{
		$data['msg'] = 'Usuario no autorizado.';
	}

	echo json_encode($data);
?>