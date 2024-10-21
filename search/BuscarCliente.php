<?php 
    session_start();

    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/ClientesData.php";

    $datos = array('data'=>array(), 'res'=>false, 'msg'=>'Error general.');

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(empty($_SESSION['UserName'])){throw new Exception("Se ha perdido la conexión.");}
        if(empty($_POST['id'])){throw new Exception("La información esta incompleta.");}

        $data=FnBuscarCliente($conmy, $_POST['id']);
        if(empty($data['id'])){throw new Exception("No se encontró resultados.");}

        $datos['res'] = true;
        $datos['msg'] = 'Ok.';
        $datos['data'] = $data;

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