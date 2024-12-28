<?php 
    
    function FnBuscarProducto($conmy, $id) {
        try {
            $datos=array();
            $stmt = $conmy->prepare("select id, idcliente, idodoo, idlista, idactividad, codigo, nombre, cantidad, medida, estado from tblproductosprogramados WHERE id=:Id;");
            $stmt->execute(array(':Id'=>$id));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);            
            if ($row) {
                $datos['id']=$row['id'];
                $datos['cliid']=$row['idcliente'];
                $datos['odoid']=$row['idodoo'];
                $datos['lisid']=$row['idlista'];
                $datos['actid']=$row['idactividad'];
                $datos['codigo']=$row['codigo'];
                $datos['nombre']=$row['nombre'];
                $datos['cantidad']=$row['cantidad'];
                $datos['medida']=$row['medida'];
                $datos['estado']=$row['estado'];
            }            
            return $datos;
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    function FnListarProductos($conmy, $producto) {
        try {
            $datos=array();
            $stmt=$conmy->prepare("select id, idodoo, idlista, codigo, nombre, cantidad, medida from tblproductosprogramados where idcliente=:CliId and idactividad=:ActId and estado=2 and concat(codigo, nombre) like :Nombre limit 15;");
            $stmt->execute(array(':CliId'=>$producto['cliid'], ':ActId'=>$producto['actid'], ':Nombre'=>'%'.$producto['nombre'].'%'));	
            if($stmt->rowCount()>0){
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $datos[]=array(
                        'id'=>$row['id'],
                        'odoid'=>$row['idodoo'],
                        'lisid'=>$row['idlista'],
                        'codigo'=>$row['codigo'],
                        'nombre'=>$row['nombre'],
                        'cantidad'=>$row['cantidad'],
                        'medida'=>$row['medida']
                    );
                }
            } 
            return $datos;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }
?>