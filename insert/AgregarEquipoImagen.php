<?php
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/EquiposData.php";

    $datos = array('res'=>false, 'msg'=>'Error general.');

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(!FnValidarSesion()){throw new Exception("Se ha perdido la conexión.");}
        if(!FnValidarSesionManNivel3()){throw new Exception("Usuario no autorizado.");}
        if(empty($_POST['id']) || empty($_POST['imagen'])){throw new Exception("La información esta incompleta.");}

        $FileName='EQU_'.$_POST['id'].'_'.uniqid().'.jpeg';
        $FileEncoded=str_replace("data:image/jpeg;base64,", "", $_POST['imagen']);
        $FileDecoded=base64_decode($FileEncoded);
        file_put_contents($_SERVER['DOCUMENT_ROOT']."/mycloud/gesman/equipos/".$FileName, $FileDecoded);

        $equipo=array(
            'id'=>$_POST['id'],
            'cliid'=>$_SESSION['gesman']['CliId'],
            'imagen'=>$FileName,
            'usuario'=>date('Ymd-His').' ('.$_SESSION['gesman']['Nombre'].')'
        );
        
        if(FnAgregarEquipoImagen($conmy, $equipo)){
            $datos['res'] = true;
            $datos['msg'] = 'Se agregó la Imágen.';
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