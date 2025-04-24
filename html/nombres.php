<?php
include_once './api/rest_api.php';



class servicio_nombres extends RestApi {
	
	
	/*
	RECIBE UNA LISTA DE NOMBRES Y LA DESCOMPONE EN NOMBRE, APELLIDO 1 Y 2 en formato csv para pegar en un excel
	[
	{"nombre" : "Juan José de la Mata y Hermosa"}
	]
	devuelve:
	Juan José;De La Mata;Y Hermosa
	*/
	protected function  ExecutePost($data){
		
		foreach ($data as $item) {
			$r = descomponer_nombre($item["nombre"]);
			echo implode(";",$r). "\n";
			
		}
		
	}
	
}

$obj = new servicio_nombres();
$obj->Run();




/*********************************************************
PARSER DESCOMPOSICIÓN DE NOMBRES COMPLETOS
**********************************************************/

function descomponer_nombre($full_name) {

  /* separar el nombre completo en espacios */
  $tokens = explode(' ', trim($full_name));
  /* arreglo donde se guardan las "palabras" del nombre */
  $names = array();
  /* palabras de apellidos (y nombres) compuetos */
  $special_tokens = array('da', 'de', 'del', 'la', 'las', 'los', 'mac', 'mc', 'van', 'von', 'y', 'i', 'san', 'santa','m.','m.ª', 'mª','d.','dª');
  $sufix_tokens = array('m.','m.ª', 'mª');
  $prev = "";
  $sufix = "";
  
  $sum_element = 0;
  $name = "";
  
  foreach($tokens as $token) {
      $_token = strtolower($token);
	  
	  if(count($names) >0 && in_array($_token, $sufix_tokens)){
		  
		  $names[] = $token;
	  }
      else if(in_array($_token, $special_tokens)) {

          $prev .= "$token ";
		  
	  } else {
		  
          $name = $token;
		  $names[] = $prev. $token;
		  $prev = "";
		  
      }

  
  }
  
  //echo json_encode($names);
  
  $num_nombres = count($names);
  //echo $num_nombres;
  
  $nombres = $apellido1 = $apellido2 = "";
  
  switch ($num_nombres) {
      case 0:
          $nombres = '';
          break;
      case 1: 
          $nombres = $names[0];
          break;
      case 2:
          $nombres    = $names[0];
          $apellido1  = $names[1];
          break;
      case 3:
	      $nombres   = $names[0];
          $apellido1 = $names[1];
		  $apellido2 = $names[2];

		  break;
      default:

		  $apellido2 = array_pop($names);
		  $apellido1 = array_pop($names);

          $nombres = implode(' ', $names);
          break;
  }
  
  $nombres    = mb_convert_case($nombres, MB_CASE_TITLE, 'UTF-8');
  $apellido1  = mb_convert_case($apellido1, MB_CASE_TITLE, 'UTF-8');
  $apellido2  = mb_convert_case($apellido2, MB_CASE_TITLE, 'UTF-8');
  
  $resultado["nombre"] = $nombres;
  $resultado["apellido1"] = $apellido1;
  $resultado["apellido2"] = $apellido2;
  //echo json_encode($resultado);
  return $resultado;
  
 }