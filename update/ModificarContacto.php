<?php
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/ContactosData.php";

    $datos=array('res'=>false, 'id'=>0, 'msg'=>'Error General.');

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(!FnValidarSesion()){throw new Exception("Se ha perdido la conexión.");}
        if(!FnValidarSesionManNivel3()){throw new Exception("Usuario no autorizado.");}
        if(empty($_POST['id']) || empty($_POST['nombre']) || empty($_POST['estado'])){throw new Exception("La información esta incompleta.");}

        $contacto=array(
            'id'=>$_POST['id'],
            'cliid'=>$_SESSION['gesman']['CliId'],
            'nombre'=>$_POST['nombre'],
            'estado'=>$_POST['estado'],
            'usuario'=>date('Ymd-His').' ('.$_SESSION['gesman']['Nombre'].')'
        );

        $id=FnModificarContacto($conmy, $contacto);
        if(empty($id)){throw new Exception("Error modificando el Contacto.");}

        $datos['id']=$id;
        $datos['res']=true;
        $datos['msg']='Se modificó el Contacto.';

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