<?php
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/FlotasData.php";

    $datos=array('res'=>false, 'id'=>0, 'msg'=>'Error General.');

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(!FnValidarSesion()){throw new Exception("Se ha perdido la conexión.");}
        if(!FnValidarSesionManNivel3()){throw new Exception("Usuario no autorizado.");}
        if(empty($_POST['nombre'])){throw new Exception("La información esta incompleta.");}

        $flota=array(
            'cliid'=>$_SESSION['gesman']['CliId'],
            'nombre'=>$_POST['nombre'],
            'usuario'=>date('Ymd-His').' ('.$_SESSION['gesman']['Nombre'].')'
        );

        $id=FnAgregarFlota($conmy, $flota);
        if(empty($id)){throw new Exception("Error agregando la Flota.");}

        $datos['id']=$id;
        $datos['res']=true;
        $datos['msg']='Se agregó la Flota.';

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