<?php 
	session_start();
	if(empty($_SESSION['UserName']) || empty($_SESSION['CliId']) || empty($_SESSION['CliNombre'])){
        header("location:/gesman/Salir.php");
        exit();
    }

	$ID=empty($_GET['id']) ? 0 : $_GET['id'];
    $ESTADO=0;
    $ORDEN=array();
    $TAREOS=array();
    $ARCHIVOS=array();

	require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGesmanDb.php';
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/OrdenesData.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/ArchivosData.php";

	try{
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $ORDEN=FnBuscarOrden($conmy, $_SESSION['CliId'], $ID);
        if(!empty($ORDEN['id'])){
            $TAREOS=FnBuscarOrdenTareos($conmy, $ID);
            $ARCHIVOS=FnBuscarArchivos($conmy, array('refid'=>$ID, 'tabla'=>'ORD', 'tipo'=>'IMG'));
            if(!empty($ORDEN['estado'])){
                $ESTADO=$ORDEN['estado'];
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
	<title>ORDEN_<?php echo empty($ORDEN['nombre'])?'-':$ORDEN['nombre'];?></title>
	<link rel="shortcut icon" href="/mycloud/logos/favicon.ico">
	<link rel="stylesheet" href="/mycloud/library/fontawesome-free-5.9.0-web/css/all.css">
    <link rel="stylesheet" href="/mycloud/library/bootstrap-5.0.2-dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="/mycloud/library/gpemsac/css/gpemsac.css">
	<style>
		.head-td{
			border-right: 1pt solid #CFCFCF;
			text-align: center; 
			padding: 4px;
		}
		.img-container {
			width: auto;
			height: 300px;
			display: flex;
			justify-content: center;
			align-items: center;
			padding:4px;
		}

		.img-container img {
			max-width: 100%;
			max-height: 100%;
			object-fit: contain;/* Mantiene la relación de aspecto de la imagen */
		}
	</style>
</head>
<body>
	
	<div class="container-loader-full">
        <div class="loader-full"></div>
    </div>
	
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
						<p class="m-0">AV. LOS INCAS S/N - COMAS - LIMA - PERU</p>
						<p class="m-0">01-7130628 / 01-7130629</p>
					</div>
				</div>				
			</div>
			<div class="col-3 p-1 border">
				<div class="row d-flex align-items-center h-100">
					<div class="col-12">
						<p class="m-0 fw-bold">ORDEN DE TRABAJO</p>
						<p class="m-0 fw-bold"><?php echo empty($ORDEN['nombre'])?null:$ORDEN['nombre'];?></p>
						<input type="hidden" id="txtId" value="<?php echo $ID;?>">
					</div>
				</div>				
			</div>
		</div>

		<div class="row mb-1">
			<div class="col-8">
				<p class="m-0 fw-bold">CLIENTE</p>
				<p class="m-0"><?php echo $_SESSION['CliNombre'];?></p>
				<p class="m-0"><?php echo !empty($ORDEN['clicontacto'])?$ORDEN['clicontacto']:'';?></p>
			</div>
			<div class="col-4">
				<p class="m-0 fw-bold">FECHA</p>
				<p class="m-0"><?php echo empty($ORDEN['fecha'])?'-':$ORDEN['fecha'];?></p>
			</div>
		</div>

		<div class="row p-1">
            <div class="col-12 mb-0 border-bottom bg-light px-1">
                <p class="m-0 fw-bold">DATOS DE LA ORDEN</p>
            </div>
        </div>

		<div class="row mb-2">
			<div class="col-4">
				<p class="text-secondary m-0">Equipo:</p>
				<p class="m-0"><?php echo empty($ORDEN['equcodigo'])?'-':$ORDEN['equcodigo'];?></p>
			</div>
			<?php
				if(!empty($ORDEN['equkm'])){
					echo '
					<div class="col-4">
						<p class="text-secondary m-0">KM:</p>
						<p class="m-0">'.$ORDEN['equkm'].'</p>
					</div>';
				}
			?>

			<?php
				if(!empty($ORDEN['equhm'])){
					echo '
					<div class="col-4">
						<p class="text-secondary m-0">HM:</p>
						<p class="m-0">'.$ORDEN['equhm'].'</p>
					</div>';
				}
			?>
			
			
			<div class="col-4">
				<p class="text-secondary m-0">Tipo:</p>
				<p class="m-0"><?php echo empty($ORDEN['tipnombre'])?'-':$ORDEN['tipnombre'];?></p>
			</div>

			<?php
                if(!empty($ORDEN['sisnombre'])){
                    echo '
                    <div class="col-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Sistema</p> 
                        <p class="m-0 p-0">'.$ORDEN['sisnombre'].'</p>
                    </div>';
                }
            ?>
            <?php
                if(!empty($ORDEN['orinombre'])){
                    echo '
                    <div class="col-4">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Orígen</p> 
                        <p class="m-0 p-0">'.$ORDEN['orinombre'].'</p>
                    </div>';
                }
            ?>       

			<div class="col-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 13px;">Supervisor</p> 
                <p class="m-0 p-0"><?php echo empty($ORDEN['supervisor'])?'-':$ORDEN['supervisor'];?></p>
            </div>  

			<div class="col-6 col-sm-4 mb-1">
                <p class="m-0 text-secondary" style="font-size: 12px;">Estado</p>
                <?php
                    switch ($ESTADO){
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
		</div>
		
		<div class="row p-1">
            <div class="col-12 mb-0 px-1 border-bottom bg-light">
                <p class="m-0 fw-bold">TRABAJOS REALIZADOS</p>
            </div>
        </div>

		<div class="row mb-2">
			<div class="col-12 mb-1">
                <p class="m-0 text-secondary" style="font-size: 13px;">Actividades</p> 
                <p class="m-0 p-0"><?php echo empty($ORDEN['actnombre'])?'':$ORDEN['actnombre'];?></p>
            </div>
            <div class="col-12 mb-1">
                <p class="m-0 text-secondary" style="font-size: 13px;">Trabajos</p> 
                <p class="m-0 p-0"><?php echo empty($ORDEN['trabajos'])?'':$ORDEN['trabajos'];?></p>
            </div>
            <?php
                if(!empty($ORDEN['observaciones'])){
                    echo '
                    <div class="col-6 col-sm-4 mb-1">
                        <p class="m-0 text-secondary" style="font-size: 13px;">Observaciones</p> 
                        <p class="m-0 p-0">'.$ORDEN['observaciones'].'</p>
                    </div>';
                }
            ?>
		</div>
		
		<?php 
			if(count($TAREOS)>0){
				echo '
				<div class="row mb-1">
					<div class="col-12 border-bottom fw-bold bg-light px-1">PERSONAL ASIGNADO</div>
				</div>';

				echo '
				<div class="row mb-2">
					<div class="col-12 px-1 mb-2">				
						<table style="border-collapse: collapse; width:100%;">
							<thead>
								<tr style="background-color: #F7F7F7;">
									<th style="border-right: 1pt solid #CFCFCF; padding: 2px;">NOMBRE</th>
									<th style="border-right: 1pt solid #CFCFCF; text-align: center; padding: 2px;">INGRESO</th>
									<th style="border-right: 1pt solid #CFCFCF; text-align: center; padding: 2px;">SALIDA</th>
									<th style="border-right: 1pt solid #CFCFCF; text-align: right; padding: 2px;">MINUTOS</th>
								</tr>
							</thead>
							<tbody>';
							foreach ($TAREOS as $key=>$valor) {
								echo '
								<tr>
									<td style="vertical-align:middle; border-right: 1pt solid #CFCFCF; padding: 2px;">'.$valor['pernombre'].'</td>
									<td style="vertical-align:middle; border-right: 1pt solid #CFCFCF; padding: 2px; text-align:center;">'.$valor['ingreso'].'</td>
									<td style="vertical-align:middle; border-right: 1pt solid #CFCFCF; padding: 2px; text-align:center;">'.$valor['salida'].'</td>
									<td style="vertical-align:middle; border-right: 1pt solid #CFCFCF; padding: 2px; text-align:right;">'.$valor['minutos'].'</td>
								</tr>';
							}
							echo '
							</tbody>
						</table>
					</div>
				</div>';
			}
		?>

		<div class="row mb-1">
			<div class="col-12 border-bottom fw-bold bg-light px-1">PRODUCTOS Y REPUESTOS <i class="fas fa-plus-square" onclick="FnBuscarSaleOrders(this); return false;" style="cursor: pointer;"></i></div>
		</div>

		<div class="row">
			<div class="col-12 d-flex justify-content-between">
				<p class="m-0">DESCRIPCION</p>
				<p class="m-0">CANTIDAD</p>
			</div>
		</div>		
		<div class="row mb-2" id="tblSaleOrders"></div>

		<?php
			if(count($ARCHIVOS)>0){
				echo '
				<div class="row p-1">
					<div class="col-12 mb-0 border-bottom bg-light px-1">
						<p class="m-0 fw-bold">ANEXOS</p>
					</div>
				</div>';

				echo '<div class="row row-cols-1 row-cols-md-2 g-4 mb-3">';
				foreach($ARCHIVOS as $key=>$valor){
					echo '
					<div class="col">
						<div class="card h-100 text-center">
							<div class="card-body text-center p-1">
								<p class="m-0 card-title">'.$valor['titulo'].'</p>
								<p class="m-0 card-text">'.$valor['descripcion'].'</p>
							</div>
							<div class="img-container text-center">
								<img src="/mycloud/gesman/files/'.$valor['nombre'].'">
							</div>							
						</div>
					</div>';
				}
				echo '</div>';
			}			
		?>
	</div>

	<script src="/mycloud/library/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
	<script src="/mycloud/library/bootstrap-5-alerta-1.0/js/bootstrap-5-alerta-1.0.js"></script>
    <script src="/gesman/js/OrdenResumen.js"></script>
</body>
</html>