<?php 

class EntityManager {
	
	public static $last_entity = [];
	
	
	public static function GetModulesList(){
		$data = query("SELECT * FROM app_modules order by orden");
		return $data;
		
	}
	public static function GetModuleById($id){
		$id = quote($id);
		$data = query1("SELECT * FROM app_modules where id = $id");
		return $data;
		
	}
	
	public static function GetMenuData($user_id){
		$menu_elements = array();
		
		$item_actual = "";
		$modulo_actual = "";
		if (isset($_GET['controller'])){
			$item_actual = $_GET['controller'];
			$metadata = EntityManager::GetEntity($item_actual);
			$modulo_actual = $metadata['id_module'];
		}
		
		$modules = EntityManager::GetModulesList();
		
		foreach ($modules as $module){
			$module_menu = array();
			$module_menu["option"] = $module['module'];
			if ($module['id']==$modulo_actual){
				$module_menu["expand"] = true;
			}else {
				$module_menu["expand"] = false;
			}
			
			
			$entities = EntityManager::GetMenuReadEntityes($module['id'],$user_id);
			$items = array();
			
			foreach ($entities as $entity){
				if ($entity['mostrar_menu']==1){
					$item = array();
					$item['option'] =  $entity['plural_name'];
					$item['url'] =  get_URL_BASE()."?controller=".$entity['entity'];
					$item['icon'] =  $entity['icon'];
					$item['current'] = false;
					if ($entity['entity']==$item_actual){
						$item['current'] = true;
					}
					$items[] = $item;
				}
			}
			if (count($items)>0){
				$module_menu["items"] = $items;
				$menu_elements[] = $module_menu;
			}
			
		}
		$menu_elements [] = ["option" => htmlspecialchars("Cerrar sesión", ENT_QUOTES)." (".$_SESSION['username'].")", "url" => get_URL_BASE()."logout.php"];
		
		return $menu_elements;
	
	}
	
	
	
	
	public static function GetMenuDataAdmin(){
		$menu_elements = array();
		
		$item_actual = "";
		$modulo_actual = "";
		if (isset($_GET['controller'])){
			$item_actual = $_GET['controller'];
			$metadata = EntityManager::GetEntity($item_actual);
			$modulo_actual = $metadata['id_module'];
		}
		
		$modules = EntityManager::GetModulesList();
		
		foreach ($modules as $module){
			$module_menu = array();
			$module_menu["option"] = $module['module'];
			if ($module['id']==$modulo_actual){
				$module_menu["expand"] = true;
			}else {
				$module_menu["expand"] = false;
			}
			
			
			$entities = EntityManager::GetEntityList($module['id']);
			$items = array();
			
			foreach ($entities as $entity){
				if ($entity['mostrar_menu']==1){
					$item = array();
					$item['option'] =  $entity['plural_name'];
					$item['url'] =  get_URL_BASE()."?controller=".$entity['entity'];
					$item['icon'] =  $entity['icon'];
					$item['current'] = false;
					if ($entity['entity']==$item_actual){
						$item['current'] = true;
					}
					$items[] = $item;
				}
			}
			$module_menu["items"] = $items;
			$menu_elements[] = $module_menu;
		}
		$menu_elements [] = ["option" => htmlspecialchars("Cerrar sesión", ENT_QUOTES)." (".$_SESSION['username'].")", "url" => get_URL_BASE()."logout.php"];
		//dump($menu_elements);
		return $menu_elements;
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
				group by pe.id_entity
				order by e.orden";
		$data = query($q);
		
		return $data;
		
	}
	
	
	public static function GetEntityList($module){
		$module = quote($module);
		$data = query("SELECT * FROM app_entities e where e.id_module =$module order by orden, id");
		return $data;
		
	}
	
	
	
	public static function GetEntity($entidad) {
		if (isset(self::$last_entity[$entidad])) {
			return self::$last_entity[$entidad];
		}
		
		$data = query1("SELECT * FROM app_entities e WHERE e.entity = " . quote($entidad));
		self::$last_entity[$entidad] = $data; 
		return $data;
	}
	
	public static function GetEntityId($entidad){
		$data = EntityManager::GetEntity($entidad);
		return $data['id'];
		
	}
	public static function GetEntityById($id){
		if (isset(self::$last_entity[$id])) {
			return self::$last_entity[$id];
		}
		
		$id = quote($id);
		$data = query1("SELECT * FROM app_entities e where e.id =".$id);
		self::$last_entity[$id] = $data; 
		return $data;
		
	}
	
	
	public static function GetEstructura($entidad){
		$data = query("SELECT col.* FROM app_entity_columns col inner join app_entities e on col.id_entity = e.id and e.entity =".quote($entidad). " order by orden, id");
		for ($i = 0; $i < count($data); $i++) {
			
			if (!empty($data[$i]["dominio"])){
			$data[$i]["options"]  = query("select codigo as value, descripcion as description 
							from app_optionsets op
							inner join app_dominios d on op.id_dominio = d.id
							where d.nombre = '".$data[$i]["dominio"]."' 
							order by descripcion");
			}
						
		}
		return $data;
		
	}
	
	public static function GetVistasLookup($entity_id){
		$data = query("Select id, name from app_views where tipo = 'lookup' and id_entity =".quote($entity_id));
		
		return $data;
		
	}


	public static function GetVistas($entity_id){
		$data = query("Select id, name from app_views where tipo <> 'lookup' and id_entity =".quote($entity_id));
		
		if (count($data)==0){
			//NO HAY VISTAS, CREAREMOS UNA SOBRE LA MARCHA CON UN SELECT * (HAREMOS ESTO TOLERANTE A ERRORES)
			//$columnas = EntityManager::GetEstructura($entidad);
			$entidad = EntityManager::GetEntityById($entity_id);
			
			$r = array();
			$r['name'] = $entidad['plural_name'];
			//$r['controller'] = $entidad;
			$r['query'] = "select id, ".$entidad['campo_principal']." from ".$entidad['entity']; //PONEMOS SOLO EL CAMPO PRINCIPAL
			$r['order_by'] = $entidad['campo_principal'];
			$r['id_entity'] = $entity_id;
			$r['tipo'] = "public";
			$r['search_fields'] = $entidad['campo_principal'];
			
			dbinsert("app_views",$r);
			$data = query("Select id, name from app_views where id_entity =".quote($entity_id));
		}
		
		return $data;
		
	}
	
	public static function GetVista($view_id){
		$view_id = quote($view_id);
		$data = query1("Select * from app_views where id =$view_id");
		return $data;
		
	}

	public static function GetGraphic($view_id){
		$view_id = quote($view_id);
		$data = query1("Select * from app_graphics where id =$view_id");
		return $data;
		
	}
	
	public static function GetPluginFiles($entidad){
		$sql = "SELECT m.folder, p.filename, p.code FROM app_plugins p
				inner join app_entities e on p.id_entity = e.id
				inner join app_modules m on e.id_module = m.id
				where p.estado = 1 and e.entity = ".quote($entidad);
		$data = query($sql);
		//return $data;
		
		$includes = array();
		foreach ($data as $plugin){
			
			$x = array();
			$x['fichero'] = "modules/".$plugin["folder"]."/plugins/".$plugin["filename"];
			$x['code'] =$plugin["code"];
			$includes[] = $x;
		}
		
		return $includes;
		
		
	}
	
	public static function GetScriptFiles($entidad){
		$sql = "SELECT m.folder, p.filename, p.type
				FROM app_scripts p
				inner join app_entities e on p.id_entity = e.id
				inner join app_modules m on e.id_module = m.id
				where p.estado = 1 and e.entity = ".quote($entidad);
		$data = query($sql);
		//return $data;
		
		$includes = array();
		foreach ($data as $plugin){
			$element = array();
			$element['path'] = "modules/".$plugin["folder"]."/scripts/".$plugin["filename"];
			$element['type'] = $plugin['type'];
			
			$includes[] = $element;

		}
		
		return $includes;
		
		
	}

	public static function getDBtype($type, $max){
		$val = $type;
		switch ($type){
			case "text": $val = "varchar($max)"; break;
			case "textarea": $val = "text"; break;
			case "password": $val = "varchar($max)"; break;
			case "guid": $val = "char(32)"; break;
			case "file": $val = "varchar(255)"; break;
			case "decimal": $val = "decimal(18,4)"; break;
			case "status": $val = "int"; break;
		}
		return $val;
	}
	
	public static function GetFormButtons($entity){
		$data = query("SELECT btn.* FROM app_buttons btn inner join app_entities e on btn.id_entity = e.id and e.entity =".quote($entity). " order by orden, id");

		return $data;
		
	}

	public static function GetReports($id_entity){
		$data = query("SELECT * FROM app_report_templates btn where id_entity=".quote($id_entity));

		return $data;
		
	}

	public static function GetEntityProcess($id_entity){

		$data = query("select id, nombre, id_entity, campo_entidad from app_proceso_entidad where id_entity = ".quote($id_entity)." and status=1");
		return $data;
		
	}
	public static function GetProcess($id){

		$data = query1("select p.id, nombre, id_entity, e.entity, campo_entidad from app_proceso_entidad p
				inner join app_entities e on p.id_entity = e.id
				where p.id = ".quote($id) );
		return $data;
		
	}


	public static function GetProcessRun($id_proceso , $item){

		$data = query1("select id, id_proceso, id_fase, id_registro from app_proceso_tramites where id_proceso = ".quote($id_proceso) .  " and id_registro = ".quote($item) );
		return $data;
		
	}
	public static function GetEntityProcessStages($id_proceso ){
	
		$fases = query("select id, nombre,valor, orden, tipo_fase from app_fases_proceso where id_proceso = ".quote($id_proceso)." order by orden asc");
		return $fases;
		
	}
	
}
//utilidad para saber si el registro tiene una columna de estado de bloqueo (status) activo o inactivo
function get_status_field($estructura){
	$fld = null;
	
	foreach($estructura  as $campo){
		if ( $campo["type"] == "status" ){
			$fld = $campo;
			break;
		}
	}
	return $fld;
}