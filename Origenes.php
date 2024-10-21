<?php 
    session_start();

    if(!isset($_SESSION['UserName']) || !isset($_SESSION['CliId'])){
        header("location:/gesman");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Origenes | GPEM SAC</title>
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
    </style>
</head>
<body>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/menu/sidebar.php';?>

  <div class="container section-top">
    
    <div class="row mb-3">
      <div class="col-12 border-bottom fw-bold fs-5">
        <p class="m-0 p-0"><?php echo $_SESSION['CliNombre'];?></p>
      </div>
    </div>
    <div class="row mb-2">
      <div class="col-6 mb-2">
          <p class="m-0" style="font-size:12px;">Nombre</p>
          <input type="text" class="form-control" id="txtNombre"/>
      </div>
      <div class="col-6 mb-2">
          <p class="m-0" style="font-size:12px;">Estado</p>
          <select class="form-select" id="cbEstado">
              <option value="0">Seleccionar</option>
              <option value="1">INACTIVO</option>
              <option value="2">ACTIVO</option>
          </select>
      </div>
    </div>

    <div class="row border-bottom mb-2 pb-1">
      <div class="col-6 mb-2">
        <button type="button" class="btn btn-outline-primary form-control" onclick="FnModalAgregarOrigen(); return false;"><i class="fas fa-plus"></i> ORIGEN</button>
      </div>
      <div class="col-6 mb-2">
        <button type="button" class="btn btn-outline-primary form-control" onclick="FnBuscarOrigenes(); return false;"><i class="fas fa-search"></i> BUSCAR</button>
      </div>
    </div>
        
    <div class="row mb-2" id="tblOrigenes">
        <div class="col-12">
            <p class="fst-italic">Haga clic en el botón Buscar para obtener resultados.</p>
        </div>
    </div>
        
    <div class="row mb-3">
        <div class="col-12 font-weight-bold d-flex justify-content-center mb-3">
            <button type="button" id="btnPrimero" class="btn btn-sm btn-outline-primary d-none mx-2" onclick="FnBuscarPrimero(); return false;">PRIMERO</button>
            <button type="button" id="btnSiguiente" class="btn btn-sm btn-outline-primary d-none mx-2" onclick="FnBuscarSiguiente(); return false;">SIGUIENTE</button>
        </div>
    </div>
  </div>

  <div class="modal fade" id="modalAgregarOrigen" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">AGREGAR ORIGEN</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body pb-1 mb-1">
          <div class="row">
            <div class="col-12 mb-2">
              <p class="m-0 text-secondary" style="font-size: 12px;">Nombre</p>
              <input type="text" class="form-control" id="txtNombre2">
            </div>   
          </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="FnAgregarOrigen(); return false;">GUARDAR</button>
        </div>              
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalModificarOrigen" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">MODIFICAR ORIGEN</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body pb-1 mb-1">
          <div class="row">
            <div class="col-12 mb-2">
              <p class="m-0 text-secondary" style="font-size: 12px;">Nombre</p>
              <input type="text" class="form-control" id="txtNombre3">
              <input type="hidden" id="txtId3">
            </div>
            <div class="col-12 mb-2">
                <p class="m-0" style="font-size:12px;">Estado</p>
                <select class="form-select" id="cbEstado3">
                    <option value="1">INACTIVO</option>
                    <option value="2">ACTIVO</option>
                </select>
            </div>  
          </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="FnModificarOrigen(); return false;">GUARDAR</button>
        </div>              
      </div>
    </div>
  </div>

  <div class="container-loader-full">
      <div class="loader-full"></div>
  </div>

  <script src="/mycloud/library/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
  <script src="/mycloud/library/bootstrap-5-alerta-1.0/js/bootstrap-5-alerta-1.0.js"></script>
  <script src="/gesman/js/Origenes.js"></script>
  <script src="/gesman/menu/sidebar.js"></script>
</body>
</html>