<?php 
    session_start();

    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/AwsDbOdoo.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/OdooData.php";

    $datos = array('data'=>array(), 'res'=>false, 'msg'=>'Error general.');

    try {
        $conpg->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(empty($_SESSION['CliId'])){throw new Exception("Usuario no tiene Autorización.");}
        if(empty($_POST['linid'])){throw new Exception("La información esta incompleta.");}

        $data=FnBuscarSaleOrderLinePicking($conpg, $_POST['linid']);

        if (count($data)>0) {
            $datos['res'] = true;
            $datos['msg'] = 'Ok.';
            $datos['data'] = $data;
        } else {
            $datos['msg'] = 'No se encontró resultados.';
        }
        $conpg = null;
    } catch(PDOException $ex) {
        $datos['msg'] = $ex->getMessage();
        $conpg = null;
    } catch (Exception $ex) {
        $datos['msg'] = $ex->getMessage();
        $conpg = null;
    }

    echo json_encode($datos);

?>