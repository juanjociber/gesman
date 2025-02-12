<?php 
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/EquiposData.php";

    $datos = array('res'=>false, 'msg'=>'Error general.');

    $input=file_get_contents('php://input');
	$json=json_decode($input, true);

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(!FnValidarSesion()){throw new Exception("Se ha perdido la conexión.");}
        if(!FnValidarSesionManNivel3()){throw new Exception("Usuario no autorizado.");}
        if(!empty($json['famownid']) && empty($json['ownid'])){throw new Exception("Esta ruta debe tener un equipo padre.");}//si el padre de la familia seleccionada es mayor a cero y no tiene equipo padre seleccionado
        if(empty($json['id']) || empty($json['famid'])){throw new Exception("La información esta incompleta.");}

        $equipo=array(
            'id'=>$json['id'],
            'cliid'=>$_SESSION['gesman']['CliId'],            
            'famid'=>$json['famid'],
            'ownid'=>empty($json['ownid'])?0:$json['ownid'],
            'famnombre'=>empty($json['famnombre'])?null:$json['famnombre'],
            'usuario'=>date('Ymd-His').' ('.$_SESSION['gesman']['Nombre'].')'
        );

        if(FnModificarEquipoRuta($conmy, $equipo)){
            $datos['res'] = true;
            $datos['msg'] = 'Se modificó el Equipo.';
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