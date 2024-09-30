<?php
    session_start();

    if(empty($_SESSION['UserName']) || empty($_SESSION['CliId']) || empty($_SESSION['CliNombre'])){
        header("location:/gesman/Salir.php");
        exit();
    }

    date_default_timezone_set("America/Lima");
    require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGesmanDb.php';

    $IdOt = 0;
    $IdActivo = 0;
    $IdTipoOt = 0;
    $IdSistema = 0;
    $IdOrigen = 0;
    $IdContacto = 0;
    $Ot = '';
    $Activo = '';
    $TipoOt = '';
    $Sistema = '';
    $Origen = '';
    $FechaInicial = '';
    $TipoTrabajo = '';
    $Actividad = '';
    $Descripcion = '';
    $Observacion = '';
    $Km = '';
    $Supervisor = '';
    $Contacto = '';
    $Estado = 0;
    
    if(!empty($_GET['orden'])){
        try{
            $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt=$conmy->prepare("select idot, idtipoot, idsistema, idorigen, idcontacto, ot, activo, tipoot, sistema, origen, fechainicial, tipotrabajo, actividad, descripcion, observaciones, km, supervisor, contacto, estado from man_ots where idot=:IdOt and idcliente=:IdCliente;");
            $stmt->execute(array(':IdOt'=>$_GET['orden'], ':IdCliente'=>$_SESSION['CliId']));
            $row=$stmt->fetch();
            if($row){
                $IdOt = $row['idot'];
                $IdTipoOt = $row['idtipoot'];
                $IdSistema = $row['idsistema'];
                $IdOrigen = $row['idorigen'];
                $IdContacto = $row['idcontacto'];
                $Ot = $row['ot'];
                $Activo = $row['activo'];
                $TipoOt = $row['tipoot'];
                $Sistema = $row['sistema'];
                $Origen = $row['origen'];
                $FechaInicial = $row['fechainicial'];
                $TipoTrabajo = $row['tipotrabajo'];
                $Actividad = $row['actividad'];
                $Descripcion = $row['descripcion'];
                $Observacion = $row['observaciones'];
                $Km = $row['km'];
                $Supervisor = $row['supervisor'];
                $Contacto = $row['contacto'];
                $Estado = $row['estado'];
            }
            $stmt=null;
        }catch(PDOException $ex){
            $stmt=null;
            echo $ex->getMessage();
        }
    }

    $Visible = ' d-none';
    if($Estado==1 || $Estado==2 || $Estado==4){
        $Visible = '';
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
        a.link-colecciones {
            color: black;
            text-decoration: none;
        }
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
                <button type="button" class="btn btn-outline-primary fw-bold" onclick="FnListarOrdenes(); return false;"><i class="fas fa-list"></i><span class="d-none d-sm-block"> Órdenes</span></button>
                <button type="button" class="btn btn-outline-primary fw-bold" onclick="FnResumenOrden(); return false;"><i class="fas fa-desktop"></i><span class="d-none d-sm-block"> Resúmen</span></button>
            </div>
        </div>

        <div class="row border-bottom mb-3 fs-5">
            <div class="col-12 fw-bold" style="display:flex; justify-content:space-between;">
                <p class="m-0 p-0"><?php echo $_SESSION['CliNombre'];?></p>
                <input type="text" class="d-none" id="txtIdOt" value="<?php echo $IdOt;?>" readonly/>
                <p class="m-0 p-0 text-center text-secondary">OT <?php echo $Ot;?></p>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
                    <ol class="breadcrumb">                        
                        <li class="breadcrumb-item active fw-bold" aria-current="page">ORDEN</li>
                        <li class="breadcrumb-item fw-bold"><a href="/gesman/EditarOrdenTareos.php?orden=<?php echo $IdOt;?>" class="text-decoration-none">TAREOS</a></li>
                        <li class="breadcrumb-item fw-bold"><a href="/gesman/EditarOrdenImagenes.php?orden=<?php echo $IdOt;?>" class="text-decoration-none">IMAGENES</a></li>
                        <li class="breadcrumb-item fw-bold"><a href="/gesman/EditarOrdenVales.php?orden=<?php echo $IdOt;?>" class="text-decoration-none">VALES</a></li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row mb-1 p-1">
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Fecha:</p>
                <input type="date" id="txtFecha" class="form-control" value="<?php echo $FechaInicial;?>"/>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Tipo</p>
                <input type="text" class="form-control" value="<?php echo $TipoOt;?>" readonly/>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Sistema:</p>
                <div class="input-group">
                    <input type="text" id="txtIdSistema" class="d-none" value="<?php echo $IdSistema;?>" readonly/>
                    <input type="text" id="txtSistema" class="form-control" value="<?php echo $Sistema;?>" readonly/>
                    <button type="button" class="btn btn-outline-secondary boton-hide<?php echo $Visible;?>" onclick="FnModalBuscarRecursos('sistema'); return false;"><i class="fas fa-pen"></i></button>
                </div>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Orígen</p>
                <div class="input-group">
                    <input type="text" id="txtIdOrigen" class="d-none" value="<?php echo $IdOrigen;?>" readonly/>
                    <input type="text" id="txtOrigen" class="form-control" value="<?php echo $Origen;?>" readonly/>
                    <button type="button" class="btn btn-outline-secondary boton-hide<?php echo $Visible;?>" onclick="FnModalBuscarRecursos('origen'); return false;"><i class="fas fa-pen"></i></button>
                </div>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Supervisor</p>
                <div class="input-group">
                    <input type="text" id="txtSupervisor" class="form-control" value="<?php echo $Supervisor;?>" readonly/>
                    <button type="button" class="btn btn-outline-secondary boton-hide<?php echo $Visible;?>" onclick="FnModalBuscarRecursos('supervisor'); return false;"><i class="fas fa-pen"></i></button>
                </div>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Contacto</p> 
                <div class="input-group">
                    <input type="text" id="txtContacto" class="form-control" value="<?php echo $Contacto;?>" readonly/>
                    <button type="button" class="btn btn-outline-secondary boton-hide<?php echo $Visible;?>" onclick="FnModalBuscarRecursos('contacto'); return false;"><i class="fas fa-pen"></i></button>
                </div>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Activo</p>
                <input type="text" class="form-control" value="<?php echo $Activo;?>" readonly/>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Km/Hr</p>
                <input type="number" id="txtKm" class="form-control" value="<?php echo $Km;?>"/>
            </div>
            <div class="col-12 mb-3">
                <p class="m-0 text-secondary" style="font-size: 12px;">Actividad</p>
                <input type="text" id="txtActividad" class="form-control" value="<?php echo $Actividad;?>"/>
            </div>
            <div class="col-12 mb-3">
                <p class="m-0 text-secondary" style="font-size: 12px;">Descripción</p>
                <textarea class="form-control" id="txtDescripcion" rows="3"><?php echo $Descripcion;?></textarea>
            </div>
            <div class="col-12 mb-3">
                <p class="m-0 text-secondary" style="font-size: 12px;">Observaciones</p>
                <textarea class="form-control" id="txtObservacion" rows="2"><?php echo $Observacion;?></textarea>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12 mb-3">
                <button type="button" class="btn btn-outline-primary form-control<?php echo $Visible;?>" onclick="FnModificarOrden(); return false;"><i class="fas fa-save"></i> Guardar</button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalBuscarRecursos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <!--<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">-->
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Buscar Recursos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>                
                <div class="modal-body pb-0">
                    <div class="row">
                        <div class="col-12 mb-1">
                            <p class="m-0 text-secondary" style="font-size: 12px;">Nombre</p>
                            <input type="text" id="txtRecurso" class="d-none" value="" readonly/>
                            <div class="input-group">
                                <input type="text" id="txtBuscar" class="form-control" value=""/>
                                <button type="button" class="btn btn-outline-secondary" onclick="FnBuscarRecursos(); return false;"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                    <div id="divRecursos" class="row p-2"></div>
                </div>
                <div id="msjBuscarRecursos" class="modal-body pt-1"></div>
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
    <script src="/gesman/js/EditarOrden.js"></script>
    <script src="/gesman/menu/sidebar.js"></script>
</body>
</html>