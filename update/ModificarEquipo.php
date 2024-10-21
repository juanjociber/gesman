<?php 
    session_start();

    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/EquiposData.php";

    $datos = array('res'=>false, 'msg'=>'Error general.');

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(empty($_SESSION['CliId']) || empty($_SESSION['UserName'])){throw new Exception("Usuario no tiene Autorización.");}
        if(empty($_POST['id'])){throw new Exception("La información esta incompleta.");}

        $equipo=array(
            'id'=>$_POST['id'],
            'cliid'=>$_SESSION['CliId'],
            'floid'=>empty($_POST['floid'])?0:$_POST['floid'],        
            'nombre'=>empty($_POST['nombre'])?null:$_POST['nombre'],
            'flonombre'=>empty($_POST['flonombre'])?null:$_POST['flonombre'],
            'marca'=>empty($_POST['marca'])?null:$_POST['marca'],
            'modelo'=>empty($_POST['modelo'])?null:$_POST['modelo'],
            'placa'=>empty($_POST['placa'])?null:$_POST['placa'],
            'serie'=>empty($_POST['serie'])?null:$_POST['serie'],
            'motor'=>empty($_POST['motor'])?null:$_POST['motor'],
            'transmision'=>empty($_POST['transmision'])?null:$_POST['transmision'],
            'diferencial'=>empty($_POST['diferencial'])?null:$_POST['diferencial'],
            'anio'=>empty($_POST['anio'])?null:$_POST['anio'],
            'fabricante'=>empty($_POST['fabricante'])?null:$_POST['fabricante'],
            'procedencia'=>empty($_POST['procedencia'])?null:$_POST['procedencia'],
            'ubicacion'=>empty($_POST['ubicacion'])?null:$_POST['ubicacion'],
            'datos'=>empty($_POST['datos'])?null:$_POST['datos'],
            'usuario'=>date('Ymd-His').' ('.$_SESSION['UserName'].')'
        );

        if(FnModificarEquipo($conmy, $equipo)){
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