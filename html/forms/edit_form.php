<?php
require_once 'classes/controller.php';
require_once 'classes/security_manager.php';
require_once 'classes/plugin_manager.php';

login_test();

$entidad = $_GET['controller'];
$item = $_GET['item'];
$entity_id = EntityManager::GetEntityId($entidad);

$metadata = EntityManager::GetEntity($entidad);
$estructura = EntityManager::GetEstructura($entidad);
		
push_calling_url();

//global $mainForm;
$mainForm = new Controller();
$mainForm->Setup(
	$metadata['plural_name'],
	$metadata['singular_name'],
	$entidad,
	$entidad,
	"id",
	'',
	$estructura,
	$metadata['tipo_entidad']
	);
			
PluginManager::RegisterForm($mainForm);

//añadimos los plugins registrados
$plugin_files = EntityManager::GetPluginFiles($entidad);
//dump($plugin_files);
foreach ($plugin_files as $fichero){

	//if ($fichero['tipo']==0){
		include_once $fichero['fichero'];
	//}
	/*
	else{
		eval ($fichero['code']); //lo eliminamos por peligroso.
	}
	*/	
}


//obtenemos los botones de la toolbar
$type = $metadata['tipo_entidad'];
if ($item != '' || $type == 'action'){
	 $botones = EntityManager::GetFormButtons($entidad);
	 
	 foreach ($botones as $btn){
		$mainForm->addCustomButton($btn['code'], $btn['nombre'], $btn['type'],$btn['icon']);
	} 
}

//le decimos los informes que tiene la entidad 
$reports = EntityManager::GetReports($entity_id);

$mainForm->setReports($reports);

//verificamos la seguridad de acceso
$is_admin = SecurityManager::UserIsAdmin($_SESSION['userid']);
$allowed = $is_admin;

if (!$is_admin){
	$perms = SecurityManager::GetEntityPermission($entity_id,$_SESSION['userid']);
	if ($perms!=null){
		
		$mainForm->setPermissions(boolval($perms['insert_access']), boolval($perms['write_access']), boolval($perms['delete_access'])); // insert, update, delete
		$allowed = boolval($perms['read_access']);
		
	}else {
		$mainForm->setPermissions(false, false, false); // insert, update, delete
	}
}

//obtenemos el proceso de negocio asociado a la entidad
$process = EntityManager::GetEntityProcess($entity_id);
$mainForm->setProcess($process);

if ($allowed){
	$mainForm->Run();
}else{
	include('templates/denied.php');
}




