<?php
    session_start();

    $datos=array('res'=>false, 'id'=>0, 'msg'=>'Error General.');

    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/EquiposData.php";

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(empty($_SESSION['CliId']) || empty($_SESSION['UserName']) || empty($_SESSION['RolMan'])){throw new Exception("Se ha perdido la conexión.");}
        if($_SESSION['RolMan']<3){throw new Exception("Usuario no autorizado.");}//1:tecnico, 2:supervisor, 3:analista, 4 gerente de area, 5:gerente general.
        if(empty($_POST['codigo'])){throw new Exception("La información esta incompleta.");}

        if(FnValidarEquipoDuplicado($conmy, $_SESSION['CliId'], $_POST['codigo'])>0){throw new Exception("El equipo ya esta registrado.");}
        
        $equipo=array();
        $equipo['cliid']=$_SESSION['CliId'];
        $equipo['floid']=empty($_POST['floid'])?0:$_POST['floid'];
        $equipo['codigo']=$_POST['codigo'];
        $equipo['nombre']=empty($_POST['nombre'])?null:$_POST['nombre'];
        $equipo['flonombre']=empty($_POST['flonombre'])?null:$_POST['flonombre'];
        $equipo['marca']=empty($_POST['marca'])?null:$_POST['marca'];
        $equipo['modelo']=empty($_POST['modelo'])?null:$_POST['modelo'];
        $equipo['serie']=empty($_POST['serie'])?null:$_POST['serie'];
        $equipo['anio']=empty($_POST['anio'])?null:$_POST['anio'];;
        $equipo['fabricante']=empty($_POST['fabricante'])?null:$_POST['fabricante'];
        $equipo['procedencia']=empty($_POST['procedencia'])?null:$_POST['procedencia'];
        $equipo['datos']=empty($_POST['datos'])?null:$_POST['datos'];
        $equipo['ubicacion']=empty($_POST['ubicacion'])?null:$_POST['ubicacion'];
        $equipo['km']=empty($_POST['km'])?0:$_POST['km'];
        $equipo['hm']=empty($_POST['hm'])?0:$_POST['hm'];
        $equipo['motor']=empty($_POST['motor'])?null:$_POST['motor'];
        $equipo['transmision']=empty($_POST['transmision'])?null:$_POST['transmision'];
        $equipo['diferencial']=empty($_POST['diferencial'])?null:$_POST['diferencial'];
        $equipo['placa']=empty($_POST['placa'])?null:$_POST['placa'];
        $equipo['usuario']=date('Ymd-His').' ('.$_SESSION['UserName'].')';

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