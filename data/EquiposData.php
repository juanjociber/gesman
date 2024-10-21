<?php 
    function FnAgregarEquipo($conmy, $equipo) {
        try {
            $id=0;
            $stmt=$conmy->prepare("insert into man_activos(idcliente, idgrupo, codigo, activo, grupo, marca, modelo, serie, anio, fabricante, procedencia, caracteristicas, ubicacion, km, hm, motor, transmision, diferencial, placa, creacion, actualizacion) 
            values(:CliId, :FloId, :Codigo, :Nombre, :FloNombre, :Marca, :Modelo, :Serie, :Anio, :Fabricante, :Procedencia, :Datos, :Ubicacion, :Km, :Hm, :Motor, :Transmision, :Diferencial, :Placa, :Creacion, :Actualizacion);");
            $stmt->execute(array(
                ':CliId'=>$equipo['cliid'],
                ':FloId'=>$equipo['floid'],
                ':Codigo'=>$equipo['codigo'],
                ':Nombre'=>$equipo['nombre'],
                ':FloNombre'=>$equipo['flonombre'],
                ':Marca'=>$equipo['marca'],
                ':Modelo'=>$equipo['modelo'],
                ':Serie'=>$equipo['serie'],
                ':Anio'=>$equipo['anio'],
                ':Fabricante'=>$equipo['fabricante'],
                ':Procedencia'=>$equipo['procedencia'],
                ':Datos'=>$equipo['datos'],
                ':Ubicacion'=>$equipo['ubicacion'],
                ':Km'=>$equipo['km'],
                ':Hm'=>$equipo['hm'],
                ':Motor'=>$equipo['motor'],
                ':Transmision'=>$equipo['transmision'],
                ':Diferencial'=>$equipo['diferencial'],
                ':Placa'=>$equipo['placa'],
                ':Creacion'=>$equipo['usuario'],
                ':Actualizacion'=>$equipo['usuario']
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
            $stmt=$conmy->prepare("update man_activos set idgrupo=:FloId, activo=:Nombre, grupo=:FloNombre, marca=:Marca, modelo=:Modelo, serie=:Serie, anio=:Anio, fabricante=:Fabricante, procedencia=:Procedencia, caracteristicas=:Datos, ubicacion=:Ubicacion, motor=:Motor, transmision=:Transmision, diferencial=:Diferencial, placa=:Placa, actualizacion=:Actualizacion where idactivo=:Id and idcliente=:CliId;");
            $stmt->execute(array(':FloId'=>$equipo['floid'], ':Nombre'=>$equipo['nombre'], ':FloNombre'=>$equipo['flonombre'], ':Marca'=>$equipo['marca'], ':Modelo'=>$equipo['modelo'], ':Serie'=>$equipo['serie'], ':Anio'=>$equipo['anio'], ':Fabricante'=>$equipo['fabricante'], ':Procedencia'=>$equipo['procedencia'], ':Datos'=>$equipo['datos'], ':Ubicacion'=>$equipo['ubicacion'], ':Motor'=>$equipo['motor'], ':Transmision'=>$equipo['transmision'], ':Diferencial'=>$equipo['diferencial'], ':Placa'=>$equipo['placa'], ':Actualizacion'=>$equipo['usuario'], ':Id'=>$equipo['id'], ':CliId'=>$equipo['cliid']));
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

    function FnValidarEquipoDuplicado($conmy, $cliid, $codigo) {
        try {
            $cantidad=0;
            $stmt=$conmy->prepare("select count(*) as cantidad from man_activos where codigo=:Codigo and idcliente=:CliId;");
            $stmt->execute(array(':Codigo'=>$codigo, ':CliId'=>$cliid));
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
            $stmt = $conmy->prepare("select idactivo, idgrupo, codigo, activo, grupo, marca, modelo, serie, anio, fabricante, procedencia, caracteristicas, 
            ubicacion, km, hm, motor, transmision, diferencial, placa, archivo, estado FROM man_activos where idactivo=:Id and idcliente=:CliId;");
            $stmt->execute(array(':Id'=>$id, ':CliId'=>$cliid));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);            
            if ($row) {
                $datos['id']=$row['idactivo'];
                $datos['floid']=$row['idgrupo'];
                $datos['codigo']=$row['codigo'];
                $datos['nombre']=$row['activo'];
                $datos['flonombre']=$row['grupo'];
                $datos['marca']=$row['marca'];
                $datos['modelo']=$row['modelo'];
                $datos['serie']=$row['serie'];
                $datos['anio']=$row['anio'];
                $datos['fabricante']=$row['fabricante'];
                $datos['procedencia']=$row['procedencia'];
                $datos['datos']=$row['caracteristicas'];
                $datos['ubicacion']=$row['ubicacion'];
                $datos['km']=$row['km'];
                $datos['hm']=$row['hm'];
                $datos['motor']=$row['motor'];
                $datos['transmision']=$row['transmision'];
                $datos['diferencial']=$row['diferencial'];
                $datos['placa']=$row['placa'];
                $datos['archivo']=$row['archivo'];
                $datos['estado']=$row['estado'];
            }            
            return $datos;
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    function FnBuscarEquipos($conmy, $equipo) {
        try {
            $datos = array('data'=>array(), 'pag'=>0);
            $query = "";

            if(!empty($equipo['nombre'])){
                $query=" and concat(codigo, activo) like'%".$equipo['nombre']."%'";
            }else{

                if($equipo['floid']>0){
                    $query.=" and idgrupo=".$equipo['floid'];
                }

                if($equipo['estado']>0){
                    $query.=" and estado=".$equipo['estado'];
                }
            }

            $query.=" limit ".$equipo['pagina'].", 2";

            $stmt = $conmy->prepare("select idactivo, codigo, grupo, marca, modelo, km, hm, estado from man_activos where idcliente=:CliId".$query.";");
            $stmt->execute(array(':CliId'=>$equipo['cliid']));
			$n=$stmt->rowCount();
            if($n>0){
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $datos['data'][]=array(
                        'id'=>(int)$row['idactivo'],
                        'codigo'=>$row['codigo'],                        
                        'flonombre'=>$row['grupo'],
                        'marca'=>$row['marca'],
                        'modelo'=>$row['modelo'],
                        'km'=>$row['km'],
                        'hm'=>$row['hm'],
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

    /*function FnBuscarEquipoArchivos($conmy, $refid, $tabla) {
        try {
            $datos = array();

            $stmt = $conmy->prepare("select id, nombre, titulo, descripcion, tipo from tblarchivos where refid=:RefId and tabla=:Tabla;");
            $stmt->execute(array(':RefId'=>$refid, ':Tabla'=>$tabla));
            if($stmt->rowCount()>0){
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $datos[]=array(
                        'id'=>$row['id'],
                        'nombre'=>$row['nombre'],                        
                        'titulo'=>$row['titulo'],
                        'descripcion'=>$row['descripcion'],
                        'tipo'=>$row['tipo']
                    );
                }
            }            
            return $datos;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage().$msg);
        }
    }*/

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

    /*function FnListarClienteFlotas($conmy, $cliid) {
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
        } catch (PDOException $e) {
            throw new Exception($e->getMessage().$msg);
        }
    }*/

    /*function FnListarClienteFlotas2($conmy, $cliid, $nombre) {
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