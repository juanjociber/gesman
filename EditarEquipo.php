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
    <title>Editar Equipo | GPEM SAC</title>
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
                <button type="button" class="btn btn-outline-primary fw-bold" onclick="FnEquipos(); return false;"><i class="fas fa-list"></i><span class="d-none d-sm-block"> Equipos</span></button>
                <button type="button" class="btn btn-outline-primary fw-bold" onclick="FnEquipo(); return false;"><i class="fas fa-desktop"></i><span class="d-none d-sm-block"> Resúmen</span></button>
            </div>
        </div>

        <div class="row border-bottom mb-3 fs-5">
            <div class="col-12 fw-bold" style="display:flex; justify-content:space-between;">
                <p class="m-0 p-0"><?php echo $_SESSION['CliNombre'];?></p>
                <input type="hidden" id="txtId" value="<?php echo $ID;?>" readonly/>
                <p class="m-0 p-0 text-center text-secondary"><?php echo empty($EQUIPO['codigo'])?null:$EQUIPO['codigo'];?></p>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
                    <ol class="breadcrumb">                        
                        <li class="breadcrumb-item active fw-bold" aria-current="page">EQUIPO</li>
                        <li class="breadcrumb-item fw-bold"><a href="/gesman/EditarEquipoImagen.php?id=<?php echo $ID;?>" class="text-decoration-none">IMAGEN</a></li>
                        <li class="breadcrumb-item fw-bold"><a href="/gesman/EditarEquipoArchivos.php?id=<?php echo $ID;?>" class="text-decoration-none">ANEXOS</a></li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">Flota:</p>
                <div class="input-group">
                    <input type="text" id="txtFloId" class="d-none" value="<?php echo empty($EQUIPO['floid']) ? 0 : $EQUIPO['floid'];?>" readonly/>
                    <input type="text" id="txtFloNombre" class="form-control" value="<?php echo empty($EQUIPO['flonombre']) ? '' : $EQUIPO['flonombre'];?>" readonly/>
                    <?php
                        if($ESTADO==2){
                            echo '<button type="button" class="btn btn-outline-secondary" onclick="FnModalAgregarFlota(); return false;"><i class="fas fa-pen"></i></button>';
                        }
                    ?>
                </div>
            </div>
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">Nombre</p>
                <input type="text" id="txtNombre" class="form-control" value="<?php echo empty($EQUIPO['nombre'])?'':$EQUIPO['nombre'];?>"/>
            </div>
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">Marca</p>
                <input type="text" id="txtMarca" class="form-control" value="<?php echo empty($EQUIPO['marca'])?'':$EQUIPO['marca'];?>"/>
            </div>
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">Modelo</p>
                <input type="text" id="txtModelo" class="form-control" value="<?php echo empty($EQUIPO['modelo'])?'':$EQUIPO['modelo'];?>"/>
            </div>
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">Placa</p>
                <input type="text" id="txtPlaca" class="form-control" value="<?php echo empty($EQUIPO['placa'])?'':$EQUIPO['placa'];?>"/>
            </div>
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">VIN</p>
                <input type="text" id="txtSerie" class="form-control" value="<?php echo empty($EQUIPO['serie'])?'':$EQUIPO['serie'];?>"/>
            </div>
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">Motor</p>
                <input type="text" id="txtMotor" class="form-control" value="<?php echo empty($EQUIPO['motor'])?'':$EQUIPO['motor'];?>"/>
            </div>
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">Transmisión</p>
                <input type="text" id="txtTransmision" class="form-control" value="<?php echo empty($EQUIPO['transmision'])?'':$EQUIPO['transmision'];?>"/>
            </div>
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">Diferencial</p>
                <input type="text" id="txtDiferencial" class="form-control" value="<?php echo empty($EQUIPO['diferencial'])?'':$EQUIPO['diferencial'];?>"/>
            </div>
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">Año</p>
                <input type="text" id="txtAnio" class="form-control" value="<?php echo empty($EQUIPO['anio'])?'':$EQUIPO['anio'];?>"/>
            </div>
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">Fabricante</p>
                <input type="text" id="txtFabricante" class="form-control" value="<?php echo empty($EQUIPO['fabricante'])?'':$EQUIPO['fabricante'];?>"/>
            </div>
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">Procedencia</p>
                <input type="text" id="txtProcedencia" class="form-control" value="<?php echo empty($EQUIPO['procedencia'])?'':$EQUIPO['procedencia'];?>"/>
            </div>
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">Ubicación</p>
                <input type="text" id="txtUbicacion" class="form-control" value="<?php echo empty($EQUIPO['ubicacion'])?'':$EQUIPO['ubicacion'];?>"/>
            </div>
            <div class="col-12 mb-2">
                <p class="m-0 text-secondary" style="font-size: 13px;">Características</p>
                <input type="text" id="txtDatos" class="form-control" value="<?php echo empty($EQUIPO['datos'])?'':$EQUIPO['datos'];?>"/>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <?php
                    if($ESTADO==2){
                        echo '<button type="button" class="btn btn-outline-primary form-control" onclick="FnModificarEquipo(); return false;"><i class="fas fa-save"></i> Guardar</button>';
                    }
                ?>               
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAgregarFlota" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">AGREGAR FLOTA</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>                
                <div class="modal-body pb-0">
                    <div class="row">
                        <div class="col-12 mb-1">
                            <p class="m-0 text-secondary" style="font-size: 12px;">Nombre</p>
                            <input type="text" id="txtNombre" class="d-none" value="" readonly/>
                            <div class="input-group">
                                <input type="text" id="txtBuscar" class="form-control" value=""/>
                                <button type="button" class="btn btn-outline-secondary" onclick="FnBuscarFlotas(); return false;"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                    <div id="tblFlotas" class="row p-2"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CERRAR</button>
                </div>              
            </div>
        </div>
    </div>

    <div class="container-loader-full">
        <div class="loader-full"></div>
    </div>

    <script src="/mycloud/library/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
    <script src="/mycloud/library/bootstrap-5-alerta-1.0/js/bootstrap-5-alerta-1.0.js"></script>
    <script src="/gesman/js/EditarEquipo.js"></script>
    <script src="/gesman/menu/sidebar.js"></script>
</body>
</html>