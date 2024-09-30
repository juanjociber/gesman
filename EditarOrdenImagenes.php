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
                        <li class="breadcrumb-item fw-bold"><a href="/gesman/EditarOrdenTareos.php?orden=<?php echo $IdOt;?>" class="text-decoration-none">TAREOS</a></li>
                        <li class="breadcrumb-item active fw-bold" aria-current="page">IMAGENES</li>
                        <li class="breadcrumb-item fw-bold"><a href="/gesman/EditarOrdenVales.php?orden=<?php echo $IdOt;?>" class="text-decoration-none">VALES</a></li>
                    </ol>
                </nav>
            </div>
        </div>
        
        <div class="row border-bottom mb-3">
            <div class="col-12 mb-2">
                <button type="button" class="btn btn-outline-primary form-control<?php echo $Visible;?>" onclick="FnModalAgregarArchivo(); return false;"><i class="fas fa-plus"></i> Archivo</button>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php
            try{
                $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $stmt=$conmy->prepare("select id, nombre, descripcion, tipo from tblarchivos where refid=:RefId and tabla='ORD' and estado=2;");
                $stmt->execute(array('RefId'=>$IdOt));
                if($stmt->rowCount()>0){              
                    $ARCHIVO_TIPO='';
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                        if($row['tipo']=='IMG'){
                            $ARCHIVO_TIPO=' fa-file-image';
                        }elseif($row['tipo']=='PDF'){
                            $ARCHIVO_TIPO=' fa-file-pdf';
                        }else{
                            $ARCHIVO_TIPO=' fa-question-circle';
                        }
                        echo '
                        <div class="col">
                            <div class="card h-100">
                                <div class="card-body p-1">
                                    <div class="d-flex align-items-center divselect me-4" datatipo="'.$row['tipo'].'" datanombre="'.$row['nombre'].'" datadescripcion="'.$row['descripcion'].'" onclick="FnModalVerArchivo(this); return false;">
                                        <i class="fas'.$ARCHIVO_TIPO.' fs-1 m-1"></i>
                                        <div class="p-2">
                                            <p class="m-0 fw-bold">'.$row['tipo'].'</p>
                                            <p class="m-0">'.$row['descripcion'].'</p>
                                        </div>
                                    </div>                        
                                </div>
                                <div class="container-wa">
                                    <a class="text-decoration-none text-secondary" href="#" onclick="FnEliminarImagen('.$row['id'].'); return false;"><h2><i class="fas fa-times link-wa"></i></h2></a>
                                </div>
                            </div>
                        </div>';
                    }
                }else{
                    echo '<div class="col-12"><p class="fst-italic">No hay archivos asociados a esta Órden.</p></div>';
                }
                $stmt=null;
            }catch(PDOException $e){
                $stmt=null;
                echo '<div class="col-12"><p class="fst-italic">'.$e->getMessage().'</p></div>';
            }  
            ?>
        </div>
    </div>

    <div class="modal fade" id="modalAgregarImagen" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Imágen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pb-1">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <p class="m-0 text-secondary" style="font-size: 12px;">Descripción</p>
                            <input type="text" class="form-control" id="txtDescripcion">
                        </div>                        
                        <div class="col-12">
                            <p class="m-0 text-secondary" style="font-size: 12px;">Imágen</p>
                            <input id="fileImagen" type="file" accept="image/*,.pdf" class="form-control mb-2"/>
                        </div>
                        <div class="col-12 m-0">
                            <div class="col-md-12 text-center" id="divImagen"><i class="fas fa-images fs-2"></i></div>
                        </div>
                    </div>
                </div>
                <div id="msjAgregarImagen" class="modal-body pt-1"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="FnAgregarImagen(); return false;">Guardar</button>
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

    <script src="/mycloud/library/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
    <script src="/gesman/js/EditarOrdenImagenes.js"></script>
    <script src="/gesman/menu/sidebar.js"></script>
</body>
</html>