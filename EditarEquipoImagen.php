<?php
    session_start();

    if(empty($_SESSION['UserName']) || empty($_SESSION['CliId']) || empty($_SESSION['CliNombre'])){
        header("location:/gesman/Salir.php");
        exit();
    }

    $ID=empty($_GET['id']) ? 0 : $_GET['id'];
    $ESTADO=0;
    $EQUIPO=array();

    require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGesmanDb.php';
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/EquiposData.php";

    try{
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $EQUIPO=FnBuscarEquipo($conmy, $_SESSION['CliId'], $ID);
        if(!empty($EQUIPO['id'])){
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
    <title>Editar Equipo | GPEM SAC.</title>
    <link rel="shortcut icon" href="/mycloud/logos/favicon.ico">
    <link rel="stylesheet" href="/mycloud/library/fontawesome-free-5.9.0-web/css/all.css">
    <link rel="stylesheet" href="/mycloud/library/gpemsac/css/gpemsac.css">
    <link rel="stylesheet" href="/mycloud/library/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/gesman/menu/sidebar.css">
    <style>
        .container-wa{
            position: absolute;
            right: 4px;
            top: 0px;
        }
        .link-wa:hover{
            color: red;
        }
        #canvas{
            width: 75%;
            margin: 0 auto;
            display: block;
            border: 1px solid #d9d9d9;
        }

        .divselect {
            cursor: pointer;
            transition: all .25s ease-in-out;
        }

        .divselect:hover {
            color: #2d7fc7;
            transition: background-color .5s;
        }
    </style>
</head>
<body>

    <?php require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/menu/sidebar.php';?>

    <div class="container section-top">
        
        <div class="row mb-3">
            <div class="col-12 btn-group" role="group" aria-label="Basic example">
                <button type="button" class="btn btn-outline-primary fw-bold" onclick="FnEquipos(); return false;"><i class="fas fa-list"></i><span class="d-none d-sm-block"> Equipos</span></button>
                <button type="button" class="btn btn-outline-primary fw-bold" onclick="FnEquipo(); return false;"><i class="fas fa-desktop"></i><span class="d-none d-sm-block"> Resúmen</span></button>
            </div>
        </div>

        <div class="row border-bottom mb-3 fs-5">
            <div class="col-12 fw-bold d-flex justify-content-between">
                <p class="m-0 p-0"><?php echo $_SESSION['CliNombre'];?></p>
                <input type="hidden" id="txtId" value="<?php echo $ID;?>">
                <p class="m-0 p-0 text-center text-secondary"><?php echo empty($EQUIPO['codigo'])?null:$EQUIPO['codigo'];?></p>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
                    <ol class="breadcrumb">                        
                        <li class="breadcrumb-item fw-bold"><a href="/gesman/EditarEquipo.php?id=<?php echo $ID;?>" class="text-decoration-none">EQUIPO</a></li>
                        <li class="breadcrumb-item active fw-bold" aria-current="page">IMAGEN</li>
                        <li class="breadcrumb-item fw-bold"><a href="/gesman/EditarEquipoArchivos.php?id=<?php echo $ID;?>" class="text-decoration-none">ANEXOS</a></li>
                    </ol>
                </nav>
            </div>
        </div>
        
        <div class="row border-bottom mb-3">
            <div class="col-12 mb-2">
                <button type="button" class="btn btn-outline-primary form-control" onclick="FnModalAgregarImagen(); return false;"><i class="fas fa-plus"></i> Cambiar</button>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12 text-center">
                <img src="/gesman/descargas/DescargarEquipoImagen.php?imagen=<?php echo $EQUIPO['archivo'];?>" class="img-fluid">
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAgregarImagen" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">AGREGAR IMAGEN</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pb-1">
                    <div class="row">                       
                        <div class="col-12">
                            <p class="m-0 text-secondary" style="font-size: 13px;">Seleccionar Imágen</p>
                            <input id="fileImagen" type="file" accept="image/*" class="form-control mb-2"/>
                        </div>
                        <div class="col-12 m-0">
                            <div class="col-md-12 text-center" id="divImagen"><i class="fas fa-images fs-2"></i></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="FnAgregarImagen(); return false;">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-loader-full">
        <div class="loader-full"></div>
    </div>

    <script src="/mycloud/library/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
    <script src="/mycloud/library/bootstrap-5-alerta-1.0/js/bootstrap-5-alerta-1.0.js"></script>
    <script src="/gesman/js/EditarEquipoImagen.js"></script>
    <script src="/gesman/menu/sidebar.js"></script>
</body>
</html>