<?php
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGesmanDb.php';

    $Bandera = false;
	if(isset($_SESSION['CliId']) && isset($_SESSION['UserName']) && isset($_SESSION['UserNombre'])){
		$Bandera = true;
	}

    $data = array();
    $data['id'] = 0;
    $data['res'] = false;
    $data['msg'] = 'Error del sistema.';

    if($Bandera==true && $_SERVER['REQUEST_METHOD']==='POST'){
        if(!empty($_POST['orden']) && !empty($_POST['idactivo']) && !empty($_POST['activo']) && !empty($_POST['idtipo']) && !empty($_POST['tipo']) && !empty($_POST['idsistema']) && !empty($_POST['sistema']) && !empty($_POST['fecha']) && !empty($_POST['actividad'])){
            $Usuario = date('Ymd-His').' ('.$_SESSION['UserName'].')';
            $Cantidad =0;
            $Km = 0;
            try{
                $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $stmt=$conmy->prepare("select count(*) as cantidad from man_ots where ot=:Ot and idcliente=:IdCliente and idtipoot=:IdTipoOt;");
                $stmt->execute(array('Ot'=>$_POST['orden'], 'IdCliente' => $_SESSION['CliId'], 'IdTipoOt'=> $_POST['idtipo']));
                $row=$stmt->fetch();
                if($row){
                    $Cantidad = $row['cantidad'];
                }
                        
                if($Cantidad == 0 ){
                    if(!empty($_POST['km'])){
                        $Km = $_POST['km'];
                    }
                    $stmt=$conmy->prepare("insert into man_ots(idactivo, idtipoot, idsistema, idorigen, idactividad, idcliente, idcontacto, ot, activo, tipoot, sistema, origen, fechainicial, tipotrabajo, actividad, km, supervisor, contacto, estado, creacion, actualizacion) values 
                        (:IdActivo, :IdTipoOt, :IdSistema, :IdOrigen, :IdActividad, :IdCliente, :IdContacto, :Ot, :Activo, :TipoOt, :Sistema, :Origen, :FechaInicial, :TipoTrabajo, :Actividad, :Km, :Supervisor, :Contacto, :Estado, :Creacion, :Actualizacion);");
                    $stmt->execute(array(
                        ':IdActivo' => $_POST['idactivo'],
                        ':IdTipoOt' => $_POST['idtipo'],
                        ':IdSistema' => $_POST['idsistema'],
                        ':IdOrigen' => 0,
                        ':IdActividad' => 0,
                        ':IdCliente' => $_SESSION['CliId'],
                        ':IdContacto' => 0,
                        ':Ot' => $_POST['orden'],
                        ':Activo' => $_POST['activo'],
                        ':TipoOt' => $_POST['tipo'],
                        ':Sistema' => $_POST['sistema'],
                        ':Origen' => 'UNKNOWN',
                        ':FechaInicial' => $_POST['fecha'],
                        ':TipoTrabajo' => 'TRABAJO_LIVIANO',
                        ':Actividad' => $_POST['actividad'],
                        ':Km' => $Km,
                        ':Supervisor' => $_SESSION['UserNombre'],
                        ':Contacto' => "UNKNOWN",
                        ':Estado' => 2, //2:Proceso
                        ':Creacion' => $Usuario,
                        ':Actualizacion' => $Usuario
                    ));
                    $data['id'] = $conmy->lastInsertId();
                    $data['res'] = true;
                    $data['msg'] = 'Se agregó la Órden.';
                }else{
                    $data['msg'] = 'La Órden ya existe.';
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