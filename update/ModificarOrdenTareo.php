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
        if(!empty($_POST['id']) && !empty($_POST['idot'])){
            try{
                $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $stmt=$conmy->prepare("delete from man_tareos where idtareo=:Id and idot=:IdOt;");
                $stmt->execute(array(':Id'=>$_POST['id'], ':IdOt'=>$_POST['idot']));
                $data['res'] = true;
                $data['msg'] = 'Se eliminó la el Tareo.';
                $stmt = null;
            }catch(PDOException $e){
                $stmt = null;
                $data['msg'] = $e->getMessage();
            }
        }else{
            if(!empty($_POST['idot']) && !empty($_POST['idpersonal']) && !empty($_POST['personal']) && !empty($_POST['ingreso']) && !empty($_POST['salida'])){
                $Usuario = date('Ymd-His').' ('.$_SESSION['UserName'].')';
                $Ingreso = new DateTime($_POST['ingreso']);
                $Salida = new DateTime($_POST['salida']);
                $Intervalo = $Ingreso->diff($Salida);
                $Minutos = $Intervalo->days * 24 * 60 + $Intervalo->h * 60 + $Intervalo->i;
                try{
                    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $stmt=$conmy->prepare("insert into man_tareos(idot, idpersonal, personal, ingreso, salida, tmin, estado, creacion, actualizacion) 
                    values (:IdOt, :IdPersonal, :Personal, :Ingreso, :Salida, :TMin, :Estado, :Creacion, :Actualizacion)");
                    $stmt->execute(array(':IdOt'=>$_POST['idot'], ':IdPersonal'=>$_POST['idpersonal'], ':Personal'=>$_POST['personal'], ':Ingreso'=>$_POST['ingreso'], ':Salida'=>$_POST['salida'], ':TMin'=>$Minutos, ':Estado'=>1, ':Creacion'=>$Usuario, ':Actualizacion'=>$Usuario));
                    $stmt = null;
                    $data['res'] = true;
                    $data['msg'] = 'Se agregó el Tareo.';
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