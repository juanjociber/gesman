<?php 
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/ArchivosData.php";

  $data = array('res' => false,'msg' => 'Error general.','data' => null);
  
  try {
    if(empty($_SESSION['CliId']) && empty($_SESSION['UserName'])){throw new Exception("Usuario no tiene Autorización.");}
    if(empty($_POST['id'])){ throw new Exception("La informacion esta incompleta.");}
  
    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $archivo = FnBuscarArchivo($conmy, $_POST['id']);
    if ($archivo) {
      $data['res'] = true;
      $data['msg'] = 'Ok.';
      $data['data'] = $archivo;
    } else {
      $data['msg'] = 'No existen registros en la base de datos.';
    }

  } catch(PDOException $ex){
    $data['msg'] = $ex->getMessage();
    $conmy = null;
  } catch (Exception $ex) {
    $data['msg'] = $ex->getMessage();
    $conmy = null;
  } 
  echo json_encode($data);
?>








