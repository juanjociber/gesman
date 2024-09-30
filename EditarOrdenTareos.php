<?php
    session_start();

    if(empty($_SESSION['UserName']) || empty($_SESSION['CliId']) || empty($_SESSION['CliNombre'])){
        header("location:/gesman/Salir.php");
        exit();
    }

    date_default_timezone_set("America/Lima");
    require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGesmanDb.php';

    $IdOt = 0;
    $Ot = '';
    $Estado = 0; 

    if(!empty($_GET['orden'])){
        try{
            $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt=$conmy->prepare("select idot, ot, estado from man_ots where idot=:IdOt and idcliente=:IdCliente;");
            $stmt->execute(array('IdOt'=>$_GET['orden'], 'IdCliente'=>$_SESSION['CliId']));
            $row=$stmt->fetch();
            if($row){
                $IdOt = $row['idot'];
                $Ot = $row['ot'];
                $Estado = $row['estado'];
            }
            $stmt=null;
        }catch(PDOException $ex){
            $stmt=null;
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
    <link rel="stylesheet" href="/mycloud/library/select2-4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="/gesman/menu/sidebar.css">    

    <style>
    .container-wa{
        position: absolute;
        z-index: 3;
        right: 0px;
        margin:0px;
        padding:0px;
    }

    .link-wa:hover{
        color: red;
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
            <div class="col-12 fw-bold d-flex justify-content-between">
                <p class="m-0 p-0"><?php echo $_SESSION['CliNombre'];?></p>
                <input type="text" class="d-none" id="txtIdOt" value="<?php echo $IdOt;?>" readonly/>
                <p class="m-0 p-0 text-center text-secondary">OT <?php echo $Ot;?></p>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
                    <ol class="breadcrumb">                        
                        <li class="breadcrumb-item fw-bold"><a href="/gesman/EditarOrden.php?orden=<?php echo $IdOt;?>" class="text-decoration-none">ORDEN</a></li>
                        <li class="breadcrumb-item active fw-bold" aria-current="page">TAREOS</li>
                        <li class="breadcrumb-item fw-bold"><a href="/gesman/EditarOrdenImagenes.php?orden=<?php echo $IdOt;?>" class="text-decoration-none">IMAGENES</a></li>
                        <li class="breadcrumb-item fw-bold"><a href="/gesman/EditarOrdenVales.php?orden=<?php echo $IdOt;?>" class="text-decoration-none">VALES</a></li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row border-bottom mb-3">
            <div class="col-12 mb-2">
                <button type="button" class="btn btn-outline-primary form-control<?php echo $Visible; ?>" onclick="FnModalAgregarTareo(); return false;"><i class="fas fa-plus"></i> Tareo</button>              
            </div>
        </div>

        <div class="row p-2">
            <div class="col-12">
            <?php
            try{
                $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $stmt=$conmy->prepare("select idtareo, personal, ingreso, salida, tmin from man_tareos where idot=:IdOt;");
                $stmt->execute(array('IdOt'=>$IdOt));
                if($stmt->rowCount()>0){
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                        echo '
                        <div class="row mb-2 border-bottom border-secondary" style="position: relative;">
                            <div class="container-wa text-end pe-2'.$Visible.'">
                                <a class="text-decoration-none text-secondary p-0" href="#" onclick="FnEliminarTareo('.$row['idtareo'].'); return false;"><i class="fas fa-times link-wa fs-4"></i></a>
                            </div>
                            <div class="col-12">
                                <p class="p-0 m-0 fw-bold">'.$row['personal'].'</p>
                            </div>
                            <div class="col-12 d-flex justify-content-between" style="font-size:13px;">
                                <p class="p-0 m-0">'.$row['ingreso'].'</p>
                                <p class="p-0 m-0">'.$row['salida'].'</p>
                                <p class="p-0 m-0 fw-bold">'.$row['tmin'].' Min</p>
                            </div>
                        </div>';                     
                    }
                }else{
                    echo '
                    <div class="col-12">
                        <p class="fst-italic">No hay Tareos asociados a esta Órden.</p>
                    </div>';
                }
                $stmt=null;
            }catch(PDOException $e){
                $stmt=null;
            }
            ?>
            </div>
            
        </div>

    </div>
    
    <div class="modal fade" id="modalAgregarTareo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Nuevo Tareo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pb-1 mb-1">
                    <div class="row">                        
                        <div class="col-12 mb-2">
                            <p class="m-0 text-secondary" style="font-size:12px;">Personal</p>
                            <select class="js-example-responsive" name="cbPersonal" id="cbPersonal" style="width: 100%">
                                <option value="0">Seleccionar</option>
                            </select>
                        </div>
                        <div class="col-12 mb-2">
                            <p class="m-0 text-secondary" style="font-size:12px;">Ingreso:</p>
                            <input type="datetime-local" class="form-control" id="dtpIngreso" value="<?php echo date('Y-m-d H:i:s');?>"/>
                        </div>
                        <div class="col-12">
                            <p class="m-0 text-secondary" style="font-size:12px;">Salida:</p>
                            <input type="datetime-local" class="form-control" id="dtpSalida" value="<?php echo date('Y-m-d H:i:s');?>"/>
                        </div>                  
                    </div>
                </div>
                <div class="modal-body pb-1 pt-1" id="msjAgregarTareo"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="FnAgregarTareo(); return false;">GUARDAR</button>
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
    <script src="/gesman/js/EditarOrdenTareos.js"></script>
    <script src="/gesman/menu/sidebar.js"></script>

</body>
</html>