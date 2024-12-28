<?php
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/OrdenesData.php";

    $datos=array('res'=>false, 'id'=>0, 'msg'=>'Error General.');

    try {        
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(!FnValidarSesion()){throw new Exception("Se ha perdido la conexión.");}
        if(!FnValidarSesionManNivel1()){throw new Exception("Usuario no autorizado.");}
        if(empty($_POST['tipid']) || empty($_POST['equid']) || empty($_POST['nombre']) || empty($_POST['equcodigo']) || empty($_POST['tipnombre']) || empty($_POST['fecha']) || empty($_POST['actnombre'])){throw new Exception("La información esta incompleta.");}

        $orden=array();
        $orden['equid']=$_POST['equid'];
        $orden['tipid']=$_POST['tipid'];
        $orden['sisid']=empty($_POST['sisid'])?0:$_POST['sisid'];
        $orden['oriid']=empty($_POST['oriid'])?0:$_POST['oriid'];
        $orden['actid']=empty($_POST['actid'])?0:$_POST['actid'];
        $orden['cliid']=$_SESSION['gesman']['CliId'];
        $orden['nombre']=$_POST['nombre'];
        $orden['equcodigo']=$_POST['equcodigo'];
        $orden['tipnombre']=$_POST['tipnombre'];
        $orden['sisnombre']=empty($_POST['sisnombre'])?null:$_POST['sisnombre'];
        $orden['orinombre']=empty($_POST['orinombre'])?null:$_POST['orinombre'];
        $orden['fecha']=$_POST['fecha'];
        $orden['tiptrabajo']="TRABAJO_LIVIANO";
        $orden['actnombre']=$_POST['actnombre'];
        $orden['trabajos']=empty($_POST['trabajos'])?null:$_POST['trabajos'];
        $orden['observaciones']=empty($_POST['observaciones'])?null:$_POST['observaciones'];
        $orden['equkm']=empty($_POST['equkm'])?0:$_POST['equkm'];
        $orden['equhm']=empty($_POST['equhm'])?0:$_POST['equhm'];
        $orden['supervisor']=$_SESSION['gesman']['Alias'];
        $orden['clicontacto']=empty($_POST['clicontacto'])?null:$_POST['clicontacto'];
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