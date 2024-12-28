<?php
	session_start();
	require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
    require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGesmanDb.php';
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/OrdenesData.php";

	/** Error reporting */
	error_reporting(E_ALL);
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);
	date_default_timezone_set('Europe/London');

	if (PHP_SAPI == 'cli')
		die('This example should only be run from a Web Browser');

	/** Include PHPExcel */
	//require_once dirname(__FILE__) . '/mycloud/library/PHPExcel-1.8/Classes/PHPExcel.php';
	include($_SERVER['DOCUMENT_ROOT'].'/mycloud/library/PHPExcel-1.8/Classes/PHPExcel.php');

	// Crear un nuevo objeto PHPExcel.
	$objPHPExcel = new PHPExcel();

	// Configurar las propiedades del documento.
	$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
		->setLastModifiedBy("Maarten Balliauw")
		->setTitle("Office 2007 XLSX Test Document")
		->setSubject("Office 2007 XLSX Test Document")
		->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
		->setKeywords("office 2007 openxml php")
		->setCategory("Test result file");

	// Agregar la primra linea, cabecera.
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', 'ID')
		->setCellValue('B1', 'ORDEN')
		->setCellValue('C1', 'EQUIPO')
		->setCellValue('D1', 'TIPO')
		->setCellValue('E1', 'SISTEMA')
		->setCellValue('F1', 'ORIGEN')
		->setCellValue('G1', 'FECHA')
		->setCellValue('H1', 'KM')
		->setCellValue('I1', 'H.M.')
		->setCellValue('J1', 'TIPO_TRABAJO')
		->setCellValue('K1', 'ACTIVIDAD')
		->setCellValue('L1', 'TRABAJOS')
		->setCellValue('M1', 'OBSERVACIONES')
		->setCellValue('N1', 'SUPERVISOR')
		->setCellValue('O1', 'CONTACTO')
		->setCellValue('P1', 'ESTADO');

	// Poner en negrita la primera columna.
	$objPHPExcel->getActiveSheet()->getStyle("A1:P1")->getFont()->setBold(true);

	// Redimensionar las columnas
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(60);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(60);
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(40);
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);

	try{
		$conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		if(!FnValidarSesion()){throw new Exception("Se ha perdido la conexión.");}
        if(!FnValidarSesionManNivel2()){throw new Exception("Usuario no autorizado.");}
        if(empty($_GET['cliid']) || empty($_GET['fechainicial']) || empty($_GET['fechafinal'])){throw new Exception("La información esta incompleta.");}

		if(FnDiferenciaFechas($_GET['fechainicial'], $_GET['fechafinal'])>180){throw new Exception("No podemos generar un reporte mayor a 180 días.");}

		$search=array(
			'cliid'=>$_GET['cliid'],
			'fechainicial'=>$_GET['fechainicial'],
			'fechafinal'=>$_GET['fechafinal']
		);

		$datos=array();
		$datos=FnReporteOrdenes($conmy, $search);

		if(count($datos)){
			$i=2;
			foreach($datos as $key=>$valor){
				$estado='';
				switch ($valor['estado']){
					case 0:
						$estado='ANULADO';
						break;
					case 1:
						$estado='ABIERTO';
						break;
					case 2:
						$estado='PROCESO';
						break;
					case 3:
						$estado='CERRADO';
						break;
					case 4:
						$estado='OBSERVADO';
						break;
					default:
						$estado='UNKNOWN';
				}

				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A'.$i, $valor['id'])
					->setCellValue('B'.$i, $valor['nombre'])
					->setCellValue('C'.$i, $valor['equcodigo'])
					->setCellValue('D'.$i, $valor['tipnombre'])
					->setCellValue('E'.$i, $valor['sisnombre'])
					->setCellValue('F'.$i, $valor['orinombre'])
					->setCellValue('G'.$i, PHPExcel_Shared_Date::PHPToExcel(date("d/m/Y", strtotime($valor['fecha']))))
					->setCellValue('H'.$i, $valor['equkm'])
					->setCellValue('I'.$i, $valor['equhm'])
					->setCellValue('J'.$i, $valor['tiptrabajo'])
					->setCellValue('K'.$i, $valor['actnombre'])
					->setCellValue('L'.$i, $valor['descripcion'])
					->setCellValue('M'.$i, $valor['observaciones'])
					->setCellValue('N'.$i, $valor['supervisor'])
					->setCellValue('O'.$i, $valor['clicontacto'])
					->setCellValue('P'.$i, $estado);
				$i+=1;
			};
			$objPHPExcel->getActiveSheet()->getStyle('G2:G'.$i)->getNumberFormat()->setFormatCode("dd/mm/yyyy");
		}else{
			throw new Exception("No se encontró resultados.");			
		}
		$conmy=null;
	} catch(PDOException $ex) {
        $conmy=null;
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A2', $ex->getMessage());
    } catch (Exception $ex) {
        $conmy=null;
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A2', $ex->getMessage());
    }

	// Renombrar la hoja
	$objPHPExcel->getActiveSheet()->setTitle('reporte_ordenes_180_dias');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	// Redirect output to a client’s web browser (Excel2007)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="reporte_ordenes_180_dias_'.date("YmdHis").'.xlsx"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
?>