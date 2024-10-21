<?php
    session_start();

    if(empty($_SESSION['UserName']) || empty($_SESSION['CliId']) || empty($_SESSION['CliNombre'])){
        header("location:/gesman/Salir.php");
        exit();
    }

    $ID=empty($_GET['id']) ? 0 : $_GET['id'];
    $ESTADO=0;
    $ORDEN=array();
    $TAREOS=array();
    $ARCHIVOS=array();

    require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGesmanDb.php';
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/OrdenesData.php";

    try{
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $ORDEN=FnBuscarOrden($conmy, $_SESSION['CliId'], $ID);
        if(!empty($ORDEN['id'])){
            $TAREOS=FnBuscarOrdenTareos($conmy, $ID);
            $ARCHIVOS=FnBuscarOrdenArchivos($conmy, $ID, 'ORD');
            if(!empty($ORDEN['estado'])){
                $ESTADO=$ORDEN['estado'];
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
    <title>Orden de Trabajo | GPEM SAC.</title>
    <link rel="shortcut icon" href="/mycloud/logos/favicon.ico">
    <link rel="stylesheet" href="/mycloud/library/fontawesome-free-5.9.0-web/css/all.css">
    <link rel="stylesheet" href="/mycloud/library/gpemsac/css/gpemsac.css">
    <link rel="stylesheet" href="/mycloud/library/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/gesman/menu/sidebar.css">
    <style>
        .divselect {
            cursor: pointer;
            transition: all .25s ease-in-out;
        }
        .divselect:hover {
            background-color: #ccd1d1 !importart;
            transition: background-color .5s;
        }
    </style>
</head>
<body>
    <?php require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/menu/sidebar.php';?>
    <div class="container section-top">
        <div class="row mb-3 gpem-hide-print">
            <div class="col-12 btn-group" role="group" aria-label="Basic example">
                <button type="button" class="btn btn-outline-primary fw-bold" onclick="FnListarOrdenes(); return false;"><i class="fas fa-list"></i><span class="d-none d-sm-block"> Ordenes</span></button>
                <?php
                    if($ESTADO==1 || $ESTADO==2){
                        echo ' <button type="button" class="btn btn-outline-primary fw-bold" onclick="FnEditarOrden(); return false;"><i class="fas fa-edit"></i><span class="d-none d-sm-block"> Editar</span></button>';
                        echo '<button type="button" class="btn btn-outline-primary fw-bold" onclick="FnModalFinalizarOrden(); return false;"><i class="fas fa-check-square"></i><span class="d-none d-sm-block"> Finalizar</span></button>';
                    }
                ?>
                <button type="button" class="btn btn-outline-primary fw-bold" onclick="FnOrdenResumen(<?php echo $ID;?>); return false;"><i class="fas fa-print"></i><span class="d-none d-sm-block"> Imprimir</span></button>
                <?php
                    if($_SESSION['RolMan']>2){
                        echo '<button type="button" class="btn btn-outline-primary fw-bold" onclick="FnModalAgregarInforme(); return false;"><i class="fas fa-plus"></i><span class="d-none d-sm-block"> Informe</span></button>';
                    }
                ?>             
            </div>
        </div>

        <div class="row border-bottom mb-2 fs-5">
            <div class="col-12 fw-bold d-flex justify-content-between">
                <p class="m-0 p-0"><?php echo $_SESSION['CliNombre'];?></p>
                <input type="hidden" id="txtId" value="<?php echo $ID;?>">
                <p class="m-0 p-0 text-center text-secondary"><?php echo empty($ORDEN['nombre'])?null:$ORDEN['nombre'];?></p>
            </div>
        </div>
        
        <div class="row p-1 mb-0">
            <div class="col-12 mb-0 border-bottom bg-light">
                <p class="m-0 fw-bold">ORDEN</p>
            </div>
        </div>

        <div class="row p-1 mb-2">    
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Fecha</p> 
                <p class="m-0 p-0"><?php echo empty($ORDEN['fecha'])?'-':$ORDEN['fecha'];?></p>
            </div>            
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Equipo</p> 
                <p class="m-0 p-0"><?php echo empty($ORDEN['equcodigo'])?'-':$ORDEN['equcodigo'];?></p>
            </div>            
            <?php
                if(!empty($ORDEN['equkm'])){
                    echo '
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Km.</p> 
                        <p class="m-0 p-0">'.$ORDEN['equkm'].'</p>
                    </div>';
                }
            ?>
            <?php
                if(!empty($ORDEN['equhm'])){
                    echo '
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Hm.</p> 
                        <p class="m-0 p-0">'.$ORDEN['equhm'].'</p>
                    </div>';
                }
            ?>            
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 13px;">Tipo</p> 
                <p class="m-0 p-0"><?php echo empty($ORDEN['tipnombre'])?'-':$ORDEN['tipnombre'];?></p>
            </div>
            <?php
                if(!empty($ORDEN['sisnombre'])){
                    echo '
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Sistema</p> 
                        <p class="m-0 p-0">'.$ORDEN['sisnombre'].'</p>
                    </div>';
                }
            ?>
            <?php
                if(!empty($ORDEN['orinombre'])){
                    echo '
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Orígen</p> 
                        <p class="m-0 p-0">'.$ORDEN['orinombre'].'</p>
                    </div>';
                }
            ?>            
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 13px;">Supervisor</p> 
                <p class="m-0 p-0"><?php echo empty($ORDEN['supervisor'])?'-':$ORDEN['supervisor'];?></p>
            </div>            
            <?php
                if(!empty($ORDEN['clicontacto'])){
                    echo '
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Contacto</p> 
                        <p class="m-0 p-0">'.$ORDEN['clicontacto'].'</p>
                    </div>';
                }
            ?>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Estado</p>
                <?php
                    switch ($ESTADO){
                        case 0:
                            echo "<span class='badge bg-danger'>Anulado</span>";
                            break;
                        case 1:
                            echo "<span class='badge bg-primary'>Abierto</span>";
                            break;
                        case 2:
                            echo "<span class='badge bg-primary'>Proceso</span>";
                            break;
                        case 3:
                            echo "<span class='badge bg-success'>Cerrado</span>";
                            break;
                        case 4:
                            echo "<span class='badge bg-warning'>Obervado</span>";
                            break;
                        default:
                            echo "<span class='badge bg-secondary'>Unknown</span>";
                    }
                ?>
            </div>
            <div class="col-12 mb-1">
                <p class="m-0 text-secondary" style="font-size: 13px;">Actividades</p> 
                <p class="m-0 p-0"><?php echo empty($ORDEN['actnombre'])?'':$ORDEN['actnombre'];?></p>
            </div>
            <div class="col-12 mb-1">
                <p class="m-0 text-secondary" style="font-size: 13px;">Trabajos</p> 
                <p class="m-0 p-0"><?php echo empty($ORDEN['trabajos'])?'':$ORDEN['trabajos'];?></p>
            </div>            
            <?php
                if(!empty($ORDEN['observaciones'])){
                    echo '
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Observaciones</p> 
                        <p class="m-0 p-0">'.$ORDEN['observaciones'].'</p>
                    </div>';
                }
            ?>

        </div>
        <div class="row p-1 mb-0">
            <div class="col-12 mb-0 border-bottom bg-light">
                <p class="m-0 fw-bold">TAREOS</p>
            </div>
        </div>        
        <div class="row mb-2 p-1">
            <?php
                if(count($TAREOS)>0){
                    foreach ($TAREOS as $key=>$valor) {
                        echo '
                        <div class="col-12 mb-1 pb-1 border-bottom">
                            <div>
                                <div class="d-flex justify-content-between">
                                    <p class="m-0 p-0">'.$valor['pernombre'].'</p>
                                    <p class="m-0 p-0 fw-bold">'.$valor['minutos'].' Min</p>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <p class="m-0 p-0">'.$valor['ingreso'].'</p>
                                    <p class="m-0 p-0">'.$valor['salida'].'</p>                                    
                                </div>
                            </div>
                        </div>';
                    }                    
                }else{
                    echo '
                    <div class="col-12">
                        <p class="fst-italic">No se encontró información.</p>
                    </div>';
                }
            ?>
        </div>
        <div class="row p-1 mb-1">
            <div class="col-12 mb-0 border-bottom bg-light">
                <p class="m-0 fw-bold">ANEXOS</p>
            </div>
        </div>
        
        <?php
            if(count($ARCHIVOS)>0){
                echo '<div class="row row-cols-1 row-cols-md-3 g-3 mb-3">';
                foreach ($ARCHIVOS as $key=>$valor) {
                    switch ($valor['tipo']) {
                        case 'IMG':
                            $icono=' fa-file-image ';
                            break;
                        case 'PDF':
                            $icono=' fa-file-pdf ';
                            break;
                        default:
                            $icono=' fa-question ';
                            break;
                    }
                    echo '
                    <div class="col">
                        <div class="card h-100 divselect" onclick="FnModalVerArchivo('."'".$valor['nombre']."'".', '."'".$valor['tipo']."'".'); return false;">
                            <div class="card-body d-flex justifi-between-content align-items-center p-0">
                                <div class="p-2">
                                    <i class="fas'.$icono.'fs-1 text-secondary"></i>
                                </div>
                                <div class="p-2">
                                    <p class="m-0 fw-bold">'.$valor['tipo'].'</p>
                                    <p class="m-0">'.$valor['titulo'].'</p>
                                </div>
                            </div>
                        </div>
                    </div>';
                }
                echo '</div>';
            }else{
                echo '<div class="row mb-2 p-1"><div class="col-12"><p class="fst-italic">No se encontró información.</p></div></div>';
            }
        ?>
    </div>
     
    <div class="modal fade" id="modalFinalizarOrden" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">FINALIZAR ORDEN DE TRABAJO</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>                
                <div class="modal-body pb-1">
                    <div class="row text-center fw-bold pt-3">                        
                        <p class="text-center">Para finalizar la Órden <?php echo empty($ORDEN['nombre']) ? '0' : $ORDEN['nombre'];?> haga clic en el botón CONFIRMAR.</p>                    
                    </div>
                </div>
                <div class="modal-body pt-1" id="msjFinalizarOrden"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="FnFinalizarOrden(); return false;">CONFIRMAR</button>
                </div>              
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalVerArchivo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Vizualización de Archivos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>                
                <div class="modal-body pb-1">
                    <div class="row text-center fw-bold">                        
                        <div class="col-12 mb-1">
                            <p id="pNombre" class="m-0"></p>
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

    <div class="modal fade" id="modalAgregarInforme" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">AGREGAR INFORME</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6 mb-2">
                            <p class="m-0 text-secondary" style="font-size:13px;">Fecha:</p>
                            <input type="date" class="form-control" id="txtFecha" value="<?php echo empty($ORDEN['fecha']) ? date('Y-m-d') : $ORDEN['fecha'];?>"/>
                        </div>
                        <div class="col-6 mb-2">
                            <p class="m-0 text-secondary" style="font-size: 13px;">Equipo</p>
                            <input type="text" class="form-control" value="<?php echo empty($ORDEN['equcodigo']) ? '' : $ORDEN['equcodigo'];?>" readonly/>
                        </div>
                        <div class="col-12">
                            <p class="m-0 text-secondary" style="font-size:13px;">Actividad:</label>
                            <textarea class="form-control" id="txtActividad" rows="2"><?php echo empty($ORDEN['actnombre'])? '' : $ORDEN['actnombre'];?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="FnAgregarInforme(); return false;">CONFIRMAR</button>
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
    <script src="/gesman/js/Orden.js"></script>
    <script src="/gesman/menu/sidebar.js"></script>

</body>
</html>