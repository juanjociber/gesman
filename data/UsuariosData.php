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

    function FnBuscarUsuarioSesion($conmy, $usuario) {
        try {
            $datos=array();
            $stmt = $conmy->prepare("select idusuario, cliid, idpersonal, nombre, usuario, lman, llog, lghu, ladm, lcia, lsis from sis_usuarios where idpersonal>0 and usuario=:Usuario and clave=:Clave and estado=1;");
            $stmt->execute(array(':Usuario'=>$usuario['usuario'], ':Clave'=>$usuario['clave']));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);            
            if ($row) {
                $datos['id']=$row['idusuario'];
                $datos['cliid']=$row['cliid'];
                $datos['perid']=$row['idpersonal'];
                $datos['pernombre']=$row['nombre'];
                $datos['usunombre']=$row['usuario'];
                $datos['rolman']=$row['lman'];
                $datos['rollog']=$row['llog'];
                $datos['rolghu']=$row['lghu'];
                $datos['roladm']=$row['ladm'];
                $datos['rolcia']=$row['lcia'];
                $datos['rolsis']=$row['lsis'];
            }            
            return $datos;
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }
?>