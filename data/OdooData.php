<?php 
    function FnBuscarSaleOrders($conpg, $ordid) {
        try {
            $datos=array();
            $stmt=$conpg->prepare("select id, name, date_order, ot_vale, state from sale_order where ot_id=:OrdId;");
            $stmt->execute(array(':OrdId'=>$ordid));
            if($stmt->rowCount()>0){
                while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                    $datos[]=array(
                        'id'=>$row['id'],
                        'nombre'=>$row['name'],                        
                        'fecha'=>$row['date_order'],
                        'vale'=>$row['ot_vale'],
                        'estado'=>$row['state']
                    );
                }
            }            
            return $datos;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    function FnBuscarSaleOrderLines($conpg, $soid) {
        try {
            $datos=array();
            $stmt=$conpg->prepare("select id, order_id, name, product_uom_qty, state from sale_order_line where order_id=:SoId;");
            $stmt->execute(array(':SoId'=>$soid));
            if($stmt->rowCount()>0){
                while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                    $datos[]=array(
                        'id'=>$row['id'],
                        'soid'=>$row['order_id'],
                        'nombre'=>$row['name'],                        
                        'cantidad'=>$row['product_uom_qty'],
                        'estado'=>$row['state']
                    );
                }
            }            
            return $datos;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    function FnBuscarSaleOrderLinePicking($conpg, $linid) {
        try {
            $datos=array();
            $stmt=$conpg->prepare("select id, product_id, description_picking, product_uom_qty, state from stock_move where sale_line_id=:LinId;");
            $stmt->execute(array(':LinId'=>$linid));
            if($stmt->rowCount()>0){
                while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                    $datos[]=array(
                        'id'=>$row['id'],
                        'proid'=>$row['product_id'],
                        'nombre'=>$row['description_picking'],                        
                        'cantidad'=>$row['product_uom_qty'],
                        'estado'=>$row['state']
                    );
                }
            }
            return $datos;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }
?>