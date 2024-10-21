<?php 
    session_start();

    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/OrdenesData.php";

    $datos = array('data'=>array(), 'res'=>false, 'msg'=>'Error general.');

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(empty($_SESSION['CliId'])){throw new Exception("Usuario no tiene Autorización.");}
        if(empty($_POST['recurso'])){throw new Exception("La información esta incompleta.");}

        $nombre=empty($_POST['nombre'])?'':$_POST['nombre'];
        $response=array();

        switch ($_POST['recurso']) {	
            case 'sistema':
                $response=FnListarClienteSistemas($conmy, $_SESSION['CliId'], $nombre);
                break;            
            case 'origen':
                $response=FnListarClienteOrigenes($conmy, $_SESSION['CliId'], $nombre);                
                break;
            case 'contacto':
                $response=FnListarClienteContactos($conmy, $_SESSION['CliId'], $nombre);
                break;
            case 'supervisor':
                $response=FnListarClienteContactos($conmy, 1, $nombre);
                break;            
            default:
                throw new Exception("No se reconoce el Recurso.");
                break;
        }

        if(count($response)>0){
            $datos['res'] = true;
            $datos['msg'] = 'Ok.';
            $datos['data'] = $response;
        }else{
            throw new Exception("No se encontró resultados.");            
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