<?php 

class SecurityManager {

	private static $is_admin = null;

	public static function GetEntityPermission($entity_id,$user_id){
		$q = "select pe.id_entity, max(read_access) as read_access, max(write_access) as write_access, max(insert_access) as insert_access, max(delete_access) as delete_access 
				from 
				app_roles_usuarios ru 
				left join app_roles r on ru.id_rol = r.id
				left join app_permisos_entidad pe on r.id = pe.id_rol
				where 
				ru.id_usuario = '$user_id'
				and pe.id_entity = '$entity_id'
				group by pe.id_entity";
		$data = query1($q);
		
		return $data;
		
	}
	
	public static function GetMenuReadEntityes($module_id,$user_id){
		$q = "select e.* 
				from 
				app_roles_usuarios ru 
				left join app_roles r on ru.id_rol = r.id
				left join app_permisos_entidad pe on r.id = pe.id_rol
				left join app_entities e on pe.id_entity = e.id
				where 
				ru.id_usuario = '$user_id'
				and e.id_module = '$module_id'
				and pe.read_access = 1
				group by pe.id_entity";
		$data = query1($q);
		
		return $data;
		
	}
	
	
	public static function UserIsAdmin($user_id){
		//singleton
		if (self::$is_admin == null){
			
		
			$q = "select max(is_admin) is_admin
				from app_roles_usuarios ru 
				left join app_roles r on ru.id_rol = r.id
				where ru.id_usuario = '$user_id'";
			$data = query1($q);
			
			if ($data !=null && boolval($data['is_admin'])){
				self::$is_admin = true;
			}		
			else {
				self::$is_admin = false;
			} 
			
		}
		return self::$is_admin;
	}
	
	
}

