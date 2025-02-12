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

        $file = $_FILES['archivo'];
        if (!in_array($file['type'], $allowedTypes)) {throw new Exception("El Archivo no es válido.");}

        $fileName = uniqid('odometro_', true).'.'.pathinfo($file['name'], PATHINFO_EXTENSION);
        if (!move_uploaded_file($file['tmp_name'], $_SERVER['DOCUMENT_ROOT']."/mycloud/gesman/temp/".$fileName)){throw new Exception("Error al mover el archivo.");}

        include($_SERVER['DOCUMENT_ROOT'].'/mycloud/library/PHPExcel-1.8/Classes/PHPExcel.php');

        $objPHPExcel = PHPExcel_IOFactory::load($_SERVER['DOCUMENT_ROOT']."/mycloud/gesman/temp/".$fileName);// Cargar el archivo de Excel
        $hoja = $objPHPExcel->getActiveSheet();// Obtener la primera hoja (puedes especificar la hoja si es necesario)

        $equipos=FnListarClienteEquipos($conmy, $_SESSION['gesman']['CliId']);
        if(count($equipos)==0){ throw new Exception("El Cliente no tiene equipos.");}

        $data=array();
        $contadorFila = 1;
        
        foreach ($hoja->getRowIterator() as $row) {
            if ($contadorFila == 1) {//Omitir primera fila
                $contadorFila++;
                continue;
            }
            foreach($equipos as $item){
                if($item['nombre']==$row->getCellIterator('A')->current()->getValue()){
                    $data[]=array(
                        'id'=>(int)$item['id'], 
                        'km'=>$row->getCellIterator('B')->current()->getValue(), 
                        'hm'=>$row->getCellIterator('C')->current()->getValue(), 
                        'fecha'=>$_POST['fecha']);
                    break;
                }
            }            
            $contadorFila++;
        }

        if(count($data)==0){throw new Exception("No hay nada que actualizar.");}

        if(FnModificarEquiposOdometro($conmy, $data)){
            $datos['res'] = true;
            $datos['msg'] = 'Se modificaron los registros.';
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