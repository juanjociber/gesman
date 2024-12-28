<?php 
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/ProductosData.php";

    $datos = array('data'=>array(), 'res'=>false, 'msg'=>'Error general.');

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(!FnValidarSesion()){throw new Exception("Se ha perdido la conexión.");}
        if(!FnValidarSesionManNivel1()){throw new Exception("Usuario no autorizado.");}

        $producto=array(
            'cliid'=>$_SESSION['gesman']['CliId'],
            'actid'=>empty($_POST['actid'])?0:$_POST['actid'],
            'nombre'=>empty($_POST['nombre'])?'':$_POST['nombre']
        );

        $data=FnListarProductos($conmy, $producto);
        $datos['res']=true;
        $datos['msg']='Ok.';
        $datos['data']=$data;
        
        $conmy=null;
    } catch(PDOException $ex) {
        $datos['msg']=$ex->getMessage();
        $conmy=null;
    } catch (Exception $ex) {
        $datos['msg']=$ex->getMessage();
        $conmy=null;
    }

    echo json_encode($datos);

?>