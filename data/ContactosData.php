<?php 
    function FnAgregarContacto($conmy, $contacto) {
        try {
            $id=0;
            $stmt=$conmy->prepare("insert into cli_supervisores(idcliente, supervisor, creacion, actualizacion) 
            values(:CliId, :Nombre, :Creacion, :Actualizacion);");
            $stmt->execute(array(
                ':CliId'=>$contacto['cliid'],
                ':Nombre'=>$contacto['nombre'],
                ':Creacion'=>$contacto['usuario'],
                ':Actualizacion'=>$contacto['usuario']
            ));
            $id=$conmy->lastInsertId();
            return $id;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    function FnModificarContacto($conmy, $contacto) {
        try {
            $res=false;
            $stmt=$conmy->prepare("update cli_supervisores set supervisor=:Nombre, estado=:Estado, actualizacion=:Actualizacion where idsupervisor=:Id and idcliente=:CliId;");
            $stmt->execute(array(':Nombre'=>$contacto['nombre'], ':Estado'=>$contacto['estado'], ':Actualizacion'=>$contacto['usuario'], ':Id'=>$contacto['id'], ':CliId'=>$contacto['cliid']));
            if($stmt->rowCount()>0){
                $res=true;
            }
            return $res; 
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    function FnBuscarContactos($conmy, $contacto) {
        try {
            $datos = array('data'=>array(), 'pag'=>0);
            $query = "";

            if(!empty($contacto['nombre'])){
                $query=" and supervisor like'%".$contacto['nombre']."%'";
            }

            if($contacto['estado']>0){
                $query.=" and estado=".$contacto['estado'];
            }

            $query.=" limit ".$contacto['pagina'].", 2";
            
            $stmt = $conmy->prepare("select idsupervisor, supervisor, estado from cli_supervisores where idcliente=:CliId".$query.";");
            $stmt->execute(array(':CliId'=>$contacto['cliid']));
			$n=$stmt->rowCount();
            if($n>0){
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $datos['data'][]=array(
                        'id'=>(int)$row['idsupervisor'],
                        'nombre'=>$row['supervisor'],
                        'estado'=>(int)$row['estado']
                    );
                }
                $datos['pag']=$n;
            }            
            return $datos;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage().$msg);
        }
    }

    /*
    function FnListarClienteContactos($conmy, $cliid) {
        try {
            $datos = array();
            $stmt = $conmy->prepare("select idsupervisor, supervisor from cli_supervisores where idcliente=:CliId and estado=2;");
            $stmt->execute(array(':CliId'=>$cliid));
            if($stmt->rowCount()>0){
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $datos[]=array(
                        'id'=>$row['idsupervisor'],
                        'nombre'=>$row['supervisor']
                    );
                }
            }            
            return $datos;
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage().$msg);
        }
    }

    function FnListarClienteContactos2($conmy, $cliid, $nombre) {
        try {
            $datos = array();
            $query="";

            if(!empty($nombre)){
                $query=" and supervisor like '%".$nombre."%'";
            }

            $stmt = $conmy->prepare("select idsupervisor, supervisor from cli_supervisores where idcliente=:CliId and estado=2".$query.";");
            $stmt->execute(array(':CliId'=>$cliid));
            if($stmt->rowCount()>0){
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $datos[]=array(
                        'id'=>$row['idsupervisor'],
                        'nombre'=>$row['supervisor']
                    );
                }
            }            
            return $datos;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage().$msg);
        }
    }*/
?>