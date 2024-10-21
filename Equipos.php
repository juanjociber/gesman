<?php 
    session_start();

    if(!isset($_SESSION['UserName']) || !isset($_SESSION['CliId'])){
        header("location:/gesman");
        exit();
    }

    require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGesmanDb.php';
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/FlotasData.php";

    $FLOTAS=array();

    try{
      $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $FLOTAS=FnListarClienteFlotas($conmy, $_SESSION['CliId']);
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
    <title>Equipos | GPEM SAC</title>
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
    
    <div class="row p-1 mb-3">
      <div class="col-12 border-bottom fw-bold fs-5">
        <p class="m-0 p-0"><?php echo $_SESSION['CliNombre'];?></p>
      </div>
    </div>
    <div class="row">
      <div class="col-6 col-sm-4 mb-2">
          <p class="m-0" style="font-size:12px;">Código</p>
          <input type="text" class="form-control" id="txtEquipo"/>
      </div>
      <div class="col-6 col-sm-4 mb-2">
        <p class="m-0" style="font-size:12px;">Flota</p>
        <select class="form-select" id="cbFlota">
          <option value="0">Seleccionar</option>
            <?php 
              if(count($FLOTAS)>0){
                foreach ($FLOTAS as $key=>$valor) {
                  echo '<option value="'.$valor['id'].'">'.$valor['nombre'].'</option>';
                }
              }
            ?>
        </select>
      </div>
      <div class="col-4 d-none d-sm-block mb-2">
          <p class="m-0" style="font-size:12px;">Estado</p>
          <select class="form-select" id="cbEstado">
              <option value="0">Seleccionar</option>
              <option value="2">ACTIVO</option>
              <option value="1">INACTIVO</option>
          </select>
      </div>
    </div>

    <div class="row border-bottom mb-2">
      <div class="col-6 mb-2">
        <button type="button" class="btn btn-outline-primary form-control" onclick="FnModalAgregarEquipo(); return false;"><i class="fas fa-plus"></i> EQUIPO</button>
      </div>
      <div class="col-6 mb-2">
        <button type="button" class="btn btn-outline-primary form-control" onclick="FnBuscarEquipos(); return false;"><i class="fas fa-search"></i> BUSCAR</button>
      </div>
    </div>
        
    <div class="row mb-2" id="tblEquipos">
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

  <div class="modal fade" id="modalAgregarEquipo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">AGREGAR EQUIPO</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body pb-1 mb-1">
          <div class="row">                        
            <div class="col-6 mb-2">
              <p class="m-0 text-secondary" style="font-size:12px;">Flota</p>
              <select class="form-select" id="cbFlota1">
                <option value="0">Seleccionar</option>
                  <?php 
                    if(count($FLOTAS)>0){
                      foreach ($FLOTAS as $key=>$valor) {
                        echo '<option value="'.$valor['id'].'">'.$valor['nombre'].'</option>';
                      }
                    }
                  ?>
              </select>
            </div>
            <div class="col-6 mb-2">
              <p class="m-0 text-secondary" style="font-size: 12px;">Código</p>
              <input type="text" class="form-control" id="txtCodigo1">
            </div>
            <div class="col-12 mb-2">
              <p class="m-0 text-secondary" style="font-size: 12px;">Nombre</p>
              <input type="text" class="form-control" id="txtNombre1">
            </div>                      
            <div class="col-6 mb-2">
              <p class="m-0 text-secondary" style="font-size:12px;">Marca:</label>
              <input type="text" class="form-control" id="txtMarca1"/>
            </div>
            <div class="col-6 mb-2">
              <p class="m-0 text-secondary" style="font-size:12px;">Modelo:</label>
              <input type="text" class="form-control" id="txtModelo1"/>
            </div>
            <div class="col-6 mb-2">
              <p class="m-0 text-secondary" style="font-size:12px;">Placa:</label>
              <input type="text" class="form-control" id="txtPlaca1"/>
            </div>
            <div class="col-6 mb-2">
              <p class="m-0 text-secondary" style="font-size:12px;">VIN:</label>
              <input type="text" class="form-control" id="txtSerie1"/>
            </div>
            <div class="col-6 mb-2">
              <p class="m-0 text-secondary" style="font-size:12px;">Motor:</label>
              <input type="text" class="form-control" id="txtMotor1"/>
            </div>
            <div class="col-6 mb-2">
              <p class="m-0 text-secondary" style="font-size:12px;">Transmision:</label>
              <input type="text" class="form-control" id="txtTransmision1"/>
            </div>
            <div class="col-6 mb-2">
              <p class="m-0 text-secondary" style="font-size:12px;">Diferencial:</label>
              <input type="text" class="form-control" id="txtDiferencial1"/>
            </div>
            <div class="col-6 mb-2">
              <p class="m-0 text-secondary" style="font-size:12px;">Año:</label>
              <input type="text" class="form-control" id="txtAnio1"/>
            </div>
            <div class="col-6 mb-2">
              <p class="m-0 text-secondary" style="font-size:12px;">Fabricante:</label>
              <input type="text" class="form-control" id="txtFabricante1"/>
            </div>
            <div class="col-6 mb-2">
              <p class="m-0 text-secondary" style="font-size:12px;">Procedencia:</label>
              <input type="text" class="form-control" id="txtProcedencia1"/>
            </div>
            <div class="col-6 mb-2">
              <p class="m-0 text-secondary" style="font-size:12px;">Ubicación:</label>
              <input type="text" class="form-control" id="txtUbicacion1"/>
            </div>
            <div class="col-12 mb-2">
              <p class="m-0 text-secondary" style="font-size:12px;">Características:</label>
              <input type="text" class="form-control" id="txtDatos1"/>
            </div>      
          </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="FnAgregarEquipo(); return false;">GUARDAR</button>
        </div>              
      </div>
    </div>
  </div>

  <div class="container-loader-full">
      <div class="loader-full"></div>
  </div>

  <script src="/mycloud/library/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
  <script src="/mycloud/library/bootstrap-5-alerta-1.0/js/bootstrap-5-alerta-1.0.js"></script>
  <script src="/gesman/js/Equipos.js"></script>
  <script src="/gesman/menu/sidebar.js"></script>
</body>
</html>