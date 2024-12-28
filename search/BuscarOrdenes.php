<?php 
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/OrdenesData.php";

    $datos = array('data'=>array(), 'res'=>false, 'pag'=>0, 'msg'=>'Error general.');

    $input=file_get_contents('php://input');
	$json=json_decode($input, true);

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(!FnValidarSesion()){throw new Exception("Se ha perdido la conexión.");}
        if(!FnValidarSesionManNivel1()){throw new Exception("Usuario no autorizado.");}

        $orden=array(
            'cliid'=>$_SESSION['gesman']['CliId'],
            'equid'=>empty($json['equid'])?0:$json['equid'],
            'tipid'=>empty($json['tipid'])?0:$json['tipid'],
            'sisid'=>empty($json['sisid'])?0:$json['sisid'],
            'oriid'=>empty($json['oriid'])?0:$json['oriid'],
            'nombre'=>empty($json['nombre'])?null:$json['nombre'],
            'fechainicial'=>empty($json['fechainicial'])?null:$json['fechainicial'],
            'fechafinal'=>empty($json['fechafinal'])?null:$json['fechafinal'],
            'actnombre'=>empty($json['actividad'])?null:$json['actividad'],
            'estado'=>empty($json['estado'])?0:$json['estado'],
            'pagina'=>empty($json['pagina'])?0:$json['pagina']
        );

        $response=FnBuscarOrdenes($conmy, $orden);

        if ($response['pag']>0) {
            $datos['res'] = true;
            $datos['msg'] = 'Ok.';
            $datos['data'] = $response['data'];
            $datos['pag'] = $response['pag'];
        } else {
            $datos['msg'] = 'No se encontró resultados.';
        }
        $conmy = null;
    } catch(PDOException $ex) {
        $datos['msg'] = $ex->getMessage();
        $conmy = null;
    } catch (Exception $ex) {
        $datos['msg'] = $ex->getMessage();
        $conmy = null;
    }

    echo json_encode($datos);
?>