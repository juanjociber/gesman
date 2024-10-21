<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/mycloud/library/Odoo-REST-API-master/ripcord.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/gesman/connection/AwsXmlrpcOdoo.php');

	$res=false;
	$msg='Error general del Servidor.';

	$input=file_get_contents('php://input');
	$json=json_decode($input, true);
	//Registrar un Log
	//$nombreArchivo="../log.txt";
	//$archivo=fopen($nombreArchivo, "w");
	try {	
		if(empty($json)){ throw new Exception('No hay órdenes para confirmar.');}

		$models=ripcord::client($urlodoo."/xmlrpc/2/common");
		$uid=$models->authenticate($dbodoo, $userodoo, $pswodoo, array());
		$models=ripcord::client($urlodoo."/xmlrpc/2/object");
		$models->setSSLVerifyPeer(1);
		//$json=array(362,363,365);
		foreach ($json as $clave=>$valor) {
			$objXmlrpc=$models->execute_kw($dbodoo, $uid, $pswodoo,'sale.order', 'action_confirm', array($valor));
			//$resp=$models->execute_kw($dbodoo, $uid, $pswodoo,'sale.order', 'action_confirm',array((int)$_GET['idpedido']));
			//fwrite($archivo, date("Y-m-d H:i:s").": ".'IdSO:'.$valor.PHP_EOL);
		}
		//fclose($archivo);
		if ($objXmlrpc==1){
			$res=true;
			$msg='Se confirmó el Vale.';
		}elseif (is_array($objXmlrpc)){
			if (array_key_exists('faultCode', $objXmlrpc)) {
				$msg=$objXmlrpc["faultCode"].' => '.$objXmlrpc["faultString"];
			}
		} else{
			$msg='Respuesta desconocida del XMLRPC.';
		}
	} catch (Exception $e) {
		$msg=$e->getMessage();
	}

	echo json_encode(array('res'=>$res, 'msg'=>$msg));
?>