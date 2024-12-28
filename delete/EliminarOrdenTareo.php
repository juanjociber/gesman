<?php
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/OrdenesData.php";

    $datos = array('res'=>false, 'msg'=>'Error general.');

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(!FnValidarSesion()){throw new Exception("Se ha perdido la conexión.");}
        if(!FnValidarSesionManNivel1()){throw new Exception("Usuario no autorizado.");}
        if(empty($_POST['id']) || empty($_POST['ordid'])){throw new Exception("La información esta incompleta.");}

        $USUARIO=date('Ymd-His').' ('.$_SESSION['gesman']['Nombre'].')';

        $orden=array(
            'id'=>$_POST['ordid'],
            'cliid'=>$_SESSION['gesman']['CliId'],
            'usuario'=>$USUARIO
        );
        if(!FnModificarOrden2($conmy, $orden)){throw new Exception("No se pudo actualizar la Órden.");}

        $tareo=array(
            'id'=>$_POST['id'],
            'ordid'=>$_POST['ordid']
        );
        if(!FnEliminarOrdenTareo($conmy, $tareo)){throw new Exception("No se pudo eliminar el Tareo.");}

        $datos['res'] = true;
        $datos['msg'] = 'Se agregó el Tareo.';

        $conmy = null;
    } catch(PDOException $ex) {
        $conmy = null;
        $datos['msg'] = $ex->getMessage();
    } catch (Exception $ex) {
        $conmy = null;
        $datos['msg'] = $ex->getMessage();
    }
    echo json_encode($datos);
?>