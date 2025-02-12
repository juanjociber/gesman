<?php
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SistemasData.php";

    $datos=array('res'=>false, 'id'=>0, 'msg'=>'Error General.');

    $input=file_get_contents('php://input');
	$json=json_decode($input, true);

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(!FnValidarSesion()){throw new Exception("Se ha perdido la conexión.");}
        if(!FnValidarSesionManNivel3()){throw new Exception("Usuario no autorizado.");}
        if(empty($json['nombre'])){throw new Exception("La información esta incompleta.");}
        if(FnValidarSistemaDuplicado($conmy, array('id'=>0, 'cliid'=>$_SESSION['gesman']['CliId'], 'nombre'=>$json['nombre']))>0){throw new Exception("El Sistema ya existe.");};

        $sistema=array(
            'cliid'=>$_SESSION['gesman']['CliId'],
            'nombre'=>$json['nombre'],
            'usuario'=>date('Ymd-His').' ('.$_SESSION['gesman']['Nombre'].')'
        );

        $id=FnAgregarSistema($conmy, $sistema);
        if(empty($id)){throw new Exception("Error agregando el Sistema.");}

        $datos['id']=$id;
        $datos['res']=true;
        $datos['msg']='Se agregó el Sistema.';

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