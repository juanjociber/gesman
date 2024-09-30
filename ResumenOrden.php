<?php
    session_start();

    if(empty($_SESSION['UserName']) || empty($_SESSION['CliId']) || empty($_SESSION['CliNombre'])){
        header("location:/gesman/Salir.php");
        exit();
    }

    date_default_timezone_set("America/Lima");
    require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGesmanDb.php';

    $Id = 0;
    $Ot = '';
    $Activo = 0;
    $TipoOt = '';
    $Sistema = '';
    $Origen = '';
    $Fecha ='';
    $TipoTrabajo = '';
    $Actividad = '';
    $Descripcion = '';
    $Observaciones = '';
    $Km = 0;
    $Supervisor = '';
    $Contacto = '';
    $Estado = 0;

    if(!empty($_GET['orden'])){
        try{
            $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt=$conmy->prepare("select idot, ot, activo, tipoot, sistema, origen, fechainicial, actividad, descripcion, observaciones, km, supervisor, contacto, estado from man_ots where idot=:IdOt and idcliente=:IdCliente;");
            $stmt->execute(array('IdOt'=>$_GET['orden'], 'IdCliente'=>$_SESSION['CliId']));
            $row=$stmt->fetch();
            if($row){
                $Id = $row['idot'];
                $Ot = $row['ot'];
                $Activo = $row['activo'];
                $TipoOt = $row['tipoot'];
                $Sistema = $row['sistema'];
                $Origen = $row['origen'];
                $Fecha = $row['fechainicial'];
                $Actividad = $row['actividad'];
                $Descripcion = $row['descripcion'];
                $Observaciones = $row['observaciones'];
                $Km = $row['km'];
                $Supervisor = $row['supervisor'];
                $Contacto = $row['contacto'];
                $Estado = $row['estado'];
            }
        }catch(PDOException $ex){
            echo $ex->getMessage();
        }
    }

    $claseHabilitado = "btn-outline-secondary";
    $atributoHabilitado = " disabled";
    if($Estado == 1 || $Estado == 2 || $Estado == 4){
        $claseHabilitado = "btn-outline-primary";
        $atributoHabilitado = "";
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
                <button type="button" class="btn <?php echo $claseHabilitado;?> fw-bold"<?php echo $atributoHabilitado;?> onclick="FnEditarOrden(); return false;"><i class="fas fa-edit"></i><span class="d-none d-sm-block"> Editar</span></button>
                <button type="button" class="btn <?php echo $claseHabilitado;?> fw-bold"<?php echo $atributoHabilitado;?> onclick="FnModalFinalizarOrden(); return false;"><i class="fas fa-check-square"></i><span class="d-none d-sm-block"> Finalizar</span></button>
            </div>
        </div>

        <div class="row border-bottom mb-2 fs-5">
            <div class="col-12 fw-bold d-flex justify-content-between">
                <p class="m-0 p-0"><?php echo $_SESSION['CliNombre'];?></p>
                <input type="text" class="d-none" id="txtId" value="<?php echo $Id;?>">
                <p class="m-0 p-0 text-center text-secondary">OT <?php echo $Ot;?></p>
            </div>
        </div>

        <div class="row p-1 mb-0">
            <div class="col-12 mb-0 border-bottom bg-light">
                <p class="m-0 fw-bold">Órden</p>
            </div>
        </div>

        <div class="row p-1 mb-2">
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Fecha</p> 
                <p class="m-0 p-0"><?php echo $Fecha;?></p>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Activo</p> 
                <p class="m-0 p-0"><?php echo $Activo;?></p>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Km/Hr</p> 
                <p class="m-0 p-0"><?php echo $Km;?></p>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Tipo</p> 
                <p class="m-0 p-0"><?php echo $TipoOt;?></p>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Sistema:</p> 
                <p class="m-0 p-0"><?php echo $Sistema;?></p>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Orígen:</p> 
                <p class="m-0 p-0"><?php echo $Origen;?></p>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Supervisor</p> 
                <p class="m-0 p-0"><?php echo $Supervisor;?></p>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Contacto</p> 
                <p class="m-0 p-0"><?php echo $Contacto;?></p>
            </div>
            <div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Estado</p>
                <?php
                    switch ($Estado){
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
                <p class="m-0 text-secondary" style="font-size: 12px;">Actividad</p> 
                <p class="m-0 p-0"><?php echo $Actividad;?></p>
            </div>
            <div class="col-12 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Descripción:</p> 
                <p class="m-0 p-0"><?php echo $Descripcion;?></p>
            </div>
            <div class="col-12 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Observaciones</p> 
                <p class="m-0 p-0"><?php echo $Observaciones;?></p>
            </div>            
        </div>

        <div class="row p-1 mb-0">
            <div class="col-12 mb-0 border-bottom bg-light">
                <p class="m-0 fw-bold">Tareos</p>
            </div>
        </div>
        
        <div class="row mb-2 p-1">
            <?php
            try{
                $stmt=$conmy->prepare("select personal, ingreso, salida, tmin from man_tareos where idot=:IdOt;");
                $stmt->execute(array('IdOt'=>$Id));
                if($stmt->rowCount()>0){                   
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){                        
                        echo '
                        <div class="col-12 mb-1 pb-1 border-bottom">
                            <div class="row">
                                <div class="col-12 d-flex justify-content-between">
                                    <p class="m-0 p-0">'.$row['personal'].'</p>
                                    <p class="m-0 p-0 fw-bold">'.$row['tmin'].' Min</p>
                                </div>
                                <div class="col-12 d-flex justify-content-between">
                                    <p class="m-0 p-0">'.$row['ingreso'].'</p>
                                    <p class="m-0 p-0">'.$row['salida'].'</p>                                    
                                </div>
                            </div>
                        </div>';
                    }
                }else{
                    echo '
                    <div class="col-12">
                        <p class="fst-italic">No hay personal asociado a esta Órden.</p>
                    </div>';
                }
            }catch(PDOException $e){
                echo '
                <div class="col-12">
                    <p class="fst-italic">'.$e->getMessage().'</p>
                </div>';
            }
            ?>
        </div>

        <div class="row p-1 mb-1">
            <div class="col-12 mb-0 border-bottom bg-light">
                <p class="m-0 fw-bold">Anexos</p>
            </div>
        </div>

        <?php
        try{
            $stmt=$conmy->prepare("select nombre, descripcion, tipo from tblarchivos where refid=:RefId and tabla=:Tabla;");
            $stmt->execute(array(':RefId'=>$Id, ':Tabla'=>'ORD'));
            if($stmt->rowCount()>0){
                echo '<div class="row row-cols-1 row-cols-md-3 g-4 mb-3">';
                $icono='';
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    switch ($row['tipo']) {
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
                        <div class="card h-100 divselect" onclick="FnModalVerArchivo('."'".$row['nombre']."'".', '."'".$row['tipo']."'".'); return false;">
                            <div class="card-body d-flex justifi-between-content align-items-center p-0">
                                <div class="p-2">
                                    <i class="fas'.$icono.'fs-1 text-secondary"></i>
                                </div>
                                <div class="p-2">
                                    <p class="m-0 fw-bold">'.$row['tipo'].'</p>
                                    <p class="m-0">'.$row['descripcion'].' '.$row['nombre'].'</p>
                                </div>
                            </div>
                        </div>
                    </div>';
                }
                echo '</div>';
            }else{
                echo '<div class="row mb-2 p-1"><div class="col-12"><p class="fst-italic">No hay archivos para esta Órden.</p></div></div>';
            }
        }catch(PDOException $e){
            echo '<div class="row mb-2 p-1"><div class="col-12"><p class="fst-italic">'.$e->getMessage().'</p></div></div>';
        }
        ?>
    </div>
     
    <div class="modal fade" id="modalFinalizarOrden" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Finalizar Órden de Trabajo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>                
                <div class="modal-body pb-1">
                    <div class="row text-center fw-bold pt-3">                        
                        <p class="text-center">Para finalizar la Órden <?php echo $Ot;?> haga clic en el botón CONFIRMAR.</p>                    
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

    <div class="container-loader-full">
        <div class="loader-full"></div>
    </div>

    <script src="/mycloud/library/jquery-3.5.1/jquery-3.5.1.js"></script>
    <script src="/mycloud/library/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
    <script src="/gesman/js/ResumenOrden.js"></script>
    <script src="/gesman/menu/sidebar.js"></script>

</body>
</html>