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

    if($Bandera==true && $_SERVER['REQUEST_METHOD']==='POST'){
        if(!empty($_POST['id'])){
            try{
                $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $stmt=$conmy->prepare("select idcliente, idodoo, idwhodoo, nombre from man_clientes where idcliente=:IdCliente and estado=:Estado;");
                $stmt->execute(array('IdCliente'=>$_POST['id'], 'Estado'=>2));
                $row=$stmt->fetch();
                if($row){
                    $_SESSION['CliId'] = $row['idcliente'];
                    $_SESSION['CliIdOdoo'] = $row['idodoo'];
                    $_SESSION['WhIdOdoo'] = $row['idwhodoo'];
                    $_SESSION['CliNombre'] = $row['nombre'];
                    $data['res'] = true;
                    $data['msg'] = 'Se hizo el cambio de Empresa';
                }else{
                    $data['msg'] = 'No se encontró la Empresa.';
                }
                $stmt=null;
            }catch(PDOException $ex){
                $stmt=null;
                $data['msg'] = $e->getMessage();
            }
        }else{
            $data['msg'] = 'La información esta incompleta.';
        }
    }else{
        $data['msg'] = 'Usuario no autorizado.';
    }

    echo json_encode($data);
?>