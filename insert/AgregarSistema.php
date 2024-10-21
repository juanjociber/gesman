<?php
    session_start();

    $datos=array('res'=>false, 'id'=>0, 'msg'=>'Error General.');

    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SistemasData.php";

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(empty($_SESSION['CliId']) || empty($_SESSION['UserName']) || empty($_SESSION['RolMan'])){throw new Exception("Se ha perdido la conexión.");}
        if($_SESSION['RolMan']<3){throw new Exception("Usuario no autorizado.");}//1:tecnico, 2:supervisor, 3:analista, 4 gerente de area, 5:gerente general.
        if(empty($_POST['nombre'])){throw new Exception("La información esta incompleta.");}

        $sistema=array(
            'cliid'=>$_SESSION['CliId'],
            'nombre'=>$_POST['nombre'],
            'usuario'=>date('Ymd-His').' ('.$_SESSION['UserName'].')'
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