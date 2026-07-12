<?php
include 'utilities.php';
include 'database.php';
include_once 'config.php';

include 'classes/loginmanager.php';


$msg = "";
$username = "";
$password = "";

$loginManager = new LoginManager();


$r = "";
if (isset($_GET['r'])) $r = $_GET['r']; //hay un redirect

// Verificar si se envi� el formulario de inicio de sesi�n.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($loginManager->login($username, $password)) {
        // Si el inicio de sesi�n es exitoso, redirige a la p�gina de inicio del usuario.
        if ($r != "") {
            //trace( "debe redirigir a " . base64_decode($r));
            header('Location: ' . base64_decode($r));
        } else {
            header('Location: index.php');
        }
        //exit;
    } else {
        $msg = "Usuario o contraseña incorrectos";
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inicio de Sesi&oacute;n - <?php echo get_config('APP_NAME'); ?></title>
    <link rel="stylesheet" href="templates/css/colors.php" />
    <link rel="stylesheet" href="templates/css/style.css" />
<style>
.app_logo_nube {
  width: auto;
  height: 100px;
  color: var(--color-cabecera);
}  
</style>
</head>

<body>
    <?php print_debug_request(); ?>
    <div class="login-main" >
        <div class="login-container" >
           
            <div style="text-align:center">
                <div style="text-align:center;">
                    <?php include 'templates/img/app_logo.svg'; ?>
                </div>
                <h1 ><?php echo get_config('APP_NAME'); ?></h1>
            </div>
            <div class="login-box">
                <h3>Iniciar Sesi&oacute;n</h3>
                <form action="" method="POST">
                    <div class="input-group">
                        <!--label for="username">Nombre de Usuario:</label-->
                        <input type="text" id="username" name="username"  placeholder="Usuario" value="<?php echo $username; ?>" autocomplete="off" required>
                    </div>
                    <div class="input-group">
                        <!--label for="password">Contrase&ntilde;a:</label-->
                        <input type="password" id="password" name="password" placeholder="Password" value="<?php echo $password; ?>" autocomplete="off" required>
                    </div>
                    <div class="input-group">
                        <button type="submit" class="boton-enlace boton-login">Iniciar Sesi&oacute;n</button>
                    </div>
                    <?php

                    if ($msg != "") {

                        echo '<div class="login-error"> ' . $msg . '</div>';
                    }
                    ?>
                </form>
            </div>
        </div>
    </div>
</body>

</html>