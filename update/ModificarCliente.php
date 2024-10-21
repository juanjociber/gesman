<?php 
    session_start();

    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/ClientesData.php";

    $datos = array('res'=>false, 'msg'=>'Error general.');

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(empty($_SESSION['UserName']) || empty($_SESSION['RolMan'])){throw new Exception("Se ha perdido la conexión.");}
        if($_SESSION['RolMan']<3){throw new Exception("Usuario no autorizado.");}//1:tecnico, 2:supervisor, 3:analista, 4 gerente de area, 5:gerente general.
        if(empty($_POST['id']) || empty($_POST['nombre']) || empty($_POST['alias']) || empty($_POST['estado'])){throw new Exception("La información esta incompleta.");}

        $cliente=array(
            'id'=>$_POST['id'],
            'odoid'=>empty($_POST['odoid'])?0:$_POST['odoid'],
            'almid'=>empty($_POST['almid'])?0:$_POST['almid'],
            'nombre'=>$_POST['nombre'],
            'alias'=>$_POST['alias'],
            'direccion'=>empty($_POST['direccion'])?null:$_POST['direccion'],
            'estado'=>$_POST['estado'],
            'usuario'=>date('Ymd-His').' ('.$_SESSION['UserName'].')'
        );

        if(FnModificarCliente($conmy, $cliente)){
            $datos['res'] = true;
            $datos['msg'] = 'Se modificó el Cliente.';
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