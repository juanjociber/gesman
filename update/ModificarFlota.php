<?php
    session_start();

    $datos=array('res'=>false, 'id'=>0, 'msg'=>'Error General.');

    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/FlotasData.php";

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(empty($_SESSION['CliId']) || empty($_SESSION['UserName'])){throw new Exception("Se ha perdido la conexión.");}
        if(empty($_POST['id']) || empty($_POST['nombre']) || empty($_POST['estado'])){throw new Exception("La información esta incompleta.");}

        $flota=array(
            'id'=>$_POST['id'],
            'cliid'=>$_SESSION['CliId'],
            'nombre'=>$_POST['nombre'],
            'estado'=>$_POST['estado'],
            'usuario'=>date('Ymd-His').' ('.$_SESSION['UserName'].')'
        );

        $id=FnModificarFlota($conmy, $flota);
        if(empty($id)){throw new Exception("Error modificando la Flota.");}

        $datos['id']=$id;
        $datos['res']=true;
        $datos['msg']='Se modificó la Flota.';

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