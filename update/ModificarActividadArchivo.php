<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/gesman/data/SesionData.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gesman/connection/ConnGesmanDb.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gesman/data/ArchivosData.php";

$datos = array('res' => false, 'msg' => 'Error general.', 'data' => null);

try {
    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if (!FnValidarSesion()) { throw new Exception("Usuario no tiene Autorización."); }
    if (empty($_POST['id']) ) { throw new Exception("La información está incompleta."); }

    // INICIALIZAR VARIABLES
    $archivoNombre = '';
    $archivoTipo = '';

    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
      $archivoTipo = 'IMG';
      $archivoNombre = 'INFD' . '_' . $_POST['id'] . '_' . uniqid() . '.jpeg';
      $archivoTemporal = $_FILES['archivo']['tmp_name'];
      $ext = pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION);
      
      // MOVER ARCHIVO A CARPETA DESTINO
      move_uploaded_file($archivoTemporal, $_SERVER['DOCUMENT_ROOT'] . "/mycloud/gesman/files/" . $archivoNombre);
    } elseif (isset($_POST['archivo'])) {
        $archivoTipo = 'IMG';
        $archivoNombre = 'INFD' . '_' . $_POST['id'] . '_' . uniqid() . '.jpeg';
        $archivoEncoded = str_replace("data:image/jpeg;base64,", "", $_POST['archivo']);
        $archivoDecoded = base64_decode($archivoEncoded);
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/mycloud/gesman/files/" . $archivoNombre, $archivoDecoded);
    }  elseif (!empty($_POST['nombre'])) {
        // MANTENIENDO NOMBRE
        $archivoNombre = $_POST['nombre'];
        $archivoTipo = 'IMG';
    } else {
        $archivoNombre = null;
        $archivoTipo = null;
    }
    // ARRAY CON INFORMACIÓN DE ARCHIVO
    $archivo = array(
      'id' => (int)$_POST['id'],
      'titulo' => trim($_POST['titulo']),
      'descripcion' => trim($_POST['descripcion']),
      'nombre' => $archivoNombre, 
      'tipo' => $archivoTipo,
      'usuario' => date('Ymd-His') . ' (' . $_SESSION['gesman']['Nombre'] . ')'
    );
    // MODIFICAR ARCHIVO
    $resultado = FnModificarActividadArchivo($conmy, (object)$archivo);

    // Verificar el resultado de la actualización
    if ($resultado) {
      $datos['res'] = true;
      $datos['msg'] = 'El archivo se actualizó correctamente.';
    } else {
       $datos['msg'] = 'No se pudo actualizar el archivo.';
    }
    $conmy = null;

} catch (PDOException $ex) {
    $datos['msg'] = $ex->getMessage();
    $conmy = null;
} catch (Exception $ex) {
    $datos['msg'] = $ex->getMessage();
    $conmy = null;
}
  echo json_encode($datos);
?>
