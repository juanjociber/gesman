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
    } catch (PDOException $ex) {
      throw new Exception($ex->getMessage());
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

  function FnBuscarArchivos2($conmy, $id) {
    try {
      $stmt = $conmy->prepare("SELECT id, refid, tabla, nombre, descripcion, tipo, titulo FROM tblarchivos WHERE refid=:Id");
      $stmt->execute(array(':Id' => $id));
      $archivos = $stmt ->fetchAll(PDO::FETCH_ASSOC);
      return $archivos;
    } catch (PDOException $ex) {
      throw new Exception($ex->getMessage());
    }
  }

  function FnBuscarArchivo($conmy, $id) {
    try {
      $stmt = $conmy->prepare("SELECT id, refid, titulo, descripcion, nombre FROM tblarchivos WHERE id = :Id");
      $stmt->execute(array(':Id' => $id));
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row) {
        $archivo = new stdClass();
        $archivo->id = $row['id'];
        $archivo->refid = $row['refid'];
        $archivo->titulo = $row['titulo'];
        $archivo->descripcion = $row['descripcion'];
        $archivo->nombre = $row['nombre'];
        return $archivo;
      }
      return null;
    } catch (PDOException $ex) {
      throw new Exception($ex->getMessage());
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
?>