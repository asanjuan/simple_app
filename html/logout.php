<?php
include_once  'utilities.php';
include_once  'database.php';

include_once 'classes/loginmanager.php';

$loginManager = new LoginManager();

// Cerrar sesi�n.
$loginManager->logout();

// Redirige a la p�gina de inicio de sesi�n despu�s de cerrar sesi�n.
header('Location: login.php');
exit;
?>
