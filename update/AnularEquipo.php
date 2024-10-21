<?php 
    session_start();

    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/EquiposData.php";

    $datos = array('res'=>false, 'msg'=>'Error general.');

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(empty($_SESSION['CliId'])){throw new Exception("Usuario no tiene Autorización.");}
        if(empty($_POST['id'])){throw new Exception("La información esta incompleta.");}

        $equipo=array(
            'id'=>$_POST['id'],
            'cliid'=>$_SESSION['CliId'],
            'usuario'=>date('Ymd-His').' ('.$_SESSION['UserName'].')'
        );

        if(FnAnularEquipo($conmy, $equipo)){
            $datos['res'] = true;
            $datos['msg'] = 'Se anuló el Equipo.';
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