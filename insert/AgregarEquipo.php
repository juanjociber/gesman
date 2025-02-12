<?php
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/EquiposData.php";

    $datos=array('res'=>false, 'id'=>0, 'msg'=>'Error General.');

    $input=file_get_contents('php://input');
	$json=json_decode($input, true);

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(!FnValidarSesion()){throw new Exception("Se ha perdido la conexión.");}
        if(!FnValidarSesionManNivel3()){throw new Exception("Usuario no autorizado.");}
        if(empty($json['nombre']) || empty($json['famid']) || empty($json['famnombre'])){throw new Exception("La información esta incompleta.");}
        if(!empty($json['famownid']) && empty($json['ownid'])){throw new Exception("Esta familia debe tener un equipo padre.");}//si el padre de la familia seleccionada es mayor a cero y no tiene equipo padre seleccionado
        if(FnValidarEquipoDuplicado($conmy, array('id'=>0, 'cliid'=>$_SESSION['gesman']['CliId'], 'nombre'=>$json['nombre']))>0){throw new Exception("El equipo ya esta registrado.");}
        
        $equipo=array();
        $equipo['cliid']=$_SESSION['gesman']['CliId'];
        $equipo['ownid']=empty($json['ownid'])?0:$json['ownid']; 
        $equipo['nombre']=$json['nombre'];
        $equipo['marca']=empty($json['marca'])?null:$json['marca'];
        $equipo['modelo']=empty($json['modelo'])?null:$json['modelo'];
        $equipo['serie']=empty($json['serie'])?null:$json['serie'];
        $equipo['datos']=empty($json['datos'])?null:$json['datos'];
        $equipo['km']=empty($json['km'])?0:$json['km'];
        $equipo['hm']=empty($json['hm'])?0:$json['hm'];
        $equipo['placa']=empty($json['equplaca'])?null:$json['placa'];
        $equipo['usuario']=date('Ymd-His').' ('.$_SESSION['gesman']['Nombre'].')';
        $equipo['famid']=$json['famid'];
        $equipo['famnombre']=$json['famnombre'];

        $id=FnAgregarEquipo($conmy, $equipo);
        if(empty($id)){throw new Exception("Error agregando el Equipo.");}

        $datos['id']=$id;
        $datos['res']=true;
        $datos['msg']='Se agregó el Equipo.';

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