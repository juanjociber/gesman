<?php
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/ClientesData.php";
    
    $datos=array('res'=>false, 'id'=>0, 'msg'=>'Error General.');

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(!FnValidarSesion()){throw new Exception("Se ha perdido la conexión.");}
        if(!FnValidarSesionManNivel3()){throw new Exception("Usuario no autorizado.");}
        if(empty($_POST['ruc']) || empty($_POST['nombre']) || empty($_POST['alias'])){throw new Exception("La información esta incompleta.");}

        if(FnValidarClienteDuplicado($conmy, $_POST['ruc'])>0){throw new Exception("El Cliente ya esta registrado.");}

        $cliente=array(
            'odoid'=>empty($_POST['odoid'])?0:$_POST['odoid'],
            'almid'=>empty($_POST['almid'])?0:$_POST['almid'],
            'ruc'=>$_POST['ruc'],
            'nombre'=>$_POST['nombre'],
            'alias'=>$_POST['alias'],
            'direccion'=>empty($_POST['direccion'])?null:$_POST['direccion'],
            'usuario'=>date('Ymd-His').' ('.$_SESSION['gesman']['Nombre'].')'
        );

        $id=FnAgregarCliente($conmy, $cliente);
        if(empty($id)){throw new Exception("Error agregando el Sistema.");}

        $datos['id']=$id;
        $datos['res']=true;
        $datos['msg']='Se agregó el Cliente.';

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