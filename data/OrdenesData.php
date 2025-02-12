<?php 

    function FnAgregarOrden($conmy, $orden) {
        try {
            $stmt = $conmy->prepare("CALL spman_agregarorden(:_equid, :_tipid, :_famid, :_sisid, :_oriid, :_actid, :_cliid, :_nombre, :_equnombre, :_tipnombre, :_famnombre, :_sisnombre, :_orinombre, 
            :_fecha, :_tiptrabajo, :_actnombre, :_trabajos, :_observaciones, :_equkm, :_equhm, :_supervisor, :_clicontacto, :_usuario, @_id)");
            $stmt->bindParam(':_equid', $orden['equid'], PDO::PARAM_INT);
            $stmt->bindParam(':_tipid', $orden['tipid'], PDO::PARAM_INT);
            $stmt->bindParam(':_famid', $orden['famid'], PDO::PARAM_INT);
            $stmt->bindParam(':_sisid', $orden['sisid'], PDO::PARAM_INT);
            $stmt->bindParam(':_oriid', $orden['oriid'], PDO::PARAM_INT);
            $stmt->bindParam(':_actid', $orden['actid'], PDO::PARAM_INT);
            $stmt->bindParam(':_cliid', $orden['cliid'], PDO::PARAM_INT);
            $stmt->bindParam(':_nombre', $orden['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':_equnombre', $orden['equnombre'], PDO::PARAM_STR);
            $stmt->bindParam(':_tipnombre', $orden['tipnombre'], PDO::PARAM_STR);
            $stmt->bindParam(':_famnombre', $orden['famnombre'], PDO::PARAM_STR);
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

            $stmt = $conmy->prepare("select idot, idactivo, idtipoot, famid, idsistema, idorigen, idactividad, idcliente, ot, activo, tipoot, fam_nombre, sistema, origen, fechainicial, 
            tipotrabajo, actividad, descripcion, observaciones, km, hm, supervisor, contacto, estado FROM man_ots WHERE idot=:Id and idcliente=:CliId;");
            $stmt->execute(array(':Id'=>$id, ':CliId'=>$cliid));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                $datos['id']=$row['idot'];
                $datos['equid']=$row['idactivo'];
                $datos['tipid']=$row['idtipoot'];
                $datos['famid']=$row['famid'];
                $datos['sisid']=$row['idsistema'];
                $datos['oriid']=$row['idorigen'];
                $datos['actid']=$row['idactividad'];
                $datos['cliid']=$row['idcliente'];
                $datos['nombre']=$row['ot'];
                $datos['equnombre']=$row['activo'];
                $datos['tipnombre']=$row['tipoot'];
                $datos['famnombre']=$row['fam_nombre'];
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

    function FnReporteOrdenes($conmy, $search){
        try {
            $datos = array();
            $stmt = $conmy->prepare("select idot, ot, activo, tipoot, fam_nombre, sistema, origen, fechainicial, tipotrabajo, actividad, descripcion, observaciones, km, hm, supervisor, contacto, estado from man_ots where idcliente=:CliId and fechainicial between :FechaInicial and :FechaFinal;");
            $stmt->execute(array(':CliId'=>$search['cliid'], ':FechaInicial'=>$search['fechainicial'], ':FechaFinal'=>$search['fechafinal']));
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $datos[]=array(
                    'id'=>$row['idot'],
                    'nombre'=>$row['ot'],
                    'equcodigo'=>$row['activo'],
                    'tipnombre'=>$row['tipoot'],
                    'famnombre'=>$row['fam_nombre'],
                    'sisnombre'=>$row['sistema'],
                    'orinombre'=>$row['origen'],
                    'fecha'=>$row['fechainicial'],
                    'tiptrabajo'=>$row['tipotrabajo'],
                    'actnombre'=>$row['actividad'],
                    'descripcion'=>$row['descripcion'],
                    'observaciones'=>$row['observaciones'],
                    'equkm'=>$row['km'],
                    'equhm'=>$row['hm'],
                    'supervisor'=>$row['supervisor'],
                    'clicontacto'=>$row['contacto'],
                    'estado'=>$row['estado']
                );
            }            
            return $datos;
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    function FnDiferenciaFechas($fechainicial, $fechafinal) {
        try {
            $date1 = new DateTime($fechainicial);
            $date2 = new DateTime($fechafinal);
            $diferencia = $date1->diff($date2);
            return $diferencia->days;  
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    function FnReporteOrdenesTareos($conmy, $search){
        try {
            $datos = array();
            $stmt = $conmy->prepare("select o.idot, o.ot, o.activo, o.tipoot, o.fam_nombre, o.sistema, o.origen, o.fechainicial, o.tipotrabajo, o.actividad, o.descripcion, o.observaciones, o.km, o.hm, o.supervisor, o.contacto, o.estado, t.personal, t.ingreso, t.refrigerio1, t.refrigerio2, t.salida, t.tmin from man_ots o left join man_tareos t on o.idot=t.idot where o.idcliente=:CliId and o.fechainicial between :FechaInicial and :FechaFinal;");
            $stmt->execute(array(':CliId'=>$search['cliid'], ':FechaInicial'=>$search['fechainicial'], ':FechaFinal'=>$search['fechafinal']));
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $datos[]=array(
                    'id'=>$row['idot'],
                    'nombre'=>$row['ot'],
                    'equcodigo'=>$row['activo'],
                    'tipnombre'=>$row['tipoot'],
                    'famnombre'=>$row['fam_nombre'],
                    'sisnombre'=>$row['sistema'],
                    'orinombre'=>$row['origen'],
                    'fecha'=>$row['fechainicial'],
                    'tiptrabajo'=>$row['tipotrabajo'],
                    'actnombre'=>$row['actividad'],
                    'descripcion'=>$row['descripcion'],
                    'observaciones'=>$row['observaciones'],
                    'equkm'=>$row['km'],
                    'equhm'=>$row['hm'],
                    'supervisor'=>$row['supervisor'],
                    'clicontacto'=>$row['contacto'],
                    'estado'=>$row['estado'],
                    'tecnico'=>$row['personal'],
                    'ing_trabajo'=>$row['ingreso'],
                    'ing_refrigerio'=>$row['refrigerio1'],
                    'sal_refrigerio'=>$row['refrigerio2'],
                    'sal_trabajo'=>$row['salida'],
                    'minutos'=>$row['tmin']
                );
            }            
            return $datos;
        } catch (PDOException $ex) {
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

                if($orden['tipid']>0){
                    $query.=" and idtipoot=".$orden['tipid'];
                }

                if($orden['famid']>0){
                    $query.=" and famid=".$orden['famid'];
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

                if(!empty($orden['actnombre'])){
                    $query.=" and actividad like '%".$orden['actnombre']."%'";
                }

                $query.=" and fechainicial between '".$orden['fechainicial']."' and '".$orden['fechafinal']."'";
                
                $query.=" order by idot desc limit ".$orden['pagina'].", 15";
            }

            $stmt = $conmy->prepare("select idot, idcliente, ot, activo, tipoot, fechainicial, actividad, km, estado from man_ots where idcliente=:CliId".$query.";");
            $stmt->execute(array(':CliId'=>$orden['cliid']));
			$n=$stmt->rowCount();
            if($n>0){
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $datos['data'][]=array(
                        'id'=>(int)$row['idot'],
                        'cliid'=>$row['idcliente'],
                        'fecha'=>$row['fechainicial'],                        
                        'nombre'=>$row['ot'],
                        'equnombre'=>$row['activo'],
                        'tipnombre'=>$row['tipoot'],
                        'actnombre'=>$row['actividad'],
                        'equkm'=>$row['km'],
                        'estado'=>(int)$row['estado']
                    );
                }
                $datos['pag']=$n;
            }            
            return $datos;
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
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
            $stmt=$conmy->prepare("update man_ots set idactivo=:EquId, famid=:FamId, idsistema=:SisId, idorigen=:OriId, idactividad=:ActId, activo=:EquNombre, fam_nombre=:FamNombre, sistema=:SisNombre, origen=:OriNombre, fechainicial=:Fecha, actividad=:ActNombre, descripcion=:Trabajos, observaciones=:Observaciones, km=:EquKm, hm=:EquHm, supervisor=:Supervisor, contacto=:CliContacto, actualizacion=:Actualizacion where idot=:Id and idcliente=:CliId and estado in(1,2);");
            $stmt->execute(array(':EquId'=>$orden['equid'], ':FamId'=>$orden['famid'], ':SisId'=>$orden['sisid'], ':OriId'=>$orden['oriid'], ':ActId'=>$orden['actid'], ':EquNombre'=>$orden['equnombre'], ':FamNombre'=>$orden['famnombre'], ':SisNombre'=>$orden['sisnombre'], ':OriNombre'=>$orden['orinombre'], ':Fecha'=>$orden['fecha'], ':ActNombre'=>$orden['actnombre'], ':Trabajos'=>$orden['trabajos'], ':Observaciones'=>$orden['observaciones'], ':EquKm'=>$orden['equkm'], ':EquHm'=>$orden['equhm'], ':Supervisor'=>$orden['supervisor'], ':CliContacto'=>$orden['clicontacto'], ':Actualizacion'=>$orden['usuario'], ':Id'=>$orden['id'], ':CliId'=>$orden['cliid']));
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