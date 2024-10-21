<?php
    session_start();

    $datos=array('res'=>false, 'id'=>0, 'msg'=>'Error General.');

    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/ClientesData.php";

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(empty($_SESSION['UserName']) || empty($_SESSION['RolMan'])){throw new Exception("Se ha perdido la conexión.");}
        if($_SESSION['RolMan']<3){throw new Exception("Usuario no autorizado.");}//1:tecnico, 2:supervisor, 3:analista, 4 gerente de area, 5:gerente general.
        if(empty($_POST['ruc']) || empty($_POST['nombre']) || empty($_POST['alias'])){throw new Exception("La información esta incompleta.");}

        if(FnValidarClienteDuplicado($conmy, $_POST['ruc'])>0){throw new Exception("El Cliente ya esta registrado.");}

        $cliente=array(
            'odoid'=>empty($_POST['odoid'])?0:$_POST['odoid'],
            'almid'=>empty($_POST['almid'])?0:$_POST['almid'],
            'ruc'=>$_POST['ruc'],
            'nombre'=>$_POST['nombre'],
            'alias'=>$_POST['alias'],
            'direccion'=>empty($_POST['direccion'])?null:$_POST['direccion'],
            'usuario'=>date('Ymd-His').' ('.$_SESSION['UserName'].')'
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