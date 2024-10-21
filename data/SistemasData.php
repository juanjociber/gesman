<?php 
    function FnAgregarSistema($conmy, $sistema) {
        try {
            $id=0;
            $stmt=$conmy->prepare("insert into man_sistemas(idcliente, sistema, creacion, actualizacion) 
            values(:CliId, :Nombre, :Creacion, :Actualizacion);");
            $stmt->execute(array(
                ':CliId'=>$sistema['cliid'],
                ':Nombre'=>$sistema['nombre'],
                ':Creacion'=>$sistema['usuario'],
                ':Actualizacion'=>$sistema['usuario']
            ));
            $id=$conmy->lastInsertId();
            return $id;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    function FnModificarSistema($conmy, $sistema) {
        try {
            $res=false;
            $stmt=$conmy->prepare("update man_sistemas set sistema=:Nombre, estado=:Estado, actualizacion=:Actualizacion where idsistema=:Id and idcliente=:CliId;");
            $stmt->execute(array(':Nombre'=>$sistema['nombre'], ':Estado'=>$sistema['estado'], ':Actualizacion'=>$sistema['usuario'], ':Id'=>$sistema['id'], ':CliId'=>$sistema['cliid']));
            if($stmt->rowCount()>0){
                $res=true;
            }
            return $res; 
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    function FnBuscarSistemas($conmy, $sistema) {
        try {
            $datos = array('data'=>array(), 'pag'=>0);
            $query = "";

            if(!empty($sistema['nombre'])){
                $query=" and sistema like'%".$sistema['nombre']."%'";
            }

            if($sistema['estado']>0){
                $query.=" and estado=".$sistema['estado'];
            }

            $query.=" limit ".$sistema['pagina'].", 2";

            $stmt = $conmy->prepare("select idsistema, sistema, estado from man_sistemas where idcliente=:CliId".$query.";");
            $stmt->execute(array(':CliId'=>$sistema['cliid']));
			$n=$stmt->rowCount();
            if($n>0){
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $datos['data'][]=array(
                        'id'=>(int)$row['idsistema'],
                        'nombre'=>$row['sistema'],
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

    function FnListarSistemas($conmy, $cliid) {
        try {
            $datos = array();
            $stmt = $conmy->prepare("select idsistema, sistema from man_sistemas where idcliente=:CliId and estado=2;");
            $stmt->execute(array(':CliId'=>$cliid));
            if($stmt->rowCount()>0){
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $datos[]=array(
                        'id'=>$row['idsistema'],
                        'nombre'=>$row['sistema']
                    );
                }
            }            
            return $datos;
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage().$msg);
        }
    }

    /*function FnListarClienteSistemas2($conmy, $cliid, $nombre) {
        try {
            $datos = array();
            $query="";

            if(!empty($nombre)){
                $query=" and sistema like '%".$nombre."%'";
            }

            $stmt = $conmy->prepare("select idsistema, sistema from man_sistemas where idcliente=:CliId and estado=2".$query.";");
            $stmt->execute(array(':CliId'=>$cliid));
            if($stmt->rowCount()>0){
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $datos[]=array(
                        'id'=>$row['idsistema'],
                        'nombre'=>$row['sistema']
                    );
                }
            }            
            return $datos;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage().$msg);
        }
    }*/
?>