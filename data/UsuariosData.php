<?php 
    function FnBuscarClienteUsuarios($conmy, $usuario) {
        try {
            $datos = array('data'=>array(), 'pag'=>0);
            $query = "";

            if(!empty($usuario['nombre'])){
                $query=" and nombre like'%".$usuario['nombre']."%'";
            }

            if($usuario['estado']>0){
                $query.=" and estado=".$usuario['estado'];
            }

            $query.=" limit ".$usuario['pagina'].", 2";

            $stmt = $conmy->prepare("select id, nombre, estado from sis_usuarios where cliid=:CliId".$query.";");
            $stmt->execute(array(':CliId'=>$usuario['cliid']));
			$n=$stmt->rowCount();
            if($n>0){
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $datos['data'][]=array(
                        'id'=>(int)$row['idsistema'],
                        'nombre'=>$row['sistema'],
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
?>