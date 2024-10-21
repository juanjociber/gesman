<?php 
    function FnAgregarFlota($conmy, $flota) {
        try {
            $id=0;
            $stmt=$conmy->prepare("insert into man_grupos(idcliente, grupo, creacion, actualizacion) 
            values(:CliId, :Nombre, :Creacion, :Actualizacion);");
            $stmt->execute(array(
                ':CliId'=>$flota['cliid'],
                ':Nombre'=>$flota['nombre'],
                ':Creacion'=>$flota['usuario'],
                ':Actualizacion'=>$flota['usuario']
            ));
            $id=$conmy->lastInsertId();
            return $id;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    function FnModificarFlota($conmy, $flota) {
        try {
            $res=false;
            $stmt=$conmy->prepare("update man_grupos set grupo=:Nombre, estado=:Estado, actualizacion=:Actualizacion where idgrupo=:Id and idcliente=:CliId;");
            $stmt->execute(array(':Nombre'=>$flota['nombre'], ':Estado'=>$flota['estado'], ':Actualizacion'=>$flota['usuario'], ':Id'=>$flota['id'], ':CliId'=>$flota['cliid']));
            if($stmt->rowCount()>0){
                $res=true;
            }
            return $res; 
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    function FnBuscarFlota($conmy, $flota) {
        try {
            $datos=array();

            $stmt = $conmy->prepare("select idgrupo, grupo, estado from man_grupos where idgrupo=:Id and idcliente=:CliId;");
            $stmt->execute(array(':Id'=>$flota['id'], ':CliId'=>$flota['cliid']));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);            
            if ($row) {
                $datos['id']=$row['idgrupo'];
                $datos['nombre']=$row['grupo'];
                $datos['estado']=$row['estado'];
            }            
            return $datos;
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    function FnBuscarFlotas($conmy, $flota) {
        try {
            $datos = array('data'=>array(), 'pag'=>0);
            $query = "";

            if(!empty($flota['nombre'])){
                $query=" and grupo like'%".$flota['nombre']."%'";
            }else{
                if($flota['estado']>0){
                    $query.=" and estado=".$flota['estado'];
                }
                $query.=" limit ".$flota['pagina'].", 2";
            }

            $stmt = $conmy->prepare("select idgrupo, grupo, estado from man_grupos where idcliente=:CliId".$query.";");
            $stmt->execute(array(':CliId'=>$flota['cliid']));
			$n=$stmt->rowCount();
            if($n>0){
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $datos['data'][]=array(
                        'id'=>(int)$row['idgrupo'],
                        'nombre'=>$row['grupo'],
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

    function FnListarClienteFlotas($conmy, $cliid) {
        try {
            $datos = array();
            $stmt = $conmy->prepare("select idgrupo, grupo from man_grupos where idcliente=:CliId and estado=2;");
            $stmt->execute(array(':CliId'=>$cliid));
            if($stmt->rowCount()>0){
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $datos[]=array(
                        'id'=>$row['idgrupo'],
                        'nombre'=>$row['grupo']
                    );
                }
            }            
            return $datos;
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage().$msg);
        }
    }

    /*
    function FnListarClienteFlotas2($conmy, $cliid, $nombre) {
        try {
            $datos = array();
            $query="";

            if(!empty($nombre)){
                $query=" and grupo like '%".$nombre."%'";
            }

            $stmt = $conmy->prepare("select idgrupo, grupo from man_grupos where idcliente=:CliId and estado=2".$query.";");
            $stmt->execute(array(':CliId'=>$cliid));
            if($stmt->rowCount()>0){
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $datos[]=array(
                        'id'=>$row['idgrupo'],
                        'nombre'=>$row['grupo']
                    );
                }
            }            
            return $datos;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage().$msg);
        }
    }*/
?>