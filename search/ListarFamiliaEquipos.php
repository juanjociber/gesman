<?php 
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/EquiposData.php";

    $datos = array('data'=>array(), 'res'=>false, 'msg'=>'Error general.');

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(!FnValidarSesion()){throw new Exception("Se ha perdido la conexión.");}
        if(!FnValidarSesionManNivel1()){throw new Exception("Usuario no autorizado.");}
       
        $search=array(
            'id'=>empty($_POST['id'])?0:$_POST['id'],//para validar que no se busque el mis equipo. debe ser otro.
            'cliid'=>$_SESSION['gesman']['CliId'],
            'famid'=>empty($_POST['famid'])?0:$_POST['famid'],
            'nombre'=>empty($_POST['nombre'])?'':$_POST['nombre']
        );

        $data=FnListarFamiliaEquipos($conmy, $search);
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