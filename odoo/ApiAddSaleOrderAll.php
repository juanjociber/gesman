<?php
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    header('Access-Control-Allow-Methods: POST');

	require_once($_SERVER['DOCUMENT_ROOT'].'/mycloud/library/Odoo-REST-API-master/ripcord.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/gesman/connection/AwsXmlrpcOdoo.php');

	date_default_timezone_set('America/Lima');

	$res=false;
	$msg='Error general del Servidor.';
	$data=array();

	//Servidor AWS Producción.
	$input=file_get_contents('php://input');
	$json=json_decode($input, true);

	//Registro de un log
	//$nombreArchivo="../log.txt";
	//$archivo=fopen($nombreArchivo, "w");
	//fclose($archivo);

	/*
	$json['CliId']='70';
	$json['WhId']='1';
	$json['OtId']='388579';
	$json['OtNombre']='20230105';
	$json['OtTipo']='INTERNA';
	$json['Equipo']='11001';
	$json['Vale']='765';
	$json['Fecha']='2024-10-09';
	$json['Tecnico']='GIANMARCOS SUAÑA';
	$json['Usuario']='MANUEL SARABIA';
	$json['Productos'][]=array('IdOdoo'=>'7260', 'Cantidad'=>'1', 'IdLista'=>'2');
	$json['Productos'][]=array('IdOdoo'=>'5389', 'Cantidad'=>'3.2', 'IdLista'=>'2');
	$json['Productos'][]=array('IdOdoo'=>'7621', 'Cantidad'=>'3.3', 'IdLista'=>'2');
	$json['Productos'][]=array('IdOdoo'=>'5351', 'Cantidad'=>'3.4', 'IdLista'=>'2');
	*/

	try {

		if(empty($json['Productos'])){ throw new Exception('El Vale no tiene Productos.');}
		if(empty($json['CliId']) || empty($json['WhId']) || empty($json['OtNombre']) || empty($json['OtTipo']) || empty($json['Equipo']) || empty($json['Vale']) || empty($json['Fecha']) || empty($json['Tecnico']) || empty($json['Usuario'])){ throw new Exception('La información esta incompleta.'); }

		$Fecha = strtotime($json['Fecha']);
		//$FechaUTC = gmdate('Y-m-d H:i:s', $Fecha);
		$FechaUTC = gmdate('Y-m-d', $Fecha);

		$PriceList=array();

		foreach ($json['Productos'] as $clave1=>$valor1) {
			$PriceList[$valor1['IdLista']][]=array('ProId'=>$valor1['IdOdoo'], 'Cantidad'=>$valor1['Cantidad']);
		}

		$models=ripcord::client($urlodoo.'/xmlrpc/2/common');
		$uid=$models->authenticate($dbodoo, $userodoo, $pswodoo, array());
		$models=ripcord::client($urlodoo.'/xmlrpc/2/object');

		foreach($PriceList as $clave2=>$valor2){
			//fwrite($archivo, date("Y-m-d H:i:s").": ".'PriceListId:'.$clave2.', CliId:'.$json['CliId'].', OtId:'.$json['OtId'].', OtNombre:'.$json['OtNombre'].', OtTipo:'.$json['OtTipo'].', Equipo:'.$json['Equipo'].', Vale:'.$json['Vale'].', Tecnico:'.$json['Tecnico'].', Usuario:'.$json['Usuario'].PHP_EOL);
			$soid=$models->execute_kw($dbodoo, $uid, $pswodoo,
				'sale.order',
				'create',
				array(
					array(
						'partner_id'=>(int)$json['CliId'],
						'warehouse_id'=>(int)$json['WhId'],
						'pricelist_id'=>(int)$clave2,
						'ot_id'=>(int)$json['OtId'],
						'ot_tecnico'=>$json['Tecnico'],
						'ot_tipo'=>$json['OtTipo'],
						'ot_equipo'=>$json['Equipo'],
						'ot_numero'=>$json['OtNombre'],
						'ot_vale'=>$json['Vale'],
						'sales_quotation_date_gpem'=>$FechaUTC,
						'ot_usuario'=>$json['Usuario']
					)
				)
			);

			$data[]=$soid;

			foreach($valor2 as $clave3=>$valor3){
				//fwrite($archivo, date("Y-m-d H:i:s").": ".'-> ProId:'.$valor3['ProId'].', Cantidad:'.$valor3['Cantidad'].PHP_EOL);
				$proid=$models->execute_kw($dbodoo, $uid, $pswodoo,
					'sale.order.line',
					'create',
					array(
						array(
							'order_id'=>(int)$soid,
							'product_id'=>(int)$valor3['ProId'],
							'product_uom_qty'=>(double)$valor3['Cantidad']
						)
					)
				);
			}
		}
		//$data[]=111;
		//$data[]=222;
		//fclose($archivo);	
		$res=true;
		$msg='Se registró correctamente el Vale.';
	} catch (Exception $e) {
		$msg=$e->getMessage();
	}
	/*echo '<pre>';
	print_r($json);
	print_r($PriceList);
	echo '<pre>';*/
	echo json_encode(array('res'=>$res, 'msg'=>$msg, 'data'=>$data));
?>