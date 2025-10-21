<?php 

login_test();


clear_calling_stack();
//push_calling_url();

$entidad = $_GET['controller'];
$metadata = EntityManager::GetEntity($entidad);
$vistas = EntityManager::GetVistas($metadata['id']);
$primera_vista = $vistas[0]["id"];
$metadata = EntityManager::GetEntity($entidad);
$grid_enabled = 1;


$is_admin = SecurityManager::UserIsAdmin($_SESSION['userid']);
$allowed = $is_admin;

if (!$is_admin){
	$perms = SecurityManager::GetEntityPermission($metadata['id'],$_SESSION['userid']);
	if ($perms!=null){
		$allowed = boolval($perms['read_access']);
	}	
	
}

if ($allowed){
	include('templates/list_form.php');
}else{
	include('templates/denied.php');
}
