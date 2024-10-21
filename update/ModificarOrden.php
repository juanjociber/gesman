<?php 
    session_start();

    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/OrdenesData.php";

    $datos = array('res'=>false, 'msg'=>'Error general.');

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(empty($_SESSION['CliId'])){throw new Exception("Usuario no tiene Autorización.");}
        if(empty($_POST['id']) || empty($_POST['fecha']) || empty($_POST['actividades'])){throw new Exception("La información esta incompleta.");}

        $orden=array(
            'id'=>$_POST['id'],
            'cliid'=>$_SESSION['CliId'],
            'sisid'=>empty($_POST['sisid'])?0:$_POST['sisid'],
            'oriid'=>empty($_POST['oriid'])?0:$_POST['oriid'],
            'actid'=>empty($_POST['actid'])?0:$_POST['actid'],
            'sisnombre'=>empty($_POST['sisnombre'])?null:$_POST['sisnombre'],
            'orinombre'=>empty($_POST['orinombre'])?null:$_POST['orinombre'],
            'fecha'=>$_POST['fecha'],
            'actividades'=>$_POST['actividades'],
            'trabajos'=>empty($_POST['trabajos'])?null:$_POST['trabajos'],
            'observaciones'=>empty($_POST['observaciones'])?null:$_POST['observaciones'],
            'equkm'=>empty($_POST['equkm'])?0:$_POST['equkm'],
            'equhm'=>empty($_POST['equhm'])?0:$_POST['equhm'],
            'supervisor'=>empty($_POST['supervisor'])?null:$_POST['supervisor'],
            'clicontacto'=>empty($_POST['clicontacto'])?null:$_POST['clicontacto'],
            'usuario'=>date('Ymd-His').' ('.$_SESSION['UserName'].')'
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