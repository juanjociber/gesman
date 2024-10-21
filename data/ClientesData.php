<?php 
    function FnAgregarCliente($conmy, $cliente) {
        try {
            $id=0;
            $stmt=$conmy->prepare("insert into man_clientes(idodoo, idwhodoo, ruc, razonsocial, nombre, direccion, creacion, actualizacion) 
            values(:OdoId, :AlmId, :Ruc, :Nombre, :Alias, :Direccion, :Creacion, :Actualizacion);");
            $stmt->execute(array(
                ':OdoId'=>$cliente['odoid'],
                ':AlmId'=>$cliente['almid'],
                ':Ruc'=>$cliente['ruc'],
                ':Nombre'=>$cliente['nombre'],
                ':Alias'=>$cliente['alias'],
                ':Direccion'=>$cliente['direccion'],
                ':Creacion'=>$cliente['usuario'],
                ':Actualizacion'=>$cliente['usuario']
            ));
            $id=$conmy->lastInsertId();
            return $id;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    function FnModificarCliente($conmy, $cliente) {
        try {
            $res=false;
            $stmt=$conmy->prepare("update man_clientes set idodoo=:OdoId, idwhodoo=:AlmId, razonsocial=:Nombre, nombre=:Alias, direccion=:Direccion, estado=:Estado, actualizacion=:Actualizacion where idcliente=:Id;");
            $stmt->execute(array(':OdoId'=>$cliente['odoid'], ':AlmId'=>$cliente['almid'], ':Nombre'=>$cliente['nombre'], ':Alias'=>$cliente['alias'], ':Direccion'=>$cliente['direccion'], ':Estado'=>$cliente['estado'], ':Actualizacion'=>$cliente['usuario'], ':Id'=>$cliente['id']));
            if($stmt->rowCount()>0){
                $res=true;
            }
            return $res; 
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    function FnValidarClienteDuplicado($conmy, $ruc) {
        try {
            $cantidad=0;
            $stmt=$conmy->prepare("select count(*) as cantidad from man_clientes where ruc=:Ruc;");
            $stmt->execute(array(':Ruc'=>$ruc));
            $row=$stmt->fetch();
            if($row){
                $cantidad=$row['cantidad'];
            }
            return $cantidad;
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    function FnBuscarCliente($conmy, $id) {
        try {
            $datos=array();
            $stmt = $conmy->prepare("select idcliente, idodoo, idwhodoo, ruc, razonsocial, nombre, direccion, estado FROM man_clientes WHERE idcliente=:Id;");
            $stmt->execute(array(':Id'=>$id));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);            
            if ($row) {
                $datos['id']=$row['idcliente'];
                $datos['odoid']=$row['idodoo'];
                $datos['almid']=$row['idwhodoo'];
                $datos['ruc']=$row['ruc'];
                $datos['nombre']=$row['razonsocial'];
                $datos['direccion']=$row['direccion'];
                $datos['alias']=$row['nombre'];
                $datos['estado']=$row['estado'];
            }            
            return $datos;
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    function FnBuscarClientes($conmy, $cliente) {
        try {
            $datos = array('data'=>array(), 'pag'=>0);
            $query = "";

            if(!empty($cliente['nombre'])){
                $query=" and razonsocial like'%".$cliente['nombre']."%'";
            }

            if($cliente['estado']>0){
                $query.=" and estado=".$cliente['estado'];
            }

            $query.=" limit ".$cliente['pagina'].", 2";

            $stmt = $conmy->prepare("select idcliente, ruc, razonsocial, nombre, direccion, estado from man_clientes where 1=1".$query.";");
            $stmt->execute();
			$n=$stmt->rowCount();
            if($n>0){
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $datos['data'][]=array(
                        'id'=>$row['idcliente'],
                        'ruc'=>$row['ruc'],
                        'nombre'=>$row['razonsocial'],
                        'alias'=>$row['nombre'],
                        'direccion'=>$row['direccion'],
                        'estado'=>(int)$row['estado']
                    );
                }
                $datos['pag']=$n;
            }            
            return $datos;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }
?>