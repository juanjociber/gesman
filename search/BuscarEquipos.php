<?php 
    session_start();

    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/EquiposData.php";

    $datos = array('data'=>array(), 'res'=>false, 'pag'=>0, 'msg'=>'Error general.');

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(empty($_SESSION['CliId'])){throw new Exception("Usuario no tiene Autorización.");}

        $equipo=array(
            'cliid'=>$_SESSION['CliId'],
            'floid'=>empty($_POST['floid'])?0:$_POST['floid'],
            'nombre'=>empty($_POST['nombre'])?'':$_POST['nombre'],
            'estado'=>empty($_POST['estado'])?0:$_POST['estado'],
            'pagina'=>empty($_POST['pagina'])?0:$_POST['pagina']
        );

        $response=FnBuscarEquipos($conmy, $equipo);

        if ($response['pag']>0) {
            $datos['res'] = true;
            $datos['msg'] = 'Ok.';
            $datos['data'] = $response['data'];
            $datos['pag'] = $response['pag'];
        } else {
            $datos['msg'] = 'No se encontró resultados.';
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