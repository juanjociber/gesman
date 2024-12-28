<?php
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";

    if(!FnValidarSesion()){
        header("location:/gesman/Salir.php");
        exit();
    }

    if(!FnValidarSesionManNivel2()){
        header("HTTP/1.1 403 Forbidden");
        //echo "Acceso no autorizado.";
        exit();
    }

    if(empty($_GET['id']) || empty($_GET['cliid'])){
        header("HTTP/1.1 404 Not Found");
        exit();
    }

    $ORDEN=array();
    $TAREOS=array();
    $ARCHIVOS=array();

    require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGesmanDb.php';
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/OrdenesData.php";

    try{
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $ORDEN=FnBuscarOrden($conmy, $_GET['cliid'], $_GET['id']);
        if(!empty($ORDEN['id'])){
            $TAREOS=FnBuscarOrdenTareos($conmy, $_GET['id']);
            $ARCHIVOS=FnBuscarOrdenArchivos($conmy, $_GET['id'], 'ORD');
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
    <style>
        .divselect {
            cursor: pointer;
            transition: all .25s ease-in-out;
        }
        .divselect:hover {
            background-color: #ccd1d1;
            transition: background-color .5s;
        }
    </style>
</head>
<body>
    <div class="container" style="font-size: 12px; margin-top:30px;">
    
        <div class="row mb-3 text-center">
			<div class="col-3 p-1 border">
				<div class="row d-flex align-items-center h-100">
					<div class="col-12">
						<img class="img-fluid" src="/mycloud/logos/logo-gpem.png">
					</div>
				</div>
			</div>
			<div class="col-6 p-1 border">
				<div class="row d-flex align-items-center h-100">
					<div class="col-12">
						<p class="m-0 fw-bold">GESTION DE PROCESOS EFICIENTES DE MANTENIMIENTO S.A.C.</p>
						<p class="m-0 d-none d-sm-block">AV. LOS INCAS S/N - COMAS - LIMA - PERU</p>
						<p class="m-0 d-none d-sm-block">01-7130628 / 01-7130629</p>
					</div>
				</div>				
			</div>
			<div class="col-3 p-1 border">
				<div class="row d-flex align-items-center h-100">
					<div class="col-12">
						<p class="m-0 fw-bold">ORDEN DE TRABAJO</p>
						<p class="m-0 fw-bold"><?php echo empty($ORDEN['nombre'])?'-':$ORDEN['nombre'];?></p>
						<input type="hidden" id="txtId" value="<?php echo $_GET['id'];?>">
					</div>
				</div>				
			</div>
		</div>

        <div class="row px-1 mb-2">
            <div class="col-12 col-sm-4 mb-1">
				<p class="m-0 fw-bold">Cliente</p>
				<p class="m-0"><?php echo $_SESSION['gesman']['CliNombre'];?></p>
			</div>
			<div class="col-6 col-sm-4 mb-1">
				<p class="m-0 fw-bold">Fecha</p>
				<p class="m-0"><?php echo empty($ORDEN['fecha'])?'-':$ORDEN['fecha'];?></p>
			</div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Estado</p>
                <?php
                    if(!empty($ORDEN['estado'])){
                        switch ($ORDEN['estado']){
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
                    }else{
                        echo '<p class="m-0">-</p>';
                    }                   
                ?>
            </div>       
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Equipo</p> 
                <p class="m-0"><?php echo empty($ORDEN['equcodigo'])?'-':$ORDEN['equcodigo'];?></p>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 13px;">KM.</p> 
                <p class="m-0"><?php echo empty($ORDEN['equkm'])?'-':$ORDEN['equkm'];?></p>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 13px;">H.M.</p> 
                <p class="m-0"><?php echo empty($ORDEN['equhm'])?'-':$ORDEN['equhm'];?></p>
            </div>         
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 13px;">Tipo</p> 
                <p class="m-0"><?php echo empty($ORDEN['tipnombre'])?'-':$ORDEN['tipnombre'];?></p>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 13px;">Sistema</p> 
                <p class="m-0"><?php echo empty($ORDEN['sisnombre'])?'':$ORDEN['sisnombre'];?></p>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 13px;">Orígen</p> 
                <p class="m-0"><?php echo empty($ORDEN['orinombre'])?'-':$ORDEN['orinombre'];?></p>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 13px;">Supervisor</p> 
                <p class="m-0"><?php echo empty($ORDEN['supervisor'])?'-':$ORDEN['supervisor'];?></p>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 13px;">Contacto</p> 
                <p class="m-0"><?php echo empty($ORDEN['clicontacto'])?'-':$ORDEN['clicontacto'];?></p>
            </div>            
            <div class="col-12 mb-1">
                <p class="m-0 text-secondary" style="font-size: 13px;">Actividades</p> 
                <p class="m-0"><?php echo empty($ORDEN['actnombre'])?'-':$ORDEN['actnombre'];?></p>
            </div>
            <div class="col-12 mb-1">
                <p class="m-0 text-secondary" style="font-size: 13px;">Trabajos</p> 
                <p class="m-0"><?php echo empty($ORDEN['trabajos'])?'-':$ORDEN['trabajos'];?></p>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 13px;">Observaciones</p> 
                <p class="m-0"><?php echo empty($ORDEN['observaciones'])?'-':$ORDEN['observaciones'];?></p>
            </div>
        </div>

        <div class="row px-1 mb-2">
            <div class="col-12">
                <div class="border-bottom bg-light px-1 mb-2">
                    <p class="m-0 fw-bold">TAREOS</p>
                </div>        
                <div class="row">
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
            </div>
        </div>

        <div class="row px-1 mb-2">
            <div class="col-12">
                <div class="border-bottom fw-bold bg-light">PRODUCTOS Y REPUESTOS <i class="fas fa-plus-square" onclick="FnBuscarSaleOrders(this); return false;" style="cursor: pointer;"></i></div>
                <div class="row">
                    <div class="col-12 d-flex justify-content-between">
                        <p class="m-0">DESCRIPCION</p>
                        <p class="m-0">CANTIDAD</p>
                    </div>
                </div>
                <div class="row mb-2" id="tblSaleOrders"></div>
            </div>
        </div>

        <div class="row px-1 mb-2">
            <div class="col-12">
                <div class="border-bottom bg-light px-1 mb-2">
                    <p class="m-0 fw-bold">ANEXOS</p>
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
                            <div class="card h-100 divselect" datatipo="'.$valor['tipo'].'" datanombre="'.$valor['nombre'].'" datatitulo="'.$valor['titulo'].'" onclick="FnModalVerArchivo(this); return false;">
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
        </div>
    </div>

    <div class="modal fade" id="modalVerArchivo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">ARCHIVO</h5>
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

    <div class="container-loader-full">
        <div class="loader-full"></div>
    </div>

    <script src="/mycloud/library/jquery-3.5.1/jquery-3.5.1.js"></script>
    <script src="/mycloud/library/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
    <script src="/mycloud/library/bootstrap-5-alerta-1.0/js/bootstrap-5-alerta-1.0.js"></script>
    <script src="/gesman/js/AdmOrden.js"></script>

</body>
</html>