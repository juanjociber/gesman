<?php 
    function FnAgregarOrigen($conmy, $origen) {
        try {
            $id=0;
            $stmt=$conmy->prepare("insert into man_origenes(idcliente, origen, creacion, actualizacion) 
            values(:CliId, :Nombre, :Creacion, :Actualizacion);");
            $stmt->execute(array(
                ':CliId'=>$origen['cliid'],
                ':Nombre'=>$origen['nombre'],
                ':Creacion'=>$origen['usuario'],
                ':Actualizacion'=>$origen['usuario']
            ));
            $id=$conmy->lastInsertId();
            return $id;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    function FnModificarOrigen($conmy, $origen) {
        try {
            $res=false;
            $stmt=$conmy->prepare("update man_origenes set origen=:Nombre, estado=:Estado, actualizacion=:Actualizacion where idorigen=:Id and idcliente=:CliId;");
            $stmt->execute(array(':Nombre'=>$origen['nombre'], ':Estado'=>$origen['estado'], ':Actualizacion'=>$origen['usuario'], ':Id'=>$origen['id'], ':CliId'=>$origen['cliid']));
            if($stmt->rowCount()>0){
                $res=true;
            }
            return $res; 
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    function FnBuscarOrigenes($conmy, $origen) {
        try {
            $datos = array('data'=>array(), 'pag'=>0);
            $query = "";

            if(!empty($origen['nombre'])){
                $query=" and origen like'%".$origen['nombre']."%'";
            }else{
                if($origen['estado']>0){
                    $query.=" and estado=".$origen['estado'];
                }
                $query.=" limit ".$origen['pagina'].", 2";
            }

            $stmt = $conmy->prepare("select idorigen, origen, estado from man_origenes where idcliente=:CliId".$query.";");
            $stmt->execute(array(':CliId'=>$origen['cliid']));
			$n=$stmt->rowCount();
            if($n>0){
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $datos['data'][]=array(
                        'id'=>(int)$row['idorigen'],
                        'nombre'=>$row['origen'],
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

    function FnListarClienteOrigenes($conmy, $cliid) {
        try {
            $datos = array();
            $stmt = $conmy->prepare("select idorigen, origen from man_origenes where idcliente=:CliId and estado=2;");
            $stmt->execute(array(':CliId'=>$cliid));
            if($stmt->rowCount()>0){
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $datos[]=array(
                        'id'=>$row['idorigen'],
                        'nombre'=>$row['origen']
                    );
                }
            }            
            return $datos;
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage().$msg);
        }
    }

    function FnListarClienteOrigenes2($conmy, $cliid, $nombre) {
        try {
            $datos = array();
            $query="";

            if(!empty($nombre)){
                $query=" and origen like '%".$nombre."%'";
            }

            $stmt = $conmy->prepare("select idorigen, origen from man_origen where idcliente=:CliId and estado=2".$query.";");
            $stmt->execute(array(':CliId'=>$cliid));
            if($stmt->rowCount()>0){
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $datos[]=array(
                        'id'=>$row['idorigen'],
                        'nombre'=>$row['origen']
                    );
                }
            }            
            return $datos;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage().$msg);
        }
    }
?>