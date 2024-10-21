<?php
    try{
        if(empty($_GET['imagen'])){throw new Exception("No se encontró la Imagen");}
        $ruta=$_SERVER['DOCUMENT_ROOT']."/mycloud/gesman/equipos/".$_GET['imagen'];
        if (file_exists($ruta)) {
            header("Content-Type: image/jpeg");
            readfile($ruta);
        } else {
            header("Content-Type: image/jpeg");
            readfile($_SERVER['DOCUMENT_ROOT']."/gesman/sources/unknown.jpg");
        }
    }
    catch(Exception $e){
        header("Content-Type: image/jpeg");
        readfile($_SERVER['DOCUMENT_ROOT']."/gesman/sources/unknown.jpg");
    }
?>