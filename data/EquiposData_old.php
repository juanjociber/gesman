<?php 
    function FnAgregarEquipo($conmy, $equipo) {
        try {
            $id=0;
            $stmt=$conmy->prepare("insert into man_activos(idcliente, codigo, marca, modelo, serie, datos, km, hm, placa, creacion, actualizacion, famid, ownid, fam_nombre) 
            values(:CliId, :Nombre, :Marca, :Modelo, :Serie, :Datos, :Km, :Hm, :Placa, :Creacion, :Actualizacion, :FamId, :OwnId, :FamNombre);");
            $stmt->execute(array(
                ':CliId'=>$equipo['cliid'],
                ':Nombre'=>$equipo['nombre'],
                ':Marca'=>$equipo['marca'],
                ':Modelo'=>$equipo['modelo'],
                ':Serie'=>$equipo['serie'],
                ':Datos'=>$equipo['datos'],
                ':Km'=>$equipo['km'],
                ':Hm'=>$equipo['hm'],
                ':Placa'=>$equipo['placa'],
                ':Creacion'=>$equipo['usuario'],
                ':Actualizacion'=>$equipo['usuario'],
                ':FamId'=>$equipo['famid'],
                ':OwnId'=>$equipo['ownid'],
                ':FamNombre'=>$equipo['famnombre']
            ));
            $id=$conmy->lastInsertId();
            return $id;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    function FnModificarEquipo($conmy, $equipo) {
        try {
            $res=false;
            $stmt=$conmy->prepare("update man_activos set marca=:Marca, modelo=:Modelo, serie=:Serie, placa=:Placa, ubicacion=:Ubicacion, datos=:Datos, actualizacion=:Actualizacion where idactivo=:Id and idcliente=:CliId;");
            $stmt->execute(array(':Marca'=>$equipo['marca'], ':Modelo'=>$equipo['modelo'], ':Serie'=>$equipo['serie'], ':Placa'=>$equipo['placa'], ':Ubicacion'=>$equipo['ubicacion'], ':Datos'=>$equipo['datos'], ':Actualizacion'=>$equipo['usuario'], ':Id'=>$equipo['id'], ':CliId'=>$equipo['cliid']));
            if($stmt->rowCount()>0){
                $res=true;
            }
            return $res; 
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    function FnModificarEquipoRuta($conmy, $equipo) {
        try {
            $res=false;
            $stmt=$conmy->prepare("update man_activos set famid=:FamId, ownid=:OwnId, fam_nombre=:FamNombre, actualizacion=:Actualizacion where idactivo=:Id and idcliente=:CliId;");
            $stmt->execute(array(':FamId'=>$equipo['famid'], ':OwnId'=>$equipo['ownid'], ':FamNombre'=>$equipo['famnombre'], ':Actualizacion'=>$equipo['usuario'], ':Id'=>$equipo['id'], ':CliId'=>$equipo['cliid']));
            if($stmt->rowCount()>0){
                $res=true;
            }
            return $res; 
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    function FnAnularEquipo($conmy, $equipo) {
        try {
            $res=false;
            $stmt=$conmy->prepare("update man_activos set estado=1, actualizacion=:Actualizacion where idactivo=:Id and idcliente=:CliId;");
            $stmt->execute(array(':Actualizacion'=>$equipo['usuario'], ':Id'=>$equipo['id'], ':CliId'=>$equipo['cliid']));
            if($stmt->rowCount()>0){
                $res=true;
            }
            return $res; 
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    function FnValidarEquipoDuplicado($conmy, $search) {
        try {
            $cantidad=0;
            $stmt=$conmy->prepare("select count(*) as cantidad from man_activos where idactivo!=:Id and idcliente=:CliId and codigo=:Nombre;");
            $stmt->execute(array(':Id'=>$search['id'], ':CliId'=>$search['cliid'], ':Nombre'=>$search['nombre']));
            $row=$stmt->fetch();
            if($row){
                $cantidad=$row['cantidad'];
            }
            return $cantidad;
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    function FnBuscarEquipo($conmy, $cliid, $id) {
        try {
            $datos=array();
            $stmt = $conmy->prepare("select idactivo, codigo, marca, modelo, serie, placa, ubicacion, datos, km, hm, archivo, famid, ownid, fam_nombre, estado FROM man_activos where idactivo=:Id and idcliente=:CliId;");
            $stmt->execute(array(':Id'=>$id, ':CliId'=>$cliid));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);            
            if ($row) {
                $datos['id']=$row['idactivo'];
                $datos['nombre']=$row['codigo'];
                $datos['marca']=$row['marca'];
                $datos['modelo']=$row['modelo'];
                $datos['serie']=$row['serie'];
                $datos['placa']=$row['placa'];
                $datos['ubicacion']=$row['ubicacion'];
                $datos['datos']=$row['datos'];
                $datos['km']=$row['km'];
                $datos['hm']=$row['hm'];
                $datos['archivo']=$row['archivo'];
                $datos['famid']=$row['famid'];
                $datos['ownid']=$row['ownid'];
                $datos['famnombre']=$row['fam_nombre'];
                $datos['estado']=$row['estado'];
            }            
            return $datos;
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    function FnBuscarEquipos($conmy, $search) {
        try {
            $datos = array('data'=>array(), 'pag'=>0);
            $query = "";

            if(!empty($search['nombre'])){
                $query=" and codigo like'%".$search['nombre']."%'";
            }else{

                if($search['famid']>0){
                    $query.=" and famid=".$search['famid'];
                }

                if($search['estado']>0){
                    $query.=" and estado=".$search['estado'];
                }
            }

            $query.=" limit ".$search['pagina'].", 15";

            $stmt = $conmy->prepare("select idactivo, codigo, marca, modelo, km, hm, estado, famid, ownid, fam_nombre from man_activos where idcliente=:CliId".$query.";");
            $stmt->execute(array(':CliId'=>$search['cliid']));
			$n=$stmt->rowCount();
            if($n>0){
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $datos['data'][]=array(
                        'id'=>(int)$row['idactivo'],
                        'nombre'=>$row['codigo'],
                        'marca'=>$row['marca'],
                        'modelo'=>$row['modelo'],
                        'km'=>$row['km'],
                        'hm'=>$row['hm'],
                        'estado'=>(int)$row['estado'],
                        'famid'=>$row['famid'],
                        'ownid'=>$row['ownid'],
                        'famnombre'=>$row['fam_nombre']
                    );
                }
                $datos['pag']=$n;
            }            
            return $datos;
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    function FnListarEquipos($conmy, $search) {
        try {
            $datos=array();

            $query=" and estado=2";
            if(!empty($search['famid'])){
                $query.=" and famid=".$search['famid'];
            }
            $stmt=$conmy->prepare("select idactivo, codigo, km, hm, famid, fam_nombre from man_activos where idcliente=:CliId".$query." and codigo like :Nombre limit 15;");
            $stmt->execute(array(':CliId'=>$search['cliid'], ':Nombre'=>'%'.$search['nombre'].'%'));
            while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                $datos[]=array(
                    'id'=>$row['idactivo'],
                    'nombre'=>$row['codigo'],
                    'km'=>$row['km'],
                    'hm'=>$row['hm'],
                    'famid'=>$row['famid'],
                    'famnombre'=>$row['fam_nombre']
                );
            }
            return $datos;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    function FnAgregarEquipoImagen($conmy, $equipo) {
        try {
            $res=false;
            $stmt=$conmy->prepare("update man_activos set archivo=:Archivo, actualizacion=:Actualizacion where idactivo=:Id and idcliente=:CliId;");
            $stmt->execute(array(':Archivo'=>$equipo['imagen'], ':Actualizacion'=>$equipo['usuario'], ':Id'=>$equipo['id'], ':CliId'=>$equipo['cliid']));
            if($stmt->rowCount()>0){
                $res=true;
            }
            return $res; 
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    function FnBuscarEquipoComponentes($conmy, $search) {
        try {
            $datos=array();
            $stmt=$conmy->prepare("select idactivo, codigo, marca, modelo, serie, ownid, fam_nombre, estado from man_activos where idcliente=:CliId and ownid=:OwnId;");
            $stmt->execute(array(':CliId'=>$search['cliid'], ':OwnId'=>$search['ownid']));
            while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                $datos[]=array(
                    'id'=>$row['idactivo'],
                    'ownid'=>$row['ownid'],
                    'nombre'=>$row['codigo'],
                    'famnombre'=>$row['fam_nombre'],
                    'marca'=>$row['marca'],
                    'modelo'=>$row['modelo'],
                    'serie'=>$row['serie'],
                    'estado'=>(int)$row['estado']
                );
            }
            return $datos;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    function FnListarClienteEquipos($conmy, $cliid) {
        try {
            $datos=array();
            $stmt=$conmy->prepare("select idactivo, codigo, estado, ownid, fam_nombre from man_activos where idcliente=:CliId;");
            $stmt->execute(array(':CliId'=>$cliid));
            while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                $datos[]=array(
                    'id'=>$row['idactivo'],
                    'nombre'=>$row['codigo'],
                    'estado'=>$row['estado'],
                    'ownid'=>$row['ownid'],
                    'famnombre'=>$row['fam_nombre']
                );
            }
            return $datos;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    //Listar equipos diferentes al equipo padre. cambiar ruta de equipos.
    function FnListarFamiliaEquipos($conmy, $search) {
        try {
            $datos=array();
            $stmt=$conmy->prepare("select idactivo, codigo from man_activos where idactivo!=:Id and idcliente=:CliId and famid=:FamId and estado=2 and codigo like :Nombre limit 15;");
            $stmt->execute(array(':Id'=>$search['id'], ':CliId'=>$search['cliid'], ':FamId'=>$search['famid'], ':Nombre'=>'%'.$search['nombre'].'%'));
            while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                $datos[]=array(
                    'id'=>$row['idactivo'],
                    'nombre'=>$row['codigo']
                );
            }
            return $datos;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

?>