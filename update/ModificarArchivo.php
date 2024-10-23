<?php
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/ArchivosData.php";
  $data = array('res' => false, 'msg' => 'Error general.');
  
  try {
    if (empty($_SESSION['CliId']) && empty($_SESSION['UserName'])) { throw new Exception("Usuario no tiene Autorización."); }
    if (empty($_POST['id'])) { throw new Exception("La información está incompleta."); }
    if (empty($_POST['tipo'])) { throw new Exception("No existe tipo de interfaz."); }

    $archivo = new stdClass();
    $archivo->Id = $_POST['id'];
    $archivo->Titulo = $_POST['titulo'];
    $archivo->Descripcion = empty($_POST['descripcion']) ? null : $_POST['descripcion'];

    // MANEJAR CARGA D ARCHIVO Y MODIFICACIÓN
    if (!empty($_FILES['archivo']['name'])) {
      $Tipo = ($_POST['tipo'] === 'tipo') ? 'INFD' : 'INFA'; 
      $archivo->nombre = $Tipo . '_'. $_POST['id'] . '_' . uniqid() . '.jpeg'; 
      move_uploaded_file($_FILES['archivo']['tmp_name'], $_SERVER['DOCUMENT_ROOT']."/mycloud/gesman/files/" . $archivo->nombre);
    } 
    // Modificar el archivo, que incluye la búsqueda del archivo existente
    if (FnModificarArchivo($conmy, $archivo)) {
      $data['msg'] = "Modificación realizada con éxito.";
      $data['res'] = true;
    } else {
      $data['msg'] = "Error al procesar la solicitud.";
    }
  } catch (PDOException $ex) {
    $data['msg'] = $ex->getMessage();
    $conmy = null;
  } catch (Exception $ex) {
    $data['msg'] = $ex->getMessage();
    $conmy = null;
  }
    echo json_encode($data);
?>
