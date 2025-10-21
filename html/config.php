<?php
//modo de depuracion de errores
//error_reporting(E_WARNING | E_ERROR);
try {
    session_start();
} catch (Exception $e) {
    error_log("Error al iniciar sesión: " . $e->getMessage());
}

error_reporting(E_ERROR || E_WARNING);
ini_set('display_errors', 'On');

function exception_error_handler($errno, $errstr, $errfile, $errline ) {
	if (!(error_reporting() & $errno)) {
        return false; // Permite que PHP maneje el error normalmente
    }
	// Ignorar solo los NOTICE y WARNING si no quieres que lancen excepciones
    if ($errno == E_NOTICE || $errno == E_WARNING) {
		echo ($errstr);
        return false;
    }
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

set_error_handler("exception_error_handler");

define( '__DEBUGSQL__', false);
define( '__DEBUGREQUEST__', false);

function print_debug_request(){
	if ( __DEBUGREQUEST__ ) {
		trace("GET");
		dump($_GET);
		trace("POST");
		dump($_POST);
	}	
}

function dump($var){
	echo "<pre>";
	var_dump($var);
	echo "</pre>";
}