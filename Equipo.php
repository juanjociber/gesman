<?php
    session_start();

    if(empty($_SESSION['UserName']) || empty($_SESSION['CliId']) || empty($_SESSION['CliNombre'])){
        header("location:/gesman/Salir.php");
        exit();
    }

    $ID=empty($_GET['id']) ? 0 : $_GET['id'];
    $ESTADO=0;
    $EQUIPO=array();
    $ARCHIVOS=array();

    require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGesmanDb.php';
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/EquiposData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/ArchivosData.php";

    try{
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $EQUIPO=FnBuscarEquipo($conmy, $_SESSION['CliId'], $ID);
        if(!empty($EQUIPO['id'])){
            $ARCHIVOS=FnBuscarArchivos($conmy, array('refid'=>$ID, 'tabla'=>'ORD'));
            if(!empty($EQUIPO['estado'])){
                $ESTADO=$EQUIPO['estado'];
            }
        }
        $conmy==null;
    } catch(PDOException $ex) {
        $conmy = null;
    } catch (Exception $ex) {
        $conmy = null;
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipo | GPEM SAC</title>
    <link rel="shortcut icon" href="/mycloud/logos/favicon.ico">
    <link rel="stylesheet" href="/mycloud/library/fontawesome-free-5.9.0-web/css/all.css">
    <link rel="stylesheet" href="/mycloud/library/gpemsac/css/gpemsac.css">
    <link rel="stylesheet" href="/mycloud/library/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/gesman/menu/sidebar.css">
</head>
<body>
    <?php require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/menu/sidebar.php';?>
    <div class="container section-top">
        
        <div class="row mb-3 gpem-hide-print">
            <div class="col-12 btn-group" role="group" aria-label="Basic example">
                <button type="button" class="btn btn-outline-primary fw-bold" onclick="FnEquipos(); return false;"><i class="fas fa-list"></i><span class="d-none d-sm-block"> Equipos</span></button>
                <?php
                    if($ESTADO==2){
                        echo ' <button type="button" class="btn btn-outline-primary fw-bold" onclick="FnEditarEquipo(); return false;"><i class="fas fa-edit"></i><span class="d-none d-sm-block"> Editar</span></button>';
                        echo '<button type="button" class="btn btn-outline-primary fw-bold" onclick="FnModalAnularEquipo(); return false;"><i class="fas fa-times-circle"></i><span class="d-none d-sm-block"> Anular</span></button>';
                    }
                ?>              
            </div>
        </div>

        <div class="row border-bottom mb-2 fs-5">
            <div class="col-12 fw-bold d-flex justify-content-between">
                <p class="m-0 p-0"><?php echo $_SESSION['CliNombre'];?></p>
                <input type="hidden" id="txtId" value="<?php echo $ID;?>">
                <p class="m-0 p-0 text-center text-secondary"><?php echo empty($EQUIPO['codigo'])?null:$EQUIPO['codigo'];?></p>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12 col-sm-6">
                <img src="/gesman/descargas/DescargarEquipoImagen.php?imagen=<?php echo $EQUIPO['archivo'];?>" class="img-fluid" alt="...">
            </div>
            <div class="col-12 col-sm-6">
                <div class="row p-1 mb-2">    
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Código</p> 
                        <p class="m-0 p-0"><?php echo empty($EQUIPO['codigo'])?'-':$EQUIPO['codigo'];?></p>
                    </div>            
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Nombre</p> 
                        <p class="m-0 p-0"><?php echo empty($EQUIPO['nombre'])?'-':$EQUIPO['nombre'];?></p>
                    </div> 
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Flota</p> 
                        <p class="m-0 p-0"><?php echo empty($EQUIPO['flonombre'])?'-':$EQUIPO['flonombre'];?></p>
                    </div> 
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Marca</p> 
                        <p class="m-0 p-0"><?php echo empty($EQUIPO['marca'])?'-':$EQUIPO['marca'];?></p>
                    </div> 
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Modelo</p> 
                        <p class="m-0 p-0"><?php echo empty($EQUIPO['modelo'])?'-':$EQUIPO['modelo'];?></p>
                    </div>
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Serie</p> 
                        <p class="m-0 p-0"><?php echo empty($EQUIPO['serie'])?'-':$EQUIPO['serie'];?></p>
                    </div>
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Fabricante</p> 
                        <p class="m-0 p-0"><?php echo empty($EQUIPO['fabricante'])?'-':$EQUIPO['fabricante'];?></p>
                    </div>
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Procedencia</p> 
                        <p class="m-0 p-0"><?php echo empty($EQUIPO['procedencia'])?'-':$EQUIPO['procedencia'];?></p>
                    </div>                   
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Ubicación</p> 
                        <p class="m-0 p-0"><?php echo empty($EQUIPO['ubicacion'])?'-':$EQUIPO['ubicacion'];?></p>
                    </div>
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Km</p> 
                        <p class="m-0 p-0"><?php echo empty($EQUIPO['km'])?'-':$EQUIPO['km'];?></p>
                    </div>
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Hm</p> 
                        <p class="m-0 p-0"><?php echo empty($EQUIPO['hm'])?'-':$EQUIPO['hm'];?></p>
                    </div>
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Motor</p> 
                        <p class="m-0 p-0"><?php echo empty($EQUIPO['motor'])?'-':$EQUIPO['motor'];?></p>
                    </div>
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Transmisión</p> 
                        <p class="m-0 p-0"><?php echo empty($EQUIPO['transmision'])?'-':$EQUIPO['transmision'];?></p>
                    </div>
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Diferencial</p> 
                        <p class="m-0 p-0"><?php echo empty($EQUIPO['diferencial'])?'-':$EQUIPO['diferencial'];?></p>
                    </div>
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Placa</p> 
                        <p class="m-0 p-0"><?php echo empty($EQUIPO['placa'])?'-':$EQUIPO['placa'];?></p>
                    </div>
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Estado</p>
                        <?php
                            switch ($ESTADO){
                                case 1:
                                    echo "<span class='badge bg-danger'>INACTIVO</span>";
                                    break;
                                case 2:
                                    echo "<span class='badge bg-success'>ACTIVO</span>";
                                    break;
                                default:
                                    echo "<span class='badge bg-secondary'>Unknown</span>";
                            }
                        ?>
                    </div>
                    <div class="col-12 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Características</p> 
                        <p class="m-0 p-0"><?php echo empty($EQUIPO['datos'])?'-':$EQUIPO['datos'];?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-0 border-bottom bg-light">
                <p class="m-0 fw-bold">ANEXOS</p>
            </div>
        </div>  

        <div class="row mb-3">
            <div class="col-12">
                <?php
                    if(count($ARCHIVOS)>0){
                        foreach ($ARCHIVOS as $key=>$valor) {
                            echo '<button type="button" class="btn btn-outline-secondary m-2" datanombre="'.$valor['nombre'].'" datatipo="'.$valor['tipo'].'" datatitulo="'.$valor['titulo'].'" onclick="FnModalVerArchivo(this); return false;">'.$valor['nombre'].'</button>';
                        }
                    }else{
                        echo '<p class="fst-italic">No se encontró información.</p>';
                    }
                ?>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="modalVerArchivo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">VISUALIZAR ANEXO</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>                
                <div class="modal-body pb-1">
                    <div class="row text-center fw-bold">                        
                        <div class="col-12 mb-1">
                            <p id="pTitulo" class="m-0"></p>
                        </div>
                        <div class="col-12 mb-1" id="fileContainer">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ACEPTAR</button>
                </div>              
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAnularEquipo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">ANULAR EQUIPO</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>                
                <div class="modal-body pb-1">
                    <div class="row text-center fw-bold pt-3">                        
                        <p class="text-center">Para anular el Equipo <?php echo empty($EQUIPO['codigo']) ? '-' : $EQUIPO['codigo'];?> haga clic en el botón CONFIRMAR.</p>                    
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="FnAnularEquipo(); return false;">CONFIRMAR</button>
                </div>              
            </div>
        </div>
    </div>

    <div class="container-loader-full">
        <div class="loader-full"></div>
    </div>

    <script src="/mycloud/library/jquery-3.5.1/jquery-3.5.1.js"></script>
    <script src="/mycloud/library/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
    <script src="/mycloud/library/bootstrap-5-alerta-1.0/js/bootstrap-5-alerta-1.0.js"></script>
    <script src="/gesman/js/Equipo.js"></script>
    <script src="/gesman/menu/sidebar.js"></script>

</body>
</html>