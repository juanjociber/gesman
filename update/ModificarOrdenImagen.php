<?php
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGesmanDb.php';

    $Bandera = false;
	if(isset($_SESSION['UserName'])){
		$Bandera = true;
	}

    $data = array();
    $data['res'] = false;
    $data['msg'] = 'Error del sistema.';

    if($Bandera==true && $_SERVER['REQUEST_METHOD'] === 'POST'){
        if(!empty($_POST['id']) && !empty($_POST['refid'])){
            try{
                $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $stmt=$conmy->prepare("delete from tblarchivos where id=:Id and refid=:RefId;");
                $stmt->bindParam(':Id', $_POST['id']);
                $stmt->bindParam(':RefId', $_POST['refid']);
                $stmt->execute();
                $data['res'] = true;
                $data['msg'] = 'Se eliminó la imágen.';
                $stmt = null;
            }catch(PDOException $e){
                $stmt = null;
                $data['msg'] = $e->getMessage();
            }
        }else{
            if(!empty($_POST['refid']) && !empty($_POST['tabla']) && !empty($_POST['descripcion'])){

                $Usuario = date('Ymd-His').' ('.$_SESSION['UserName'].')';
                $Descripcion = substr($_POST['descripcion'], 0, 100);
                $FileName='';
                $ArchivoTipo='';

                if(isset($_FILES['archivo'])) {
                    $FileName = $_POST['tabla'].'_'.$_POST['refid'].'_'.uniqid().'.pdf';
                    $ArchivoTipo='PDF';
                    //$nombreArchivo = $_FILES['archivo']['name'];
                    $archivoTemporal = $_FILES['archivo']['tmp_name'];
                    //$destino = 'uploads/' . $nombreArchivo;
                    move_uploaded_file($archivoTemporal, $_SERVER['DOCUMENT_ROOT']."/mycloud/gesman/files/".$FileName);

                    $data['res']=true;
                    $data['msg']='esto es un archivo.';
                }elseif(isset($_POST['archivo'])) {
                    $FileName = $_POST['tabla'].'_'.$_POST['refid'].'_'.uniqid().'.jpeg';
                    $ArchivoTipo='IMG';
                    $FileEncoded = str_replace("data:image/jpeg;base64,", "", $_POST['archivo']);
                    $FileDecoded = base64_decode($FileEncoded);
                    file_put_contents($_SERVER['DOCUMENT_ROOT']."/mycloud/gesman/files/".$FileName, $FileDecoded);

                    $data['res']=true;
                    $data['msg']='Esto es un canvas.';
                }else{
                    $data['msg']='No se reconoce el Archivo.';
                    //throw new Exception('Archivo desconocido.');
                }

                try{
                    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $stmt=$conmy->prepare("insert into tblarchivos(refid, tabla, nombre, descripcion, tipo, estado, actualizacion) values (:RefId, :Tabla, :Nombre, :Descripcion, :Tipo, :Estado, :Actualizacion)");
                    $stmt->execute(array('RefId'=>$_POST['refid'], 'Tabla'=>$_POST['tabla'], 'Nombre'=>$FileName, 'Descripcion'=>$Descripcion, 'Tipo'=>$ArchivoTipo, 'Estado'=>2, 'Actualizacion'=>$Usuario));
                    $stmt = null;
                    $data['res'] = true;
                    $data['msg'] = 'Se agregó la imágen.';
                }catch(PDOException $e){
                    $stmt = null;
                    $data['msg'] = $e->getMessage();
                }
            }else{
                $data['msg']='Información incompleta.';
            }                    
        }
    }else{
        $data['msg'] = 'Usuario no autorizado.';
    }

    echo json_encode($data);
?>