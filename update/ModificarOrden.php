<?php
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGesmanDb.php';

    $Bandera = false;
	if(isset($_SESSION['CliId']) && isset($_SESSION['UserName'])){
		$Bandera = true;
	}

    $data = array();
    $data['res'] = false;
    $data['msg'] = 'Error del sistema.';

    if($Bandera==true && $_SERVER['REQUEST_METHOD'] === 'POST'){
        if(!empty($_POST['idot']) && !empty($_POST['fecha']) && !empty($_POST['idsistema']) && !empty($_POST['sistema']) && !empty($_POST['supervisor']) && !empty($_POST['contacto']) && !empty($_POST['actividad'])){
            
            $Usuario = date('Ymd-His').' ('.$_SESSION['UserName'].')';

            $IdOrigen = 0;
            $Origen = 'UNKNOWN';
            $Km = 0;
            $Descripcion = '';
            $Observacion = '';

            if(!empty($_POST['idorigen'])){
                $IdOrigen = $_POST['idorigen'];
            }

            if(!empty($_POST['origen'])){
                $Origen = $_POST['origen'];
            }

            if(!empty($_POST['km'])){
                $Km = $_POST['km'];
            }

            if(!empty($_POST['descripcion'])){
                $Descripcion = $_POST['descripcion'];
            }

            if(!empty($_POST['observacion'])){
                $Observacion = $_POST['observacion'];
            }

            try{
                $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $stmt=$conmy->prepare("update man_ots set idsistema=:IdSistema, idorigen=:IdOrigen, sistema=:Sistema, origen=:Origen, fechainicial=:Fecha, actividad=:Actividad, descripcion=:Descripcion, 
                observaciones=:Observacion, km=:Km, supervisor=:Supervisor, contacto=:Contacto, actualizacion=:Actualizacion, estado=2 where idot=:IdOt and idcliente=:IdCliente and estado in(1,2,4);");
                $stmt->execute(array(
                    ':IdSistema' => $_POST['idsistema'],
                    ':IdOrigen' => $IdOrigen,
                    ':Sistema' => $_POST['sistema'],
                    ':Origen' => $Origen,
                    ':Fecha' => $_POST['fecha'],
                    ':Actividad' => $_POST['actividad'],
                    ':Descripcion' => $Descripcion,
                    ':Observacion' => $Observacion,
                    ':Km' => $Km,
                    ':Supervisor' => $_POST['supervisor'],
                    ':Contacto' => $_POST['contacto'],
                    ':Actualizacion' => $Usuario,
                    ':IdOt' => $_POST['idot'],
                    ':IdCliente' => $_SESSION['CliId']                  
                ));

                if($stmt->rowCount()>0){
                    $data['res'] = true;
                    $data['msg'] = 'Se modificó la Órden.';                    
                }else{
                    $data['msg'] = 'No se pudo modificar la Órden.';
                }
                $stmt = null;                
            }catch(PDOException $e){
                $stmt=null;
                $data['msg'] = $e->getMessage();
            }
        }else{
            $data['msg'] = 'La información esta incompleta.';
        }
    }else{
        $data['msg'] = 'El usuario no puede realizar esta acción.';
    }

    echo json_encode($data);
?>