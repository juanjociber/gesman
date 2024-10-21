<?php
    session_start();

    if(empty($_SESSION['UserName']) || empty($_SESSION['CliId']) || empty($_SESSION['CliNombre'])){
        header("location:/gesman/Salir.php");
        exit();
    }

    $ID=empty($_GET['id']) ? 0 : $_GET['id'];
    $ESTADO=0;
    $ORDEN=array();

    require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGesmanDb.php';
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/OrdenesData.php";

    try{
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $ORDEN=FnBuscarOrden($conmy, $_SESSION['CliId'], $ID);
        if(!empty($ORDEN['id'])){
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
            background-color: #ccd1d1;
            transition: background-color .5s;
        }
        .boton-hide{
            z-index: 0 !important;
        }
    </style>
</head>
<body>

    <?php require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/menu/sidebar.php';?>

    <div class="container section-top">
        <div class="row mb-3">
            <div class="col-12 btn-group" role="group" aria-label="Basic example">
                <button type="button" class="btn btn-outline-primary fw-bold" onclick="FnOrdenes(); return false;"><i class="fas fa-list"></i><span class="d-none d-sm-block"> Órdenes</span></button>
                <button type="button" class="btn btn-outline-primary fw-bold" onclick="FnOrden(); return false;"><i class="fas fa-desktop"></i><span class="d-none d-sm-block"> Resúmen</span></button>
            </div>
        </div>

        <div class="row border-bottom mb-3 fs-5">
            <div class="col-12 fw-bold" style="display:flex; justify-content:space-between;">
                <p class="m-0 p-0"><?php echo $_SESSION['CliNombre'];?></p>
                <input type="hidden" id="txtId" value="<?php echo $ID;?>" readonly/>
                <p class="m-0 p-0 text-center text-secondary"><?php echo empty($ORDEN['nombre'])?null:$ORDEN['nombre'];?></p>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
                    <ol class="breadcrumb">                        
                        <li class="breadcrumb-item active fw-bold" aria-current="page">ORDEN</li>
                        <li class="breadcrumb-item fw-bold"><a href="/gesman/EditarOrdenTareos.php?id=<?php echo $ID;?>" class="text-decoration-none">TAREOS</a></li>
                        <li class="breadcrumb-item fw-bold"><a href="/gesman/EditarOrdenArchivos.php?id=<?php echo $ID;?>" class="text-decoration-none">ARCHIVOS</a></li>
                        <li class="breadcrumb-item fw-bold"><a href="/gesman/EditarOrdenVales.php?id=<?php echo $ID;?>" class="text-decoration-none">VALES</a></li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row mb-1 p-1">
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Fecha:</p>
                <input type="date" id="txtFecha" class="form-control" value="<?php echo empty($ORDEN['fecha'])?date('Y-m-d'):$ORDEN['fecha'];?>"/>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Tipo</p>
                <input type="text" class="form-control" value="<?php echo empty($ORDEN['tipnombre']) ? '' : $ORDEN['tipnombre'];?>" readonly/>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Sistema:</p>
                <div class="input-group">
                    <input type="text" id="txtSisId" class="d-none" value="<?php echo empty($ORDEN['sisid']) ? 0 : $ORDEN['sisid'];?>" readonly/>
                    <input type="text" id="txtSisNombre" class="form-control" value="<?php echo empty($ORDEN['sisnombre']) ? '' : $ORDEN['sisnombre'];?>" readonly/>
                    <?php
                        if($ESTADO==1 || $ESTADO==2){
                            echo '<button type="button" class="btn btn-outline-secondary" onclick="FnModalBuscarRecursos('."'sistema'".'); return false;"><i class="fas fa-pen"></i></button>';
                        }
                    ?>
                </div>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Orígen</p>
                <div class="input-group">
                    <input type="text" id="txtOriId" class="d-none" value="<?php echo empty($ORDEN['oriid']) ? 0 : $ORDEN['oriid'];?>" readonly/>
                    <input type="text" id="txtOriNombre" class="form-control" value="<?php echo empty($ORDEN['orinombre']) ? '' : $ORDEN['orinombre'];?>" readonly/>
                    <?php
                        if($ESTADO==1 || $ESTADO==2){
                            echo '<button type="button" class="btn btn-outline-secondary" onclick="FnModalBuscarRecursos('."'origen'".'); return false;"><i class="fas fa-pen"></i></button>';
                        }
                    ?>
                </div>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Supervisor</p>
                <div class="input-group">
                    <input type="text" id="txtSupervisor" class="form-control" value="<?php echo empty($ORDEN['supervisor']) ? 0 : $ORDEN['supervisor'];?>" readonly/>
                    <?php
                        if($ESTADO==1 || $ESTADO==2){
                            echo '<button type="button" class="btn btn-outline-secondary" onclick="FnModalBuscarRecursos('."'supervisor'".'); return false;"><i class="fas fa-pen"></i></button>';
                        }
                    ?>
                </div>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Contacto</p> 
                <div class="input-group">
                    <input type="text" id="txtCliContacto" class="form-control" value="<?php echo empty($ORDEN['clicontacto']) ? '' : $ORDEN['clicontacto'];?>" readonly/>
                    <?php
                        if($ESTADO==1 || $ESTADO==2){
                            echo '<button type="button" class="btn btn-outline-secondary" onclick="FnModalBuscarRecursos('."'contacto'".'); return false;"><i class="fas fa-pen"></i></button>';
                        }
                    ?>
                </div>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Equipo</p>
                <input type="text" class="form-control" value="<?php echo empty($ORDEN['equcodigo'])?'-':$ORDEN['equcodigo'];?>" readonly/>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">KM.</p>
                <input type="number" id="txtEquKm" class="form-control" value="<?php echo empty($ORDEN['equkm'])?0:$ORDEN['equkm'];?>"/>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">HM.</p>
                <input type="number" id="txtEquHm" class="form-control" value="<?php echo empty($ORDEN['equhm'])?0:$ORDEN['equhm'];?>"/>
            </div>
            <div class="col-12 mb-3">
                <p class="m-0 text-secondary" style="font-size: 12px;">Actividades</p>
                <input type="text" id="txtActividades" class="form-control" value="<?php echo empty($ORDEN['actnombre'])?'':$ORDEN['actnombre'];?>"/>
            </div>
            <div class="col-12 mb-3">
                <p class="m-0 text-secondary" style="font-size: 12px;">Trabajos</p>
                <textarea class="form-control" id="txtTrabajos" rows="3"><?php echo empty($ORDEN['trabajos'])?'':$ORDEN['trabajos'];?></textarea>
            </div>
            <div class="col-12 mb-3">
                <p class="m-0 text-secondary" style="font-size: 12px;">Observaciones</p>
                <textarea class="form-control" id="txtObservaciones" rows="2"><?php echo empty($ORDEN['observaciones'])?'':$ORDEN['observaciones'];?></textarea>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12 mb-3">
                <?php
                    if($ESTADO==1 || $ESTADO==2){
                        echo '<button type="button" class="btn btn-outline-primary form-control" onclick="FnModificarOrden(); return false;"><i class="fas fa-save"></i> Guardar</button>';
                    }
                ?>               
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalBuscarRecursos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">BUSCAR RECURSOS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>                
                <div class="modal-body pb-0">
                    <div class="row">
                        <div class="col-12 mb-1">
                            <p class="m-0 text-secondary" style="font-size: 12px;">Nombre</p>
                            <input type="hidden" id="txtTabla"/>
                            <input type="text" id="txtRecurso" class="d-none" value="" readonly/>
                            <div class="input-group">
                                <input type="text" id="txtBuscar" class="form-control" value=""/>
                                <button type="button" class="btn btn-outline-secondary" onclick="FnBuscarRecursos(); return false;"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                    <div id="tblRecursos" class="row p-2"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>              
            </div>
        </div>
    </div>

    <div class="container-loader-full">
        <div class="loader-full"></div>
    </div>

    <script src="/mycloud/library/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
    <script src="/mycloud/library/bootstrap-5-alerta-1.0/js/bootstrap-5-alerta-1.0.js"></script>
    <script src="/gesman/js/EditarOrden.js"></script>
    <script src="/gesman/menu/sidebar.js"></script>
</body>
</html>