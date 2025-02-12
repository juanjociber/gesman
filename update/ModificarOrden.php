<?php 
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/OrdenesData.php";

    $datos = array('res'=>false, 'msg'=>'Error general.');

    $input=file_get_contents('php://input');
	$json=json_decode($input, true);

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(!FnValidarSesion()){throw new Exception("Se ha perdido la conexión.");}
        if(!FnValidarSesionManNivel1()){throw new Exception("Usuario no autorizado.");}
        if(empty($json['id']) || empty($json['equid']) || empty($json['equnombre']) || empty($json['fecha']) || empty($json['actnombre'])){throw new Exception("La información esta incompleta.");}

        $orden=array(
            'id'=>$json['id'],
            'cliid'=>$_SESSION['gesman']['CliId'],
            'equid'=>$json['equid'],
            'famid'=>empty($json['famid'])?0:$json['famid'],
            'sisid'=>empty($json['sisid'])?0:$json['sisid'],
            'oriid'=>empty($json['oriid'])?0:$json['oriid'],
            'actid'=>empty($json['actid'])?0:$json['actid'],
            'equnombre'=>$json['equnombre'],
            'famnombre'=>empty($json['famnombre'])?null:$json['famnombre'],
            'sisnombre'=>empty($json['sisnombre'])?null:$json['sisnombre'],
            'orinombre'=>empty($json['orinombre'])?null:$json['orinombre'],
            'fecha'=>$json['fecha'],
            'actnombre'=>$json['actnombre'],
            'trabajos'=>empty($json['trabajos'])?null:$json['trabajos'],
            'observaciones'=>empty($json['observaciones'])?null:$json['observaciones'],
            'equkm'=>empty($json['equkm'])?0:$json['equkm'],
            'equhm'=>empty($json['equhm'])?0:$json['equhm'],
            'supervisor'=>empty($json['supervisor'])?null:$json['supervisor'],
            'clicontacto'=>empty($json['clicontacto'])?null:$json['clicontacto'],
            'usuario'=>date('Ymd-His').' ('.$_SESSION['gesman']['Nombre'].')'
        );

        if(FnModificarOrden($conmy, $orden)){
            $datos['res'] = true;
            $datos['msg'] = 'Se modificó la Orden de Trabajo.';
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