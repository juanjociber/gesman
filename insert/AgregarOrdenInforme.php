<?php 
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/informes/data/InformesData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/OrdenesData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/EquiposData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/ClientesData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/ArchivosData.php";

    $datos=array('res'=>false, 'id'=>0, 'msg'=>'Error General.');

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(!FnValidarSesion()){throw new Exception("Se ha perdido la conexión.");}
        if(!FnValidarSesionManNivel2()){throw new Exception("Usuario no autorizado.");}
        if(empty($_POST['ordid']) || empty($_POST['fecha']) || empty($_POST['actividad'])){throw new Exception("La información esta incompleta.");}       

        $conmy->beginTransaction();

        $orden=FnBuscarOrden($conmy, $_SESSION['gesman']['CliId'], $_POST['ordid']);
        if(empty($orden['id'])){ throw new Exception("No se encontró la Orden.");}

        $archivos=FnBuscarArchivos($conmy, array('refid'=>$orden['id'], 'tabla'=>'ORD'));
        
        $equipo=FnBuscarEquipo($conmy, $orden['cliid'], $orden['equid']);
        if(empty($equipo['id'])){ throw new Exception("No se encontró el Equipo."); }
        
        $cliente=FnBuscarCliente($conmy, $orden['cliid']);
        if(empty($cliente['id'])){ throw new Exception("No se encontró el Cliente."); }

        $USUARIO=date('Ymd-His (').$_SESSION['gesman']['Nombre'].')';

        $infome=array(
            'ordid'=>$orden['id'],
            'equid'=>$equipo['id'],
            'cliid'=>$cliente['id'],
            'supid'=>$_SESSION['gesman']['PerId'],
            'fecha'=>$_POST['fecha'],
            'ordnombre'=>$orden['nombre'],
            'clinombre'=>$cliente['nombre'],
            'clidireccion'=>$cliente['direccion'],
            'clicontacto'=>$orden['clicontacto'],
            'supnombre'=>$_SESSION['gesman']['Alias'],
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
        if($id==0){throw new Exception("Error agregando el Informes.");}

        $actividades=array();

        if(!empty($orden['trabajos'])){
            $actividades[]=array(
                'infid'=>$id,
                'ownid'=>0,
                'orden'=>0,
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
            $i=1;
            foreach ($archivos as $key=>$valor) {
                $actividades[]=array(
                    'infid'=>$id,
                    'ownid'=>0,
                    'orden'=>$i,
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
                $i+=1;
            }
        }

        if(count($actividades)>0){
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