<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGesmanDb.php';

$msg="<div class='alert alert-info p-2 mb-0 text-center' role='alert'><strong>Ingrese sus credenciales.</strong></div>";
$estado=false;

if(!empty($_POST['usu']) and !empty($_POST['psw'])){
    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	
        $stmt=$conmy->prepare('select nombre, usuario, lman from sis_usuarios where usuario=:Usuario and clave=:Clave and estado=:Estado');
        $stmt->execute(array(':Usuario'=>$_POST['usu'], ':Clave'=>$_POST['psw'], 'Estado'=>1));
        if ($stmt->rowCount()==1){
            $row = $stmt->fetch();
            $estado=true;
            $_SESSION['UserNombre']=$row['nombre'];
            $_SESSION['UserName']=$row['usuario'];
            $_SESSION['RolMan']=$row['lman'];
            $_SESSION['CliId']=0;
            $_SESSION['CliIdOdoo']=0;
            $_SESSION['CliNombre']='UNKNOWN';
            $msg="<div class='alert alert-info p-2 mb-2 text-center' role='alert'><strong>¡Ingreso exitoso!</strong></div>";
        }else{
            $msg="<div class='alert alert-danger p-2 mb-0 text-center' role='alert'><strong>Acceso denegado!. </strong> Usuario o contraseña incorrectos.</div>";
        }
    } catch (PDOException $pe) {
        $msg="<div class='alert alert-danger p-2 mb-0 text-center' role='alert'><strong>Acceso denegado!. </strong> Error al validar sus datos..</div>";
        $stmt = null;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | GPEM SAC.</title>
    <link rel="stylesheet" href="/mycloud/library/gpemsac/portal/css/portal-menu.css">
    <link rel="stylesheet" href="/mycloud/library/gpemsac/css/gpemsac.css">
    <link rel="stylesheet" href="/mycloud/library/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link rel="shortcut icon" href="/mycloud/logos/favicon.ico">

    <style>
        /*
        * Specific styles of signin component
        */
        /*
        * General styles
        */
        body, html {
            /*height: 100%;
            background-repeat: no-repeat;
            background-image: linear-gradient(rgb(104, 145, 162), rgb(12, 97, 33));*/
        }

        .card-container.card {
            max-width: 350px;
            padding: 40px 40px;
        }

        .btn {
            font-weight: 700;
            height: 36px;
            -moz-user-select: none;
            -webkit-user-select: none;
            user-select: none;
            cursor: default;
        }

        /*
        * Card component
        */
        .card {
            background-color: #F7F7F7;
            /* just in case there no content*/
            padding: 20px 25px 30px;
            margin: 0 auto 25px;
            margin-top: 50px;
            /* shadows and rounded borders */
            -moz-border-radius: 2px;
            -webkit-border-radius: 2px;
            border-radius: 2px;
            -moz-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
            -webkit-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
            box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
        }

        .profile-img-card {
            width: 96px;
            height: 96px;
            margin: 0 auto 10px;
            display: block;
            -moz-border-radius: 50%;
            -webkit-border-radius: 50%;
            border-radius: 50%;
        }

        /*
        * Form styles
        */
        .profile-name-card {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin: 10px 0 0;
            min-height: 1em;
        }

        .reauth-email {
            display: block;
            color: #404040;
            line-height: 2;
            margin-bottom: 10px;
            font-size: 14px;
            text-align: center;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
        }

        .form-signin #inputEmail,
        .form-signin #inputPassword {
            direction: ltr;
            height: 44px;
            font-size: 16px;
        }

        .form-signin input[type=email],
        .form-signin input[type=password],
        .form-signin input[type=text],
        .form-signin button {
            width: 100%;
            display: block;
            margin-bottom: 10px;
            z-index: 1;
            position: relative;
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
        }

        .form-signin .form-control:focus {
            border-color: rgb(104, 145, 162);
            outline: 0;
            -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgb(104, 145, 162);
            box-shadow: inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgb(104, 145, 162);
        }

        .btn.btn-signin {
            /*background-color: #4d90fe; */
            background-color: rgb(104, 145, 162);
            /* background-color: linear-gradient(rgb(104, 145, 162), rgb(12, 97, 33));*/
            padding: 0px;
            font-weight: 700;
            font-size: 15px;
            height: 45px;
            -moz-border-radius: 3px;
            -webkit-border-radius: 3px;
            border-radius: 3px;
            border: none;
            -o-transition: all 0.218s;
            -moz-transition: all 0.218s;
            -webkit-transition: all 0.218s;
            transition: all 0.218s;
        }

        .btn.btn-signin:hover,
        .btn.btn-signin:active,
        .btn.btn-signin:focus {
            background-color: rgb(12, 97, 33);
        }

        .forgot-password {
            color: rgb(104, 145, 162);
        }

        .forgot-password:hover,
        .forgot-password:active,
        .forgot-password:focus{
            color: rgb(12, 97, 33);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card card-container">
            <img id="profile-img" src="/mycloud/logos/logo-gpem.png"/>
            <p id="profile-name" class="profile-name-card text-primary">INICIAR SESION</p>
            <script>
                //setTimeout("window.location='thankyou.php';");
                //setTimeout("location.href='AdmProductos.php';", 3000);
            </script>            
            <?php
                echo $msg;
                if($estado){
                    echo '<script>','setTimeout("location.href='."'"."/gesman/Empresas.php';".'"'.',3000);','</script>';
                    echo '
                    <div class="alert alert-success alert-dismissable text-center">
                        <h4>BIENVENIDO</h4>
                        <p class="text-left text-primary m-0">Usuario:</p>
                        <p class="fw-bold">'.$_SESSION['UserNombre'].'</p>
                        <h1><i class="fas fa-circle-notch fa-spin text-success mb-0"></i></h1>
                        <p class="font-italic text-success mb-0">Iniciando ...</p>
                    </div>';                   
                }else{
                    echo '
                    <form class="form-signin" method="post">
                        <span id="reauth-email" class="reauth-email"></span>
                        <input type="text" id="inputEmail" name="usu" class="form-control" placeholder="Usuario" required autofocus>
                        <input type="password" id="inputPassword" name="psw" class="form-control" placeholder="Contraseña" required>
                        <div id="remember" class="checkbox"><label><input type="checkbox" value="remember-me"> Remember me</label></div>
                        <button class="btn btn-lg btn-primary btn-block btn-signin" type="submit">Ingresar</button>
                    </form>
                    <a href="#" class="forgot-password">Forgot the password?</a>';
                }
            ?>
        </div>
    </div>
</body>
</html>