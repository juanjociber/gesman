<?php
    $archivo = $_SERVER['DOCUMENT_ROOT'].'/mycloud/gesman/sources/plantilla_odometros.xlsx';
    if (!file_exists($archivo)) {
        echo "El fichero $archivo no existe";
        exit;
    }
    header('Content-Disposition: attachment;filename="'.'plantilla_odometros.xlsx"');
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Length: '.filesize($archivo));
    header('Cache-Control: max-age=0');
    readfile($archivo);
    exit;
?>