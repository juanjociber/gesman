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
        if(empty($_POST['ownid'])){throw new Exception("La información esta imcompleta.");}
        
        $search=array(
            'cliid'=>$_SESSION['gesman']['CliId'],
            'ownid'=>$_POST['ownid']
        );

        $data=FnBuscarEquipoComponentes($conmy, $search);
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