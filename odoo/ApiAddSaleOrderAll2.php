<?php
	//header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Origin: https://intranet.gpemsac.com");
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    header('Access-Control-Allow-Methods: POST, OPTIONS');

	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
		http_response_code(200);
		exit;
	}

	require_once($_SERVER['DOCUMENT_ROOT'].'/mycloud/library/Odoo-REST-API-master/ripcord.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/gesman/connection/AwsXmlrpcOdoo.php');

	date_default_timezone_set('America/Lima');
	$datos=array('res'=>false, 'msg'=>'Error general.', 'data'=>array());

	$input=file_get_contents('php://input');
	$json=json_decode($input, true);

	try {
		if(empty($json['cliid']) || empty($json['almid']) || empty($json['ordnombre']) || empty($json['ordtipo']) || empty($json['equcodigo']) || empty($json['clivale']) || empty($json['clifecha']) || empty($json['tecnico']) || empty($json['usuario'])){ throw new Exception('La información esta incompleta.'); }
		if(empty($json['productos'])){ throw new Exception('El Vale no tiene productos.');}

		$Fecha = strtotime($json['clifecha']);
		$FechaUTC = gmdate('Y-m-d', $Fecha);

		$PriceList=array();
		foreach ($json['productos'] as $clave1=>$valor1) {
			$PriceList[$valor1['lisid']][]=array('odoid'=>$valor1['odoid'], 'cantidad'=>$valor1['cantidad']);
		}

		$models=ripcord::client($urlodoo.'/xmlrpc/2/common');
		$uid=$models->authenticate($dbodoo, $userodoo, $pswodoo, array());
		$models=ripcord::client($urlodoo.'/xmlrpc/2/object');

		foreach($PriceList as $clave2=>$valor2){
			$soid=$models->execute_kw($dbodoo, $uid, $pswodoo,
				'sale.order',
				'create',
				array(
					array(
						'partner_id'=>(int)$json['cliid'],
						'warehouse_id'=>(int)$json['almid'],
						'pricelist_id'=>(int)$clave2,
						'ot_id'=>(int)$json['ordid'],
						'ot_tecnico'=>$json['tecnico'],
						'ot_tipo'=>$json['ordtipo'],
						'ot_equipo'=>$json['equcodigo'],
						'ot_numero'=>$json['ordnombre'],
						'ot_vale'=>$json['clivale'],
						'sales_quotation_date_gpem'=>$FechaUTC,
						'ot_usuario'=>$json['usuario']
					)
				)
			);

			$data[]=$soid;

			foreach($valor2 as $clave3=>$valor3){
				$proid=$models->execute_kw($dbodoo, $uid, $pswodoo,
					'sale.order.line',
					'create',
					array(
						array(
							'order_id'=>(int)$soid,
							'product_id'=>(int)$valor3['odoid'],
							'product_uom_qty'=>(double)$valor3['cantidad']
						)
					)
				);
			}
		}

		$datos['res']=true;
		$datos['msg']='Se registró el Vale.';

	} catch (Exception $ex) {
		$datos['msg']=$ex->getMessage();
	}

	echo json_encode($datos);
?>