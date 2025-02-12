<?php
    
    function FnAgregarFamilia($conmy, $familia) {
        try {
            $id=0;
            $stmt=$conmy->prepare("insert into tblfamilias(cliid, ownid, nombre, ruta, creacion, actualizacion) 
            values(:CliId, :OwnId, :Nombre, :Ruta, :Creacion, :Actualizacion);");
            $stmt->execute(array(
                ':CliId'=>$familia['cliid'],
                ':OwnId'=>$familia['ownid'],
                ':Nombre'=>$familia['nombre'],
                ':Ruta'=>$familia['ruta'],
                ':Creacion'=>$familia['usuario'],
                ':Actualizacion'=>$familia['usuario']                
            ));
            $id=$conmy->lastInsertId();
            return $id;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    function FnModificarFamilia($conmy, $familia) {
        try {
            $res=false;
            $stmt=$conmy->prepare("update tblfamilias set ownid=:OwnId, nombre=:Nombre, ruta=:Ruta, estado=:Estado, actualizacion=:Actualizacion where id=:Id and cliid=:CliId;");
            $stmt->execute(array(
                ':OwnId'=>$familia['ownid'],
                ':Nombre'=>$familia['nombre'],
                ':Ruta'=>$familia['ruta'],
                ':Estado'=>$familia['estado'],
                ':Actualizacion'=>$familia['usuario'],
                ':Id'=>$familia['id'],
                ':CliId'=>$familia['cliid']                
            ));
            if($stmt->rowCount()>0){
                $res=true;
            }
            return $res; 
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    function FnValidarFamiliaDuplicado($conmy, $search) {
        try {
            $cantidad=0;
            $stmt=$conmy->prepare("select count(*) as cantidad from tblfamilias where id!=:Id and cliid=:CliId and ownid=:OwnId and nombre=:Nombre;");
            $stmt->execute(array(':Id'=>$search['id'], ':CliId'=>$search['cliid'], ':OwnId'=>$search['ownid'], ':Nombre'=>$search['nombre']));
            $row=$stmt->fetch();
            if($row){
                $cantidad=$row['cantidad'];
            }
            return $cantidad;
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    function FnBuscarFamilias($conmy, $search) {
        try {
            $datos = array('data'=>array(), 'pag'=>0);
            $query = "";

            if(!empty($search['nombre'])){
                $query=" and nombre like'%".$search['nombre']."%'";
            }

            if($search['estado']>0){
                $query.=" and estado=".$search['estado'];
            }

            $query.=" limit ".$search['pagina'].", 15";

            $stmt = $conmy->prepare("select id, ownid, nombre, ruta, estado from tblfamilias where cliid=:CliId".$query.";");
            $stmt->execute(array(':CliId'=>$search['cliid']));
            $n=$stmt->rowCount();
            if($n>0){
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $datos['data'][]=array(
                        'id'=>(int)$row['id'],
                        'ownid'=>$row['ownid'],
                        'nombre'=>$row['nombre'],
                        'ruta'=>$row['ruta'],
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

    function FnBuscarFamilia($conmy, $search) {
        try {
            $datos=array();
            $stmt = $conmy->prepare("select id, ownid, nombre, ruta, estado from tblfamilias where id=:Id and cliid=:CliId;");
            $stmt->execute(array(':Id'=>$search['id'], ':CliId'=>$search['cliid']));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);            
            if ($row) {
                $datos['id']=$row['id'];
                $datos['ownid']=$row['ownid'];
                $datos['nombre']=$row['nombre'];
                $datos['ruta']=$row['ruta'];
                $datos['estado']=$row['estado'];
            }            
            return $datos;
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    function FnListarFamilias($conmy, $search) {
        try {
            $datos=array();
            $stmt=$conmy->prepare("select id, ownid, nombre, ruta from tblfamilias where cliid=:CliId and estado=2 and ruta like :Nombre limit 15;");
            $stmt->execute(array(':CliId'=>$search['cliid'], ':Nombre'=>'%'.$search['nombre'].'%'));
            while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                $datos[]=array(
                    'id'=>$row['id'],
                    'ownid'=>$row['ownid'],
                    'nombre'=>$row['nombre'],
                    'ruta'=>$row['ruta']
                );
            }
            return $datos;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }
?>