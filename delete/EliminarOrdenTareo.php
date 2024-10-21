<?php
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/OrdenesData.php";

    $datos = array('res'=>false, 'msg'=>'Error general.');

    try {        
        if(empty($_SESSION['CliId']) || empty($_SESSION['UserName']) || empty($_SESSION['UserNombre'])){throw new Exception("Se ha perdido la conexión.");}
        if(empty($_SESSION['RolMan'])){throw new Exception("Usuario no autorizado.");}//1:tecnico, 2:supervisor, 3:analista, 4 gerente de area, 5:gerente general.
        if(empty($_POST['id']) || empty($_POST['ordid'])){throw new Exception("La información esta incompleta.");}

        $USUARIO=date('Ymd-His').' ('.$_SESSION['UserName'].')';

        $orden=array(
            'id'=>$_POST['ordid'],
            'cliid'=>$_SESSION['CliId'],
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