<?php

include_once 'config.php';
include_once 'utilities.php';
include_once 'database.php';
require_once 'classes/loginmanager.php';
include_once 'classes/entity_manager.php';
include_once 'classes/form_manager.php';
include_once 'classes/security_manager.php';
include_once 'classes/report_manager.php';

login_test();
check_URL_BASE();
		
function print_menu(){
	ob_start();
	
	$is_admin = SecurityManager::UserIsAdmin($_SESSION['userid']);

	if ($is_admin){
		$menu_elements = EntityManager::GetMenuDataAdmin();
	}else{
		$menu_elements = EntityManager::GetMenuData($_SESSION['userid']);
	}
	
	//dump($menu_elements);
	//include 'templates/menu_tailwind.php';
	include 'templates/menu.php';
	
	return ob_get_clean();
  
} 


function show_user(){

	$user_data = query1("select * from usuarios where id=".quote($_SESSION['userid']));

	$container_start = '<div class="flex-rows" style="gap:10px">';
	$container_end = '</div>';
	$html_foto = "";
	if (!empty($user_data['foto'])){
		$html_foto = '<div style="height:40px;width:40px;border:solid 1px white;border-radius:50%;overflow:hidden;display:inline-block;">';
		$html_foto .= '<img  style="height:40px;width:40px;object-fit:cover;" src="'.$user_data['foto'].'" > </image>';
		$html_foto .= '</div>';

	}
	$html_nombre = '<div class="flex-columns" >';
	$html_nombre .= '<div><span>'.$user_data['nombre'].' '. $user_data['apellidos'] .'</span></div>';
	$html_nombre .= '<div><a href="logout.php"> Cerrar sesión ('.$user_data['login'] .') </a></div>';
	$html_nombre .= '</div>';


	echo $container_start. $html_foto . $html_nombre . $container_end;
}

function print_app_name(){
	
	return get_config('APP_NAME');
	
}

function print_title_name(){
	
	$app_name = get_config('APP_NAME');
	if (!empty($_GET)) {
		// Obtener el valor del par�metro "nombre"
		$controller = isset($_GET['controller']) ? $_GET['controller'] : '';
		$metadata = EntityManager::GetEntity($controller);
		
		$item = isset($_GET['item']) ? $_GET['item'] : '';
		if ($item != ""){
			return $metadata["singular_name"];
		}else {
			return $metadata["plural_name"];
		}

	}else {
		return $app_name;
	}
	
}

function add_form_scripts(){
	
	
	if (!empty($_GET)) {
		// Obtener el valor del par�metro "nombre"
		$controller = isset($_GET['controller']) ? $_GET['controller'] : '';
		$item = isset($_GET['item']) ? $_GET['item'] : '';
		$new = isset($_GET['new']) ? 'new' : '';
		
		$listado = true;
		if ($item != "" || $new != ""){
			$listado = false;
		}
		
		$metadata = EntityManager::GetEntity($controller);
		$files = EntityManager::GetScriptFiles($controller);
		
		foreach ($files as $file){
			if ($listado == true && $file['type']=="list" 
			|| $listado == false && $file['type']=="form"){
				echo '<script src="'.$file['path'].'"></script>';
			}
			
		}
			
		
	}
	
	
	
}

function print_main_form(){
	
	ob_start();	
	
	if (!empty($_GET)) {
		// Obtener el valor del par�metro "nombre"
		$controller = isset($_GET['controller']) ? $_GET['controller'] : '';
		$metadata = EntityManager::GetEntity($controller);
		
		if ($metadata){
			$tipo = $metadata['tipo_entidad'];
		
			if ($tipo == "panel"){
				$_GET['new']= '00000000'; //hack
				include 'forms/edit_form.php';
			}else if ($tipo == "action"){
				$_GET['new']= '00000000'; //hack
				include 'forms/edit_form.php';
			}else if (isset($_GET['item']) || isset($_GET['new'])){
				
				include 'forms/edit_form.php';
				
				
			}else {
				include 'forms/list_form.php';
			}
		}else {
			include 'templates/denied.php';
		}
		
			
		
	}
	
	return ob_get_clean();		
}

//include ('templates/main_tailwind.php');
include ('templates/main.php');
