<?php 
    session_start();

    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/OrdenesData.php";

    $datos = array('data'=>array(), 'res'=>false, 'pag'=>0, 'msg'=>'Error general.');

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(empty($_SESSION['CliId'])){throw new Exception("Usuario no tiene Autorización.");}

        $orden=array(
            'cliid'=>$_SESSION['CliId'],
            'equid'=>empty($_POST['equid'])?0:$_POST['equid'],
            'sisid'=>empty($_POST['sisid'])?0:$_POST['sisid'],
            'oriid'=>empty($_POST['oriid'])?0:$_POST['oriid'],
            'nombre'=>empty($_POST['nombre'])?'':$_POST['nombre'],
            'fechainicial'=>empty($_POST['fechainicial'])?'':$_POST['fechainicial'],
            'fechafinal'=>empty($_POST['fechafinal'])?'':$_POST['fechafinal'],
            'actnombre'=>empty($_POST['actividad'])?'':$_POST['actividad'],
            'estado'=>empty($_POST['estado'])?0:$_POST['estado'],
            'pagina'=>empty($_POST['pagina'])?0:$_POST['pagina']
        );

        $response=FnBuscarOrdenes($conmy, $orden);

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