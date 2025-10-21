<?php
include_once  'utilities.php';
include_once  'database.php';

include_once 'classes/loginmanager.php';

$loginManager = new LoginManager();

// Cerrar sesión.
$loginManager->logout();

// Redirige a la página de inicio de sesión después de cerrar sesión.
header('Location: login.php');
exit;
?>
