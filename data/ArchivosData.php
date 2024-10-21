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
            $stmt = $conmy->prepare("delete from tblarchivos where id=:Id and refid=:RefId;");
            $stmt->execute(array(':Id'=>$archivo['id'], ':RefId'=>$archivo['refid']));
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






    function FnBuscarArchivoTituloDescripcion($conmy, $id) {
        try {
          $stmt = $conmy->prepare("SELECT id, titulo, descripcion, nombre FROM tblarchivos WHERE id = :Id");
          $stmt->execute(array(':Id' => $id));
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
          if ($row) {
            $archivo = new stdClass();
            $archivo->id = $row['id'];
            $archivo->titulo = $row['titulo'];
            $archivo->descripcion = $row['descripcion'];
            $archivo->nombre = $row['nombre'];
            return $archivo;
          }
          return null;
        } catch (PDOException $e) {
          throw new Exception($e->getMessage());
        }
      }
    
    //   function FnBuscarArchivos($conmy, $id) {
    //     try {
    //       $stmt = $conmy->prepare("SELECT id, refid, tabla, nombre, descripcion, tipo, titulo FROM tblarchivos WHERE refid=:Id");
    //       $stmt->execute(array(':Id' => $id));
    //       $archivos = $stmt ->fetchAll(PDO::FETCH_ASSOC);
    //       return $archivos;
    //     } catch (PDOException $ex) {
    //       throw new Exception($ex->getMessage());
    //     }
    //   }
    
      function FnRegistrarArchivo($conmy, $imagen) {
        try {
          $stmt = $conmy->prepare("INSERT INTO tblarchivos (refid, tabla, nombre, titulo, descripcion, tipo) VALUES (:RefId, :Tabla, :Nombre, :Titulo, :Descripcion, :Tipo)");
          $params = array(
            ':RefId' => $imagen->refid,
            ':Tabla' => $imagen->tabla,
            ':Nombre' => $imagen->nombre,
            ':Titulo' => $imagen->titulo,
            ':Descripcion' => $imagen->descripcion,
            ':Tipo' => $imagen->tipo
          );
          $stmt->execute($params);
          return $stmt;
        } catch (PDOException $ex) {
          throw new Exception($ex->getMessage());
        }
      }
    
      function FnModificarArchivoImagenTituloDescripcion($conmy, $archivo) {
        try {
          $query = "UPDATE tblarchivos SET descripcion = :Descripcion, titulo = :Titulo";
          if (!empty($archivo->nombre)) {
            $query.=", nombre = :Nombre";
          }
          $query.=" WHERE id = :Id";
          $stmt = $conmy->prepare($query);
          $params = array(
            ':Descripcion' => $archivo->Descripcion,
            ':Titulo' => $archivo->Titulo,
            ':Id' => $archivo->Id,
          );
          // AGREGAR NUEVO NOMBRE
          if (!empty($archivo->nombre)) {
            $params[':Nombre'] = $archivo->nombre;
          }
          // EJECUTAR CONSULTA
          $result = $stmt->execute($params);
          if ($stmt->rowCount() == 0) {
            throw new Exception('Cambios no realizados.');
          }
          return $result;
        } catch (PDOException $e) {
          throw new Exception($e->getMessage());
        }
      }
    
      function FnModificarArchivoAnexoTituloDescripcion($conmy, $archivo) {
        try {
          $query = "UPDATE tblarchivos SET descripcion = :Descripcion, titulo = :Titulo";
          if (!empty($archivo->nombre)) {
            $query.=", nombre = :Nombre";
          }
          $query.=" WHERE id = :Id";
          $stmt = $conmy->prepare($query);
          $params = array(
            ':Descripcion' => $archivo->Descripcion,
            ':Titulo' => $archivo->Titulo,
            ':Id' => $archivo->Id,
          );
          // AGREGAR NUEVO NOMBRE
          if (!empty($archivo->nombre)) {
            $params[':Nombre'] = $archivo->nombre;
          }
          // EJECUTAR CONSULTA
          $result = $stmt->execute($params);
          if ($stmt->rowCount() == 0) {
            throw new Exception('Cambios no realizados.');
          }
          return $result;
        } catch (PDOException $e) {
          throw new Exception($e->getMessage());
        }
      }
    
    //   function FnEliminarArchivo($conmy, $id) {
    //     try {
    //       $res = false;
    //       $stmt = $conmy->prepare("DELETE FROM tblarchivos WHERE id =:Id");
    //       $params = array(':Id' => $id);
    //       if ($stmt->execute($params)) {
    //           $res = true;
    //       }
    //       return $res;
    //     } catch (PDOException $e) {
    //       throw new Exception($e->getMessage());
    //     }
    //   }


?>