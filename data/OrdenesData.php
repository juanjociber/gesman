<?php 

    function FnAgregarOrden($conmy, $orden) {
        try {
            $stmt = $conmy->prepare("CALL spman_agregarorden(:_equid, :_tipid, :_sisid, :_oriid, :_actid, :_cliid, :_nombre, :_equcodigo, :_tipnombre, :_sisnombre, :_orinombre, 
            :_fecha, :_tiptrabajo, :_actnombre, :_trabajos, :_observaciones, :_equkm, :_equhm, :_supervisor, :_clicontacto, :_usuario, @_id)");
            $stmt->bindParam(':_equid', $orden['equid'], PDO::PARAM_INT);
            $stmt->bindParam(':_tipid', $orden['tipid'], PDO::PARAM_INT);
            $stmt->bindParam(':_sisid', $orden['sisid'], PDO::PARAM_INT);
            $stmt->bindParam(':_oriid', $orden['oriid'], PDO::PARAM_INT);
            $stmt->bindParam(':_actid', $orden['actid'], PDO::PARAM_INT);
            $stmt->bindParam(':_cliid', $orden['cliid'], PDO::PARAM_INT);
            $stmt->bindParam(':_nombre', $orden['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':_equcodigo', $orden['equcodigo'], PDO::PARAM_STR);
            $stmt->bindParam(':_tipnombre', $orden['tipnombre'], PDO::PARAM_STR);
            $stmt->bindParam(':_sisnombre', $orden['sisnombre'], PDO::PARAM_STR);
            $stmt->bindParam(':_orinombre', $orden['orinombre'], PDO::PARAM_STR);
            $stmt->bindParam(':_fecha', $orden['fecha'], PDO::PARAM_STR);
            $stmt->bindParam(':_tiptrabajo', $orden['tiptrabajo'], PDO::PARAM_STR);
            $stmt->bindParam(':_actnombre', $orden['actnombre'], PDO::PARAM_STR);
            $stmt->bindParam(':_trabajos', $orden['trabajos'], PDO::PARAM_STR);
            $stmt->bindParam(':_observaciones', $orden['observaciones'], PDO::PARAM_STR);
            $stmt->bindParam(':_equkm', $orden['equkm'], PDO::PARAM_INT);
            $stmt->bindParam(':_equhm', $orden['equhm'], PDO::PARAM_INT);
            $stmt->bindParam(':_supervisor', $orden['supervisor'], PDO::PARAM_STR);
            $stmt->bindParam(':_clicontacto', $orden['clicontacto'], PDO::PARAM_STR);
            $stmt->bindParam(':_usuario', $orden['usuario'], PDO::PARAM_STR);
            $stmt->execute();
            $stmt=$conmy->query("SELECT @_id as id");
            $row=$stmt->fetch(PDO::FETCH_ASSOC);
            $id=$row['id'];
            return $id;
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());//sera propagado al catch(Exception $ex) del nivel superior.
        }
    }

    function FnBuscarOrden($conmy, $cliid, $id) {
        try {
            $datos=array();

            $stmt = $conmy->prepare("select idot, idactivo, idtipoot, idsistema, idorigen, idactividad, idcliente, ot, activo, tipoot, sistema, origen, fechainicial, 
            tipotrabajo, actividad, descripcion, observaciones, km, hm, supervisor, contacto, estado FROM man_ots WHERE idot=:Id and idcliente=:CliId;");
            $stmt->execute(array(':Id'=>$id, ':CliId'=>$cliid));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                $datos['id']=$row['idot'];
                $datos['equid']=$row['idactivo'];
                $datos['tipid']=$row['idtipoot'];
                $datos['sisid']=$row['idsistema'];
                $datos['oriid']=$row['idorigen'];
                $datos['actid']=$row['idactividad'];
                $datos['cliid']=$row['idcliente'];
                $datos['nombre']=$row['ot'];
                $datos['equcodigo']=$row['activo'];
                $datos['tipnombre']=$row['tipoot'];
                $datos['sisnombre']=$row['sistema'];
                $datos['orinombre']=$row['origen'];
                $datos['fecha']=$row['fechainicial'];
                $datos['tiptra']=$row['tipotrabajo'];
                $datos['actnombre']=$row['actividad'];
                $datos['trabajos']=$row['descripcion'];
                $datos['observaciones']=$row['observaciones'];
                $datos['equkm']=$row['km'];
                $datos['equhm']=$row['hm'];
                $datos['supervisor']=$row['supervisor'];
                $datos['clicontacto']=$row['contacto'];
                $datos['estado']=$row['estado'];
            }            
            return $datos;
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    function FnBuscarOrdenes($conmy, $orden) {
        try {
            $datos = array('data'=>array(), 'pag'=>0);
            $query = "";

            if(!empty($orden['nombre'])){
                $query=" and ot='".$orden['nombre']."'";
            }else{
                
                if(empty($orden['fechainicial']) || empty($orden['fechafinal'])){throw new Exception("Las fechas son incorrectas.");}

                if($orden['equid']>0){
                    $query.=" and idactivo=".$orden['equid'];
                }

                if($orden['sisid']>0){
                    $query.=" and idsistema=".$orden['sisid'];
                }

                if($orden['oriid']>0){
                    $query.=" and idorigen=".$orden['oriid'];
                }

                if($orden['estado']>0){
                    $query.=" and estado=".$orden['estado'];
                }

                if(!empty($orden['actividad'])){
                    $query.=" and actividad like '%".$orden['actividad']."%'";
                }

                $query.=" and fechainicial between '".$orden['fechainicial']."' and '".$orden['fechafinal']."'";
                
                $query.=" limit ".$orden['pagina'].", 15";
            }

            $stmt = $conmy->prepare("select idot, ot, activo, tipoot, fechainicial, actividad, km, estado from man_ots where idcliente=:CliId".$query.";");
            $stmt->execute(array(':CliId'=>$orden['cliid']));
			$n=$stmt->rowCount();
            if($n>0){
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $datos['data'][]=array(
                        'id'=>(int)$row['idot'],
                        'fecha'=>$row['fechainicial'],                        
                        'nombre'=>$row['ot'],
                        'equcodigo'=>$row['activo'],
                        'tipnombre'=>$row['tipoot'],
                        'actnombre'=>$row['actividad'],
                        'equkm'=>$row['km'],
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

    function FnBuscarOrdenArchivos($conmy, $refid, $tabla) {
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
            throw new Exception($e->getMessage());
        }
    }

    function FnFinalizarOrden($conmy, $orden) {
        try {
            $res=false;
            $stmt = $conmy->prepare("update man_ots set estado=3, actualizacion=:Actualizacion where idot=:Id and idcliente=:CliId and estado in(1,2);");
            $stmt->execute(array(':Actualizacion'=>$orden['usuario'], ':Id'=>$orden['id'], ':CliId'=>$orden['cliid']));
            if($stmt->rowCount()>0){
                $res=true;
            }
            return $res;         
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    function FnModificarOrden($conmy, $orden) {
        try {
            $res=false;
            $stmt=$conmy->prepare("update man_ots set idsistema=:SisId, idorigen=:OriId, idactividad=:ActId, sistema=:SisNombre, origen=:OriNombre, fechainicial=:Fecha, actividad=:Actividades, descripcion=:Trabajos, observaciones=:Observaciones, km=:EquKm, hm=:EquHm, supervisor=:Supervisor, contacto=:CliContacto, actualizacion=:Actualizacion where idot=:Id and idcliente=:CliId and estado in(1,2);");
            $stmt->execute(array(':SisId'=>$orden['sisid'], ':OriId'=>$orden['oriid'], ':ActId'=>$orden['actid'], ':SisNombre'=>$orden['sisnombre'], ':OriNombre'=>$orden['orinombre'], ':Fecha'=>$orden['fecha'], ':Actividades'=>$orden['actividades'], ':Trabajos'=>$orden['trabajos'], ':Observaciones'=>$orden['observaciones'], ':EquKm'=>$orden['equkm'], ':EquHm'=>$orden['equhm'], ':Supervisor'=>$orden['supervisor'], ':CliContacto'=>$orden['clicontacto'], ':Actualizacion'=>$orden['usuario'], ':Id'=>$orden['id'], ':CliId'=>$orden['cliid']));
            if($stmt->rowCount()>0){
                $res=true;
            }
            return $res; 
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    //Solo cuando se agrega un Tareo.
    function FnModificarOrden2($conmy, $orden) {
        try {
            $res=false;
            $stmt = $conmy->prepare("update man_ots set estado=2, actualizacion=:Actualizacion where idot=:Id and idcliente=:CliId and estado in(1,2);");
            $stmt->execute(array(':Actualizacion'=>$orden['usuario'], ':Id'=>$orden['id'], ':CliId'=>$orden['cliid']));
            if($stmt->rowCount()>0){
                $res=true;
            }
            return $res;         
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    function FnListarClienteSistemas($conmy, $cliid, $nombre) {
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
    }

    function FnListarClienteOrigenes($conmy, $cliid, $nombre) {
        try {
            $datos = array();
            $query="";

            if(!empty($nombre)){
                $query=" and origen like '%".$nombre."%'";
            }

            $stmt = $conmy->prepare("select idorigen, origen from man_origenes where idcliente=:CliId and estado=2".$query.";");
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

    function FnListarClienteContactos($conmy, $cliid, $nombre) {
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
    }

    function FnBuscarOrdenTareos($conmy, $ordid) {
        try {
            $datos = array();

            $stmt = $conmy->prepare("select idtareo, idpersonal, personal, ingreso, salida, tmin from man_tareos where idot=:OrdId;");
            $stmt->execute(array(':OrdId'=>$ordid));
            if($stmt->rowCount()>0){
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $datos[]=array(
                        'id'=>$row['idtareo'],
                        'perid'=>$row['idpersonal'],
                        'pernombre'=>$row['personal'],                        
                        'ingreso'=>$row['ingreso'],
                        'salida'=>$row['salida'],
                        'minutos'=>$row['tmin']
                    );
                }
            }            
            return $datos;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage().$msg);
        }
    }
    
    function FnAgregarOrdenTareo($conmy, $tareo) {
        try {
            $res=false;
            $stmt = $conmy->prepare("insert into man_tareos(idot, idpersonal, personal, ingreso, salida, tmin, estado, creacion, actualizacion) values(:OrdId, :PerId, :PerNombre, :Ingreso, :Salida, :Minutos, :Estado, :Creacion, :Actualizacion);");
            $stmt->execute(array(':OrdId'=>$tareo['ordid'], ':PerId'=>$tareo['perid'], ':PerNombre'=>$tareo['pernombre'], ':Ingreso'=>$tareo['ingreso'], ':Salida'=>$tareo['salida'], ':Minutos'=>$tareo['minutos'], ':Estado'=>$tareo['estado'], ':Creacion'=>$tareo['usuario'], ':Actualizacion'=>$tareo['usuario']));
            if($stmt->rowCount()>0){
                $res=true;
            }
            return $res;         
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    function FnEliminarOrdenTareo($conmy, $tareo) {
        try {
            $res=false;
            $stmt = $conmy->prepare("delete from man_tareos where idtareo=:Id and idot=:OrdId;");
            $stmt->execute(array(':Id'=>$tareo['id'], ':OrdId'=>$tareo['ordid']));
            if($stmt->rowCount()>0){
                $res=true;
            }
            return $res;         
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }
?>