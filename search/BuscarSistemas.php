<?php 
    session_start();

    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SistemasData.php";

    $datos = array('data'=>array(), 'res'=>false, 'pag'=>0, 'msg'=>'Error general.');

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(empty($_SESSION['CliId'])){throw new Exception("Usuario no tiene Autorización.");}

        $sistema=array(
            'cliid'=>$_SESSION['CliId'],
            'nombre'=>empty($_POST['nombre'])?'':$_POST['nombre'],
            'estado'=>empty($_POST['estado'])?0:$_POST['estado'],
            'pagina'=>empty($_POST['pagina'])?0:$_POST['pagina']
        );

        $response=FnBuscarSistemas($conmy, $sistema);

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