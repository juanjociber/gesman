<?php 
    session_start();
    $datos=array('res'=>false, 'id'=>0, 'msg'=>'Error General.');

    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/informes/datos/InformesData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/OrdenesData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/EquiposData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/ClientesData.php";

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if(empty($_SESSION['CliId']) || empty($_SESSION['UserName']) || empty($_SESSION['UserNombre']) || empty($_SESSION['RolMan'])){throw new Exception("Se ha perdido la conexión.");}
        if($_SESSION['RolMan']<2){throw new Exception("Usuario no autorizado.");}//1:tecnico, 2:supervisor, 3:analista, 4 gerente de area, 5:gerente general.
        if(empty($_POST['ordid']) || empty($_POST['fecha']) || empty($_POST['actividad'])){throw new Exception("La información esta incompleta.");}       

        $conmy->beginTransaction();

        $orden=FnBuscarOrden($conmy, $_SESSION['CliId'], $_POST['ordid']);
        if(empty($orden['id'])){ throw new Exception("No se encontró la Orden.");}

        $archivos=FnBuscarOrdenArchivos($conmy, $orden['id'], 'ORD');
        
        $equipo=FnBuscarEquipo($conmy, $orden['cliid'], $orden['equid']);
        if(empty($equipo['id'])){ throw new Exception("No se encontró el Equipo."); }
        
        $cliente=FnBuscarCliente($conmy, $orden['cliid']);
        if(empty($cliente['id'])){ throw new Exception("No se encontró el Cliente."); }

        $USUARIO=date('Ymd-His (').$_SESSION['UserName'].')';

        $infome=array(
            'ordid'=>$orden['id'],
            'equid'=>$equipo['id'],
            'cliid'=>$cliente['id'],
            'fecha'=>$_POST['fecha'],
            'ordnombre'=>$orden['nombre'],
            'clinombre'=>$cliente['nombre'],
            'clidireccion'=>$cliente['direccion'],
            'clicontacto'=>$orden['clicontacto'],
            'supervisor'=>$_SESSION['UserNombre'],
            'equcodigo'=>$equipo['codigo'],
            'equnombre'=>$equipo['nombre'],
            'equmarca'=>$equipo['marca'],
            'equmodelo'=>$equipo['modelo'],
            'equserie'=>$equipo['serie'],
            'equdatos'=>$equipo['datos'],
            'equkm'=>$orden['equkm'],
            'equhm'=>$orden['equhm'],
            'actividad'=>$_POST['actividad'],
            'usuario'=>$USUARIO
        );

        $id=FnAgregarInforme($conmy, $infome);
        if($id==0){throw new Exception("Error agregando el Informe.");}

        $actividades=array();

        if(!empty($orden['trabajos'])){
            $actividades[]=array(
                'infid'=>$id,
                'ownid'=>0,
                'acttipo'=>'ant',
                'actnombre'=>$orden['trabajos'],
                'diagnostico'=>null,
                'trabajos'=>null,
                'observaciones'=>null,
                'arctabla'=>null,
                'arcnombre'=>null,
                'arctipo'=>null,
                'usuario'=>$USUARIO
            );
        }        

        if(count($archivos)>0){
            foreach ($archivos as $key=>$valor) {
                $actividades[]=array(
                    'infid'=>$id,
                    'ownid'=>0,
                    'acttipo'=>'act',
                    'actnombre'=>empty($valor['titulo'])?'-':$valor['titulo'],
                    'diagnostico'=>null,
                    'trabajos'=>$valor['descripcion'],
                    'observaciones'=>null,
                    'arctabla'=>'INFD',
                    'arcnombre'=>$valor['nombre'],
                    'arctipo'=>$valor['tipo'],
                    'usuario'=>$USUARIO
                );
            }
            FnAgregarInformeActividades($conmy, $actividades);
        }

        $conmy->commit();

        $datos['res']=true;
        $datos['id']=$id;
        $datos['msg']="Se agregó el Informe.";

        $conmy=null;
    } catch(PDOException $ex){
        $datos['msg']=$ex->getMessage();
        $conmy->rollBack();
        $conmy=null;
    } catch (Exception $ex) {
        $datos['msg']=$ex->getMessage();
        $conmy->rollBack();
        $conmy=null;
    }

    echo json_encode($datos);
?>