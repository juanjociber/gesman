<?php
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/FamiliasData.php";

    $datos=array('res'=>false, 'msg'=>'Error General.');

    $input=file_get_contents('php://input');
	$json=json_decode($input, true);

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(!FnValidarSesion()){throw new Exception("Se ha perdido la conexión.");}
        if(!FnValidarSesionManNivel3()){throw new Exception("Usuario no autorizado.");}
        if(empty($json['id']) || empty($json['nombre']) || empty($json['estado'])){throw new Exception("La información esta incompleta.");}
        if(FnValidarFamiliaDuplicado($conmy, array('id'=>$json['id'], 'cliid'=>$_SESSION['gesman']['CliId'], 'ownid'=>$json['ownid'], 'nombre'=>$json['nombre']))>0){throw new Exception("Ya existe la Familia en este Nivel.");};

        $familia=array(
            'id'=>$json['id'],
            'cliid'=>$_SESSION['gesman']['CliId'],
            'ownid'=>empty($json['ownid'])?0:$json['ownid'],
            'nombre'=>$json['nombre'],
            'ruta'=>empty($json['ownruta'])?$json['nombre']:$json['ownruta'].'/'.$json['nombre'],
            'estado'=>$json['estado'],
            'usuario'=>date('Ymd-His').' ('.$_SESSION['gesman']['Nombre'].')'
        );

        if(FnModificarFamilia($conmy, $familia)){
            $datos['res'] = true;
            $datos['msg'] = 'Se modificó la Familia.';
        }else{
            $datos['msg'] = 'Error modificando la Familia.';
        }

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