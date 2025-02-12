<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Authorization, Content-Type, Accept");
    header("Access-Control-Allow-Methods: GET");
    header("Content-Type: application/json");

    if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {    
        http_response_code(200);
        exit();
    }

    $data=array('res'=>false, 'msg'=>'Error General.', 'tipo'=>'', 'archivo'=>'');

    try {
        if(empty($_GET['nombre']) || empty($_GET['tipo'])){throw new Exception("La Información está incompletass.");}

        $NOMBRE = $_GET['nombre'];
        $ARCHIVO_TIPO='';

        if($_GET['tipo']=='IMG'){
            $ARCHIVO_TIPO='image/jpeg';
        }else if($_GET['tipo']=="PDF"){
            $ARCHIVO_TIPO='application/pdf';
        }else{
            throw new Exception("El tipo de archivo no esta disponible.");
        }

        $PATH_FILE = $_SERVER['DOCUMENT_ROOT'].'/mycloud/gesman/files/'.$NOMBRE;
        if(!file_exists($PATH_FILE)){throw new  Exception('El Archivo no esta listo.'); }
        //$archivo_tipo = mime_content_type($PATH_FILE);
        $archivo_data = file_get_contents($PATH_FILE);

        $data['res'] = true;
        $data['msg'] = 'Ok';
        $data['tipo'] = $ARCHIVO_TIPO;
        $data['archivo'] = base64_encode($archivo_data);
        
    } catch (Exception $ex) {
        $data['msg'] = $ex->getMessage();
    }

    echo json_encode($data);
?>