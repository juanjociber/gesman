<?php
    session_start();
    
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/ArchivosData.php";

    $datos = array('res'=>false, 'id'=>0, 'msg'=>'Error general.');

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(empty($_SESSION['UserName'])){throw new Exception("Usuario no tiene Autorización.");}
        if(empty($_POST['refid']) || empty($_POST['tabla'])){throw new Exception("La información esta incompleta.");}

        $FileName='';
        $ArchivoTipo='';

        if(isset($_FILES['archivo'])) {
            $FileName=$_POST['tabla'].'_'.$_POST['refid'].'_'.uniqid().'.pdf';
            $ArchivoTipo='PDF';
            $archivoTemporal = $_FILES['archivo']['tmp_name'];
            move_uploaded_file($archivoTemporal, $_SERVER['DOCUMENT_ROOT']."/mycloud/gesman/files/".$FileName);
        }elseif(isset($_POST['archivo'])) {
            $FileName=$_POST['tabla'].'_'.$_POST['refid'].'_'.uniqid().'.jpeg';
            $ArchivoTipo='IMG';
            $FileEncoded=str_replace("data:image/jpeg;base64,", "", $_POST['archivo']);
            $FileDecoded=base64_decode($FileEncoded);
            file_put_contents($_SERVER['DOCUMENT_ROOT']."/mycloud/gesman/files/".$FileName, $FileDecoded);
        }else{
            throw new Exception("No se reconoce el Archivo.");
        }

        $archivo=array(
            'refid'=>$_POST['refid'],
            'tabla'=>$_POST['tabla'],
            'nombre'=>$FileName,
            'titulo'=>empty($_POST['titulo'])?null:$_POST['titulo'],
            'descripcion'=>empty($_POST['descripcion'])?null:$_POST['descripcion'],
            'tipo'=>$ArchivoTipo,
            'usuario'=>date('Ymd-His').' ('.$_SESSION['UserName'].')'
        );

        $id=FnAgregarArchivo($conmy, $archivo);
        if($id>0){
            $datos['res']=true;
            $datos['id']=$id;
            $datos['msg']='Se agregó el Archivo.';
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