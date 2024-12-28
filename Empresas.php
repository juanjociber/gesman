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

    require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGesmanDb.php';
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/ClientesData.php";

    $CLIENTES=array();

    try{
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $CLIENTES=FnListarClientes2($conmy);
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
    <title>Empresas | GPEM SAC.</title>
    <link rel="shortcut icon" href="/mycloud/logos/favicon.ico">
    <link rel="stylesheet" href="/mycloud/library/fontawesome-free-5.9.0-web/css/all.css">
    <link rel="stylesheet" href="/mycloud/library/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/mycloud/library/gpemsac/css/gpemsac.css">
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
        .container-wa{
            position: absolute;
            z-index: 3;
            right: 0px;
            margin:0px;
            padding:0px;
        }
    </style>   
</head>

<body>
    
    <?php require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/menu/sidebar.php';?>

    <div class="container section-top">        
        <div class="row p-1">
            <div class="col-12 border-bottom fw-bold m-0 fs-4"><?php echo $_SESSION['gesman']['CliNombre'];?><span class="text-secondary" style="font-style: italic; font-size: 12px;"> Predeterminado</span></div>
        </div>
        <div class="row p-2 position-relative" style="z-index:0;">
            <?php
            if(count($CLIENTES)>0){
                foreach($CLIENTES as $key=>$valor){
                    $set = 'far fa-circle';
                    if($valor['id']==$_SESSION['gesman']['CliId']){
                        $set = 'fas fa-check-circle';
                    }
                    echo '
                    <div class="col-12 divselect border-bottom border-secondary mb-1 p-1">
                        <a class="link-colecciones" href="#" onclick="FnModalCambiarEmpresa('.$valor['id'].','."'".$valor['alias']."'".'); return false;">
                            <div class="row position-relative">
                                <div class="container-wa text-end pe-3 text-primary">
                                        <i class="'.$set.' fs-5"></i>
                                    </div>
                                <div class="col-12">
                                    <p class="m-0 text-secondary" style="font-size:13px;">'.$valor['ruc'].'</p>
                                    <p class="m-0">'.$valor['alias'].'</p>
                                </div>
                            </div>
                        </a>
                    </div>';
                }
            }else{
                echo '<div class="col-12"><p class="m-0">No hay Clientes disponibles.</p></div>';
            }
            ?>
    </div>

    <div class="modal fade" id="modalCambiarEmpresa" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Establecer Empresa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>                
                <div class="modal-body pb-0">
                    <div class="form-row">
                        <div class="col-md-12">
                            <p class="mb-2 fw-bold text-center text-secondary">Haga clic en el boton Confirmar para establecer la siguiente Empresa:</p>
                            <input type="text" class="d-none" id="txtId" readonly>
                            <input type="text" class="form-control text-center border-0 fs-4 fw-bold" id="txtEmpresa">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="FnCambiarEmpresa(); return false;">Confirmar</button>
                </div>              
            </div>
        </div>
    </div>

    <div class="container-loader-full">
        <div class="loader-full"></div>
    </div>

    <script src="/mycloud/library/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
    <script src="/mycloud/library/bootstrap-5-alerta-1.0/js/bootstrap-5-alerta-1.0.js"></script>
    <script src="/gesman/js/Empresas.js"></script>
    <script src="/gesman/menu/sidebar.js"></script>
</body>
</html>