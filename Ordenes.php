<?php 
    session_start();

    if(!isset($_SESSION['UserName']) || !isset($_SESSION['CliId'])){
        header("location:/gesman");
        exit();
    }

    date_default_timezone_set("America/Lima");
    require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGesmanDb.php';

    $cbActivos='';
    $cbTipos='';
    $cbSistemas='';
    $Visible="";

    if(empty($_SESSION['CliId'])){
        $Visible=" d-none";
    }

    try{
        $stmt=$conmy->prepare("select idtipoot, tipoot from man_tipoots where idcliente=:IdCliente and estado=2;");
        $stmt->bindParam(':IdCliente', $_SESSION['CliId'], PDO::PARAM_INT);
        $stmt->execute();
        while ($row=$stmt->fetch(PDO::FETCH_ASSOC)){
            $cbTipos.='<option value="'.$row['idtipoot'].'">'.$row['tipoot'].'</option>';
        }  
        
        $stmt=$conmy->prepare("select idsistema, sistema from man_sistemas where idcliente=:IdCliente and estado=2;");
        $stmt->bindParam(':IdCliente', $_SESSION['CliId'], PDO::PARAM_INT);
        $stmt->execute();
        while ($row=$stmt->fetch(PDO::FETCH_ASSOC)){
            $cbSistemas.='<option value="'.$row['idsistema'].'">'.$row['sistema'].'</option>';
        }  

        $stmt=null;
    }catch(PDOException $e){
        $stmt=null;
        echo '<script type="text/javascript">alert("'.$e->getMessage().'");</script>';
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
</head>
<body>
    
    <?php require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/menu/sidebar.php';?>
    
    <div class="container section-top">
        <div class="row p-1 mb-3">
            <div class="col-12 border-bottom fw-bold fs-5">
                <p class="m-0 p-0"><?php echo $_SESSION['CliNombre'];?></p>
            </div>
        </div>
        <div class="row mb-1 border-bottom">
            <div class="col-6 col-sm-3 mb-2">
                <p class="m-0" style="font-size:12px;">Órden</p>
                <input type="text" class="form-control" id="txtOrden">
            </div>
            <div class="col-6 col-sm-3 mb-2">
                <p class="m-0" style="font-size:12px;">Equipo</p>
                <select class="js-example-responsive" name="cbActivo1" id="cbActivo1" style="width: 100%"></select>
            </div>
            <div class="col-6 col-sm-3 mb-3">
                <p class="m-0" style="font-size:12px;">Fecha Inicial</p>
                <input type="date" class="form-control" id="dtpFechaInicial" value="<?php echo date('Y-m-d');?>"/>
            </div>
            <div class="col-6 col-sm-3 mb-3">
                <p class="m-0" style="font-size:12px;">Fecha Final</p>
                <input type="date" class="form-control" id="dtpFechaFinal" value="<?php echo date('Y-m-d');?>"/>
            </div>
            <div class="col-6 mb-2">
                <button type="button" class="btn btn-outline-primary form-control<?php echo $Visible;?>" onclick="FnModalAgregarOrden(); return false;"><i class="fas fa-plus"></i> Orden</button>
            </div>   
            <div class="col-6 mb-2">
                <button type="button" class="btn btn-outline-primary form-control<?php echo $Visible;?>" onclick="FnBuscarOrdenes(); return false;"><i class="fas fa-search"></i> Buscar</button>
            </div>  
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="row p-2" id="divOrdenes">
                    <p class="fst-italic">Haga clic en el botón Buscar para obtener resultados.</p>
                </div>
            </div>
        </div>

        <div class="row p-2">            
            <div class="col-12 text-center mb-3 d-none" id="divPaginacion">
                <button type="button" class="btn btn-outline-primary" onclick="fnNuevaPagina(); return false;"><i class="fas fa-chevron-down"></i> Ver mas.. </button>
            </div>
        </div>
    </div>
    
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



    <div class="modal fade" id="modalAgregarOrden" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Nueva Orden</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pb-1 mb-1">
                    <div class="row mb-3">
                        <div class="col-6">
                            <p class="m-0 text-secondary" style="font-size:12px;">Fecha:</p>
                            <input type="date" class="form-control" id="txtFecha2" value="<?php echo date('Y-m-d');?>"/>
                        </div>
                        <div class="col-6">
                            <p class="m-0 text-secondary" style="font-size: 12px;">Orden</p>
                            <input type="text" class="form-control" id="txtOrden2">
                        </div>                        
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <p class="m-0 text-secondary" style="font-size:12px;">Equipo</p>
                            <select class="js-example-responsive" name="cbActivo2" id="cbActivo2" style="width: 100%">
                                <option value="0">Seleccionar</option>
                            </select>
                        </div>  
                        <div class="col-6">
                            <p class="m-0 text-secondary" style="font-size:12px;">Km/Hr:</label>
                            <input type="number" class="form-control" id="txtKm2" value="0">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <p class="m-0 text-secondary" style="font-size:12px;">Tipo:</label>
                            <select class="form-select" id="cbTipo2">
                                <option value="0">Seleccionar</option>
                                <?php echo $cbTipos;?>
                            </select>
                        </div>
                        <div class="col-6">
                            <p class="m-0 text-secondary" style="font-size:12px;">Sistema:</label>
                            <select class="form-select" id="cbSistema2">
                                <option value="0">Seleccionar</option>
                                <?php echo $cbSistemas;?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <p class="m-0 text-secondary" style="font-size:12px;">Actividad:</label>
                            <textarea class="form-control" id="txtActividad2" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-body pb-1 pt-1" id="msjAgregarOrden"></div>
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
    <script src="/gesman/js/Ordenes.js"></script>
    <script src="/gesman/menu/sidebar.js"></script>
</body>
</html>