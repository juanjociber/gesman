<?php
    function FnAgregarArchivo($conmy, $archivo) {
        try {
            $id=0;
            $stmt=$conmy->prepare("insert into tblarchivos(refid, tabla, nombre, titulo, descripcion, tipo, creacion) 
            values(:RefId, :Tabla, :Nombre, :Titulo, :Descripcion, :Tipo, :Creacion);");
            $stmt->execute(array(
                ':RefId'=>$archivo['refid'],
                ':Tabla'=>$archivo['tabla'],
                ':Nombre'=>$archivo['nombre'],
                ':Titulo'=>$archivo['titulo'],
                ':Descripcion'=>$archivo['descripcion'],
                ':Tipo'=>$archivo['tipo'],
                ':Creacion'=>$archivo['usuario']
            ));
            $id=$conmy->lastInsertId();
            return $id;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    function FnEliminarArchivo($conmy, $archivo) {
        try {
            $res=false;
            $stmt = $conmy->prepare("delete from tblarchivos where id=:Id");
            $stmt->execute(array(':Id'=>$archivo['id']));
            if($stmt->rowCount()>0){
                $res=true;
            }
            return $res;         
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    function FnBuscarArchivos($conmy, $archivo) {
        try {
            $datos=array();
            $query="";

            if(!empty($archivo['tipo'])){
                $query.=" and tipo='".$archivo['tipo']."'";
            }

            $stmt=$conmy->prepare("select id, refid, tabla, nombre, titulo, descripcion, tipo from tblarchivos where refid=:RefId and tabla=:Tabla".$query.";");
            $stmt->execute(array(':RefId'=>$archivo['refid'], ':Tabla'=>$archivo['tabla']));            
            while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                $datos[]=array(
                    'id'=>(int)$row['id'],
                    'refid'=>$row['refid'],                        
                    'tabla'=>$row['tabla'],
                    'nombre'=>$row['nombre'],
                    'titulo'=>$row['titulo'],
                    'descripcion'=>$row['descripcion'],
                    'tipo'=>$row['tipo']
                );
            }           
            return $datos;
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    function FnBuscarArchivo($conmy, $id) {
      try {
        $datos = array();
        $stmt = $conmy->prepare("SELECT id, refid, tabla, nombre, titulo, descripcion, tipo FROM tblarchivos WHERE id = :id");
        $stmt->execute(array(':id' => $id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $datos[] = array(
            'id' => (int)$row['id'],
            'refid' => (int)$row['refid'],
            'tabla' => $row['tabla'],
            'nombre' => $row['nombre'],
            'titulo' => $row['titulo'],
            'descripcion' => $row['descripcion'],
            'tipo' => $row['tipo']
          );
        }
        return $datos;
      } catch (PDOException $ex) {
          throw new Exception($ex->getMessage());
      } catch (Exception $ex) {
          throw new Exception($ex->getMessage());
      }
    }

    function FnBuscarReferenciaArchivos($conmy, $refId) {
        try {
            $datos=array();
            $stmt=$conmy->prepare("select id, tabla, nombre, titulo, descripcion, tipo from tblarchivos where refid=:RefId;");
            $stmt->execute(array(':RefId'=>$refId));            
            while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
                $datos[]=array(
                    'id'=>(int)$row['id'],
                    'tabla'=>$row['tabla'],
                    'tipo'=>$row['tipo'],
                    'nombre'=>$row['nombre'],
                    'titulo'=>$row['titulo'],
                    'descripcion'=>$row['descripcion']
                );
            }
            return $datos;
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    function FnBuscarReferenciasArchivos($conmy, $archivo, $query) {
        try {
            $datos=array();
            $stmt=$conmy->prepare("select id, refid, nombre, titulo, descripcion, tipo from tblarchivos where refid IN(".$query.") and tabla=:Tabla and tipo=:Tipo;");
            $stmt->execute(array(':Tabla'=>$archivo['tabla'], ':Tipo'=>$archivo['tipo']));            
            while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
                $datos[$row['refid']][]=array(
                    'id'=>(int)$row['id'],
                    'tipo'=>$row['tipo'],
                    'nombre'=>$row['nombre'],
                    'titulo'=>$row['titulo'],
                    'descripcion'=>$row['descripcion']
                );
            }
            return $datos;
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    function FnModificarActividadArchivo($conmy, $archivo) {
      try {
        // VALIDAR QUE EXISTS id ANTES DE PROCESARr.
        if (empty($archivo->id)) { return false; }
        $datos = array();
        $params = array(':Id' => $archivo->id);
        // VERIFICAR SI DEBE ACTUALIZARSE TITULO Y DESCRIPCION
        if (isset($archivo->titulo)) {
          if (empty($archivo->titulo)) {
              $datos[] = "titulo = NULL";  
          } else {
              $datos[] = "titulo = :Titulo";
              $params[':Titulo'] = $archivo->titulo;
          }
        }
        if (isset($archivo->descripcion)) {
          if (empty($archivo->descripcion)) {
            $datos[] = "descripcion = NULL";  
          } else {
            $datos[] = "descripcion = :Descripcion";  
            $params[':Descripcion'] = $archivo->descripcion;
          }
        }
        // VERIFICAR SI SE DEBE ACTUALIZAR NOMBRE DE ARCHIVO
        if (!empty($archivo->nombre)) {
          $datos[] = "nombre = :Archivo";
          $params[':Archivo'] = $archivo->nombre;
        }
        if (empty($datos)) {
          return false;
        }
        // CONSTRUIR CONSULTA FINAL.
        $sql = "UPDATE tblarchivos SET " . implode(", ", $datos) . " WHERE id = :Id";
        $stmt = $conmy->prepare($sql);
        return $stmt->execute($params);
      } catch (Exception $ex) {
        error_log($ex->getMessage());
        return false;
      } 
    }

    function FnModificarArchivo($conmy, $archivo) {
      try {
        // BUSCAR ARCHIVO EXISTENTE
        $query = "SELECT * FROM tblarchivos WHERE id = :Id";
        $stmt = $conmy->prepare($query);
        $stmt->bindParam(':Id', $archivo->Id, PDO::PARAM_INT);
        $stmt->execute();
  
        $archivoExistente = $stmt->fetch(PDO::FETCH_OBJ);
        if (!$archivoExistente) {
          throw new Exception("El archivo no existe.");
        }
        // CONSULTA MODIFICACIÓN
        $query = "UPDATE tblarchivos SET descripcion = :Descripcion, titulo = :Titulo" .(!empty($archivo->nombre) ? ", nombre = :Nombre" : "") . " WHERE id = :Id";
        $stmt = $conmy->prepare($query);
        $params = array(
          ':Descripcion' => $archivo->Descripcion,
          ':Titulo' => $archivo->Titulo,
          ':Id' => $archivo->Id,
        );
        // AGREGAR NUEVO NOMBRE SI EXISTE
        if (!empty($archivo->nombre)) {
          $params[':Nombre'] = $archivo->nombre;
        }
        // EJECUTAR CONSULTA
        if ($stmt->execute($params) || $stmt->rowCount() != 0) { 
          return true; 
        }
        return false;
      } catch (PDOException $ex) {
        throw new Exception($ex->getMessage());
      }
    }
?>