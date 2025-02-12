<?php
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/OrdenesData.php";

    $datos=array('res'=>false, 'id'=>0, 'msg'=>'Error General.');

    $input=file_get_contents('php://input');
	$json=json_decode($input, true);

    try {        
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(!FnValidarSesion()){throw new Exception("Se ha perdido la conexión.");}
        if(!FnValidarSesionManNivel1()){throw new Exception("Usuario no autorizado.");}
        if(empty($json['tipid']) || empty($json['equid']) || empty($json['nombre']) || empty($json['equnombre']) || empty($json['tipnombre']) || empty($json['fecha']) || empty($json['actnombre'])){throw new Exception("La información esta incompleta.");}

        $orden=array();
        $orden['equid']=$json['equid'];
        $orden['tipid']=$json['tipid'];
        $orden['famid']=empty($json['famid'])?0:$json['famid'];
        $orden['sisid']=empty($json['sisid'])?0:$json['sisid'];
        $orden['oriid']=empty($json['oriid'])?0:$json['oriid'];
        $orden['actid']=empty($json['actid'])?0:$json['actid'];
        $orden['cliid']=$_SESSION['gesman']['CliId'];
        $orden['nombre']=$json['nombre'];
        $orden['equnombre']=$json['equnombre'];
        $orden['tipnombre']=$json['tipnombre'];
        $orden['famnombre']=empty($json['famnombre'])?null:$json['famnombre'];
        $orden['sisnombre']=empty($json['sisnombre'])?null:$json['sisnombre'];
        $orden['orinombre']=empty($json['orinombre'])?null:$json['orinombre'];
        $orden['fecha']=$json['fecha'];
        $orden['tiptrabajo']="TRABAJO_LIVIANO";
        $orden['actnombre']=$json['actnombre'];
        $orden['trabajos']=empty($json['trabajos'])?null:$json['trabajos'];
        $orden['observaciones']=empty($json['observaciones'])?null:$json['observaciones'];
        $orden['equkm']=empty($json['equkm'])?0:$json['equkm'];
        $orden['equhm']=empty($json['equhm'])?0:$json['equhm'];
        $orden['supervisor']=$_SESSION['gesman']['Alias'];
        $orden['clicontacto']=empty($json['clicontacto'])?null:$json['clicontacto'];
        $orden['usuario']=date('Ymd-His').' ('.$_SESSION['gesman']['Nombre'].')';

        $id=FnAgregarOrden($conmy, $orden);
        if(empty($id)){throw new Exception("Error generando la Órden.");}

        $datos['id']=$id;
        $datos['res']=true;
        $datos['msg']='Se generó la Orden.';

        $conmy=null;
    } catch(PDOException $ex){
        $datos['msg']=$ex->getMessage();
        $conmy=null;
    } catch (Exception $ex) {
        $datos['msg']=$ex->getMessage();
        $conmy=null;
    }

    echo json_encode($datos);
?>