<?php 
    function FnListarPersonal($conmy, $nombre) {
        try {
            $datos=array();
            $stmt=$conmy->prepare("select pers_codigo, concat(pers_apellidos,', ', pers_nombres) as nombre from tblpersonal where pers_estado=1 and concat(pers_apellidos, pers_nombres) like :Nombre limit 15;");
            $stmt->execute(array(':Nombre'=>'%'.$nombre.'%'));
            while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                $datos[]=array(
                    'id'=>$row['pers_codigo'],
                    'nombre'=>$row['nombre']
                );
            }
            return $datos;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }
?>