<?php 
    function FnAgregarSesion($usuario) {
        try {
            $_SESSION['gesman']['CliId']=0;
            $_SESSION['gesman']['CliOdoId']=0;
            $_SESSION['gesman']['CliWhId']=0;
            $_SESSION['gesman']['PerId']=$usuario['perid'];
            $_SESSION['gesman']['Alias']=$usuario['pernombre'];
            $_SESSION['gesman']['Nombre']=$usuario['usunombre'];
            $_SESSION['gesman']['RolMan']=$usuario['rolman'];
            $_SESSION['gesman']['CliNombre']='UNKNOWN';
            return true;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    function FnModificarSesionCliente($sesion) {
        try {
            $_SESSION['gesman']['CliId']=$sesion['cliid'];
            $_SESSION['gesman']['CliOdoId']=$sesion['odoid'];
            $_SESSION['gesman']['CliWhId']=$sesion['almid'];
            $_SESSION['gesman']['CliNombre']=$sesion['clinombre'];
            return true;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    function FnValidarSesion(){
        try {
            if(!(empty($_SESSION['gesman']))){
                return true;
            }
            return false;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    function FnValidarSesionMan(){
        try {
            if(!(empty($_SESSION['gesman']['RolMan']))){
                return true;
            }
            return false;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    function FnValidarSesionManNivel1(){
        try {
            if($_SESSION['gesman']['RolMan']>0){
                return true;
            }
            return false;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    function FnValidarSesionManNivel2(){
        try {
            if($_SESSION['gesman']['RolMan']>1){
                return true;
            }
            return false;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    function FnValidarSesionManNivel3(){
        try {
            if($_SESSION['gesman']['RolMan']>2){
                return true;
            }
            return false;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    function FnEiminarSesion(){
        try {
            session_unset();
            session_destroy();
            return true;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

?>