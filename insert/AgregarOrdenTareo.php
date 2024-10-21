<?php
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/OrdenesData.php";

    $datos = array('res'=>false, 'msg'=>'Error general.');

    try {        
        if(empty($_SESSION['CliId']) || empty($_SESSION['UserName']) || empty($_SESSION['UserNombre'])){throw new Exception("Se ha perdido la conexión.");}
        if(empty($_SESSION['RolMan'])){throw new Exception("Usuario no autorizado.");}//1:tecnico, 2:supervisor, 3:analista, 4 gerente de area, 5:gerente general.
        if(empty($_POST['ordid']) || empty($_POST['perid']) || empty($_POST['pernombre']) || empty($_POST['ingreso']) || empty($_POST['salida'])){throw new Exception("La información esta incompleta.");}

        $USUARIO=date('Ymd-His').' ('.$_SESSION['UserName'].')';

        $orden=array(
            'id'=>$_POST['ordid'],
            'cliid'=>$_SESSION['CliId'],
            'usuario'=>$USUARIO
        );
        if(!FnModificarOrden2($conmy, $orden)){throw new Exception("No se pudo actualizar la Órden.");}

        $INGRESO=new DateTime($_POST['ingreso']);
        $SALIDA=new DateTime($_POST['salida']);
        $INTERVALO=$INGRESO->diff($SALIDA);
        $MINUTOS=$INTERVALO->days * 24 * 60 + $INTERVALO->h * 60 + $INTERVALO->i;

        $tareo=array(
            'ordid'=>$_POST['ordid'],
            'perid'=>$_POST['perid'],
            'pernombre'=>$_POST['pernombre'],
            'ingreso'=>$_POST['ingreso'],
            'salida'=>$_POST['salida'],
            'minutos'=>$MINUTOS,
            'estado'=>2,
            'usuario'=>$USUARIO
        );
        if(!FnAgregarOrdenTareo($conmy, $tareo)){throw new Exception("No se pudo agregar el Tareo.");}

        $datos['res'] = true;
        $datos['msg'] = 'Se agregó el Tareo.';

        $conmy = null;
    } catch(PDOException $ex) {
        $conmy = null;
        $datos['msg'] = $ex->getMessage();
    } catch (Exception $ex) {
        $conmy = null;
        $datos['msg'] = $ex->getMessage();
    }
    echo json_encode($datos);
?>