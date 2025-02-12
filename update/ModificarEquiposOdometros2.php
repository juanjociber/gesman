<?php 
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/EquiposData.php";

    $datos = array('res'=>false, 'msg'=>'Error general.', 'data'=>array());

    $allowedTypes = array(
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
        'application/vnd.ms-excel'  // .xls
    );

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(!FnValidarSesion()){throw new Exception("Usuario no tiene Autorización.");}
        if (!isset($_FILES['archivo']) && $_FILES['archivo']['error'] != UPLOAD_ERR_OK) {throw new Exception($_FILES['excelFile']['error']);}

        $file=$_FILES['archivo'];
        if (!in_array($file['type'], $allowedTypes)) {throw new Exception("El Archivo no es válido.");}

        
        $fileName = uniqid('odometro_', true).'.'.pathinfo($file['name'], PATHINFO_EXTENSION);
        if (!move_uploaded_file($file['tmp_name'], $_SERVER['DOCUMENT_ROOT']."/mycloud/gesman/temp/".$fileName)){throw new Exception("Error al mover el archivo.");}

        require_once $_SERVER['DOCUMENT_ROOT']."/mycloud/library/simplexlsx-0.8.19/src/SimpleXLSX2.php";
        $xlsx = SimpleXLSX::parse($_SERVER['DOCUMENT_ROOT']."/mycloud/gesman/temp/".$fileName);
        $rows = $xlsx->rows(); // Esto devuelve un array de filas
        
        /*$equipos=FnListarClienteEquipos($conmy, $_SESSION['gesman']['CliId']);
        if(count($equipos)==0){ throw new Exception("El Cliente no tiene equipos.");}

        $data=array();
        foreach ($rows as $index => $row) {    
            if ($index === 0) continue;// Saltar la primera fila (encabezados)
            foreach($equipos as $item){
                if($item['nombre']==$row[0]){
                    $data[]=array('id'=>(int)$item['id'], 'km'=>$row[1], 'hm'=>$row[2], 'fecha'=>$_POST['fecha']);
                    break;
                }
            }
        }

        if(count($data)==0){throw new Exception("No hay nada que actualizar.");}

        if(FnModificarEquiposOdometro($conmy, $data)){
            $datos['res'] = true;
            $datos['msg'] = 'Se modificaron los registros.';
        }*/

        $datos['res']=true;
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