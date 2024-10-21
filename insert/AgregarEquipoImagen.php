<?php
    session_start();
    
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/EquiposData.php";

    $datos = array('res'=>false, 'msg'=>'Error general.');

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(empty($_SESSION['CliId']) || empty($_SESSION['UserName']) || empty($_SESSION['RolMan'])){throw new Exception("Se ha perdido la conexión.");}
        if($_SESSION['RolMan']<3){throw new Exception("Usuario no autorizado.");}//1:tecnico, 2:supervisor, 3:analista, 4 gerente de area, 5:gerente general.
        if(empty($_POST['id']) || empty($_POST['imagen'])){throw new Exception("La información esta incompleta.");}

        $FileName='EQU_'.$_POST['id'].'_'.uniqid().'.jpeg';
        $FileEncoded=str_replace("data:image/jpeg;base64,", "", $_POST['imagen']);
        $FileDecoded=base64_decode($FileEncoded);
        file_put_contents($_SERVER['DOCUMENT_ROOT']."/mycloud/gesman/equipos/".$FileName, $FileDecoded);

        $equipo=array(
            'id'=>$_POST['id'],
            'cliid'=>$_SESSION['CliId'],
            'imagen'=>$FileName,
            'usuario'=>date('Ymd-His').' ('.$_SESSION['UserName'].')'
        );
        
        if(FnAgregarEquipoImagen($conmy, $equipo)){
            $datos['res'] = true;
            $datos['msg'] = 'Se agrego la Imagen.';
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