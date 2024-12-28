<?php 
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";

    if(!FnValidarSesion()){
        header("location:/gesman/Salir.php");
        exit();
    }

    if(!FnValidarSesionManNivel1()){
        header("HTTP/1.1 403 Forbidden");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordenes de Trabajo | GPEM SAC.</title>
    <link rel="shortcut icon" href="/mycloud/logos/favicon.ico">
    <link rel="stylesheet" href="/mycloud/library/fontawesome-free-5.9.0-web/css/all.css">
    <link rel="stylesheet" href="/mycloud/library/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/mycloud/library/select2-4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="/mycloud/library/gpemsac/css/gpemsac.css"> 
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
        .select2-selection__rendered {
            line-height: 36px !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
        }
        .select2-search__field{
            border: 1px solid #ced4da !important;
            height: 37px !important;
        }
        .select2-search__field:focus{
            color: #212529;
            background-color: #fff !important;
            border-color: #86b7fe !important;
            outline: 0 !important;
            box-shadow: 0 0 0 0.25rem rgba(13,110,253,.25) !important;
        }
        .select2-container .select2-selection--single {
            height: 37px !important;
            border: 1px solid #ced4da !important;
        }
        .select2-selection__arrow {
            display: none !important;
            /*height: 34px !important;*/
        }
    </style>
</head>
<body>
    <?php require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/menu/sidebar.php';?>
    <div class="container section-top">
        <div class="row p-1 mb-3">
            <div class="col-12 border-bottom d-flex justify-content-between">
                <p class="m-0 fw-bold fs-5"><?php echo $_SESSION['gesman']['CliNombre'];?></p>
                <?php
                    if(FnValidarSesionManNivel2()){
                        echo '<a href="/gesman/AdmOrdenes.php" title="Búsqueda avanzada" class="text-decoration-none d-flex justify-content-between align-items-center fw-bold"><i class="fas fa-search-plus"></i> <span class="d-none d-sm-block"> Búsqueda avanzada</span></a>';
                    }
                ?>
            </div>
        </div>
        <div class="row mb-1 border-bottom">
            <div class="col-6 col-sm-2 mb-2">
                <p class="m-0" style="font-size:12px;">Órden</p>
                <input type="text" class="form-control" id="txtOrden">
            </div>
            <div class="col-6 col-sm-3 mb-2">
                <p class="m-0" style="font-size:12px;">Equipo</p>
                <select class="js-example-responsive" name="cbEquipo" id="cbEquipo" style="width: 100%"></select>
            </div>
            <div class="col-12 col-sm-3 mb-2">
                <p class="m-0" style="font-size:12px;">Actividad</p>
                <input type="text" class="form-control" id="txtActividad">
            </div>
            <div class="col-6 col-sm-2 mb-3">
                <p class="m-0" style="font-size:12px;">Fecha Inicial</p>
                <input type="date" class="form-control" id="dtpFechaInicial" value="<?php echo date('Y-m-d');?>"/>
            </div>
            <div class="col-6 col-sm-2 mb-3">
                <p class="m-0" style="font-size:12px;">Fecha Final</p>
                <input type="date" class="form-control" id="dtpFechaFinal" value="<?php echo date('Y-m-d');?>"/>
            </div>
            <div class="col-6 mb-2">
                <button type="button" class="btn btn-outline-primary form-control" onclick="FnModalAgregarOrden(); return false;"><i class="fas fa-plus"></i> Órden</button>
            </div>   
            <div class="col-6 mb-2">
                <button type="button" class="btn btn-outline-primary form-control" onclick="FnBuscarOrdenes(); return false;"><i class="fas fa-search"></i> Buscar</button>
            </div>  
        </div>        
        <div id="tblOrdenes" class="row mb-3">
            <div class="col-12">
                <div class="fst-italic p-2">Haga clic en el botón Buscar para obtener resultados.</div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12 font-weight-bold d-flex justify-content-center mb-3">
                <button type="button" id="btnPrimero" class="btn btn-sm btn-outline-primary d-none mx-2" onclick="FnBuscarPrimero(); return false;">PRIMERO</button>
                <button type="button" id="btnSiguiente" class="btn btn-sm btn-outline-primary d-none mx-2" onclick="FnBuscarSiguiente(); return false;">SIGUIENTE</button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAgregarOrden" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">AGREGAR ORDEN</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pb-1 mb-1">
                    <div class="row">
                        <div class="col-6 mb-2">
                            <p class="m-0 text-secondary" style="font-size:13px;">Fecha:</p>
                            <input type="date" class="form-control" id="txtFecha2" value="<?php echo date('Y-m-d');?>"/>
                        </div>
                        <div class="col-6 mb-2">
                            <p class="m-0 text-secondary" style="font-size:13px;">Tipo:</label>
                            <select class="form-select" id="cbTipo2">
                                <option value="0">Seleccionar</option>
                                <option value="1">CORRECTIVO</option>
                                <option value="2">PREVENTIVO</option>
                                <option value="3">INTERNO</option>
                            </select>
                        </div>
                        <div class="col-6 mb-2">
                            <p class="m-0 text-secondary" style="font-size: 13px;">Órden</p>
                            <input type="text" class="form-control" id="txtNombre2">
                        </div>
                        <div class="col-6 mb-2">
                            <p class="m-0 text-secondary" style="font-size:13px;">Equipo</p>
                            <select class="js-example-responsive" name="cbEquipo2" id="cbEquipo2" style="width: 100%">
                                <option value="0">Seleccionar</option>
                            </select>
                        </div>
                        <div class="col-6 mb-2">
                            <p class="m-0 text-secondary" style="font-size:13px;">KM</label>
                            <input type="number" class="form-control" id="txtEquKm2" value="0">
                        </div>
                        <div class="col-6 mb-2">
                            <p class="m-0 text-secondary" style="font-size:13px;">HM</label>
                            <input type="number" class="form-control" id="txtEquHm2" value="0">
                        </div>
                        <div class="col-6 mb-2">
                            <p class="m-0 text-secondary" style="font-size:13px;">Sistema</p>
                            <select class="js-example-responsive" name="cbSistema2" id="cbSistema2" style="width: 100%">
                                <option value="0">Seleccionar</option>
                            </select>
                        </div>
                        <div class="col-12 mb-2">
                            <p class="m-0 text-secondary" style="font-size:13px;">Actividad</label>
                            <textarea class="form-control" id="txtActNombre2" rows="2"></textarea>
                        </div>                  
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="FnAgregarOrden(); return false;">GUARDAR</button>
                </div>              
            </div>
        </div>
    </div>

    <div class="container-loader-full">
        <div class="loader-full"></div>
    </div>

    <script src="/mycloud/library/jquery-3.5.1/jquery-3.5.1.js"></script>
    <script src="/mycloud/library/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
    <script src="/mycloud/library/select2-4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="/mycloud/library/bootstrap-5-alerta-1.0/js/bootstrap-5-alerta-1.0.js"></script>
    <script src="/gesman/js/Ordenes.js"></script>
    <script src="/gesman/menu/sidebar.js"></script>
</body>
</html>