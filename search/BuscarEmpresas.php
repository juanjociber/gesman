<?php 
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/ClientesData.php";

    $datos = array('data'=>array(), 'res'=>false, 'pag'=>0, 'msg'=>'Error general.');

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(!FnValidarSesion()){throw new Exception("Se ha perdido la conexión.");}

        $search=array(
            'nombre'=>empty($_POST['nombre'])?'':$_POST['nombre'],
            'pagina'=>empty($_POST['pagina'])?0:$_POST['pagina']
        );

        $data=FnBuscarEmpresas($conmy, $search);

        if ($data['pag']>0) {
            $datos['res'] = true;
            $datos['msg'] = 'Ok.';
            $datos['data'] = $data['data'];
            $datos['pag'] = $data['pag'];
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