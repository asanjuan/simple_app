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


//obtenemos el controlador por defecto para cargar la página principal
$default_controller = get_config('DEFAULT_CONTROLLER');	
if (empty($_GET)) {
	$_GET['controller'] = $default_controller; //hack para que funcione el menu
}


//es necesario estar en el ámbito de una empresa
if (isset($_GET['company'])){ //el parámetro sirve para cambiar de empresa
	if ($_GET['company'] == "0") {
		unset($_SESSION['company']);
	}else {
		$_SESSION['company'] = $_GET['company'];
	}
}
/*
//obtenemos la empresa por defecto para cargar la página principal. 
// Si el usuario solo tiene acceso a una empresa, se seleccionará automáticamente
if (!isset($_SESSION['company']) ){
	$empresas = SecurityManager::getUserCompanies($_SESSION['userid']);
	if (count($empresas)==1){
		$_SESSION['company'] = $empresas[0]['id'];
	}
}
*/

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

function print_company_selector(){
	
	
	$opt_list = SecurityManager::getUserCompanies($_SESSION['userid']);

	$html =  "<select id='company_selector' > ";
	$html .= '<option value="0" > -- </option>';
	foreach ($opt_list as $opt) {
		$selected = "";
		if ($opt["id"] == $_SESSION['company']) $selected = "selected";
		$html .= '<option value="' . $opt["id"] . '" ' . $selected . '>' . $opt["empresa"] . '</option>';
	}
	$html .= "</select>";
	
	return $html;
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
	$default_controller = get_config('DEFAULT_CONTROLLER');	

	if (!empty($_GET)) {
		// Obtener el valor del par�metro "nombre"
		$controller = isset($_GET['controller']) ? $_GET['controller'] : $default_controller;
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
	$default_controller = get_config('DEFAULT_CONTROLLER');	

	$controller = $default_controller;

	if (!empty($_GET)) {
		// Obtener el valor del par�metro "nombre"
		$controller = isset($_GET['controller']) ? $_GET['controller'] : $default_controller;
	}

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

function print_main_form(){
	
	ob_start();	
	$default_controller = get_config('DEFAULT_CONTROLLER');	

	$controller = $default_controller;

	if (!empty($_GET) && isset($_GET['controller'])) {
		// Obtener el valor del par�metro "nombre"
		$controller = isset($_GET['controller']) ? $_GET['controller'] : $default_controller;
	}else{
		$_GET['controller'] = $default_controller; //hack para que funcione el menu
	}

	
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
	
	
	return ob_get_clean();		
}

//include ('templates/main_tailwind.php');
include ('templates/main.php');
