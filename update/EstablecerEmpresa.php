<?php
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
    require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGesmanDb.php';
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/ClientesData.php";

    $datos=array('res'=>false, 'msg'=>'Error general.');

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(!FnValidarSesion()){throw new Exception("Se ha perdido la conexión.");}
        if(!FnValidarSesionManNivel1()){throw new Exception("Usuario no autorizado.");}
        if(empty($_POST['id'])){throw new Exception("La información esta incompleta.");}

        $response=FnBuscarCliente($conmy, $_POST['id']);
        if(empty($response)){throw new Exception("El Cliente no existe.");}
        if(!($response['estado']==2)){throw new Exception("El Cliente no esta disponible.");}

        $data=array(
            'cliid'=>$response['id'],
            'odoid'=>$response['odoid'],
            'almid'=>$response['almid'],
            'clinombre'=>$response['alias']
        );

        if(!FnModificarSesionCliente($data)){throw new Exception("No se pudo cambiar el Cliente.");}

        $datos['res'] = true;
        $datos['msg'] = 'Se hizo el cambio de Cliente';

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