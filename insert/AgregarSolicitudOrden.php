<?php
    session_start();

    $datos=array('res'=>false, 'id'=>0, 'msg'=>'Error General.');

    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/OrdenesData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/solicitudes/data/SolicitudesData.php";

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(empty($_SESSION['CliId']) || empty($_SESSION['UserName']) || empty($_SESSION['UserNombre'])){throw new Exception("Se ha perdido la conexión.");}
        if(empty($_SESSION['RolMan'])){throw new Exception("Usuario no autorizado.");}//1:tecnico, 2:supervisor, 3:analista, 4 gerente de area, 5:gerente general.
        if(empty($_POST['solid']) || empty($_POST['tipid']) || empty($_POST['nombre']) || empty($_POST['tipnombre']) || empty($_POST['fecha']) || empty($_POST['actnombre'])){throw new Exception("La información esta incompleta.");}

        $orden=array();
        $solicitud=array();
        
        $solicitud=FnBuscarSolicitud($conmy, $_POST['solid'], $_SESSION['CliId']);
        if(empty($solicitud['id'])){throw new Exception("No se encontró la Solicitud.");}

        $orden['equid']=$solicitud['equid'];
        $orden['tipid']=$_POST['tipid'];
        $orden['sisid']=0;
        $orden['oriid']=0;
        $orden['actid']=0;
        $orden['cliid']=$_SESSION['CliId'];
        $orden['nombre']=$_POST['nombre'];
        $orden['equcodigo']=$solicitud['equcodigo'];
        $orden['tipnombre']=$_POST['tipnombre'];
        $orden['sisnombre']=null;
        $orden['orinombre']=null;
        $orden['fecha']=$_POST['fecha'];
        $orden['tiptrabajo']="TRABAJO_LIVIANO";
        $orden['actnombre']=$_POST['actnombre'];
        $orden['trabajos']=null;
        $orden['observaciones']=null;
        $orden['equkm']=$solicitud['equkm'];
        $orden['equhm']=$solicitud['equhm'];
        $orden['supervisor']=$_SESSION['UserNombre'];
        $orden['clicontacto']=$solicitud['clicontacto'];
        $orden['usuario']=date('Ymd-His').' ('.$_SESSION['UserName'].')';

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