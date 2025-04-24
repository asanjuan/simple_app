<?php 

class EntityManager {
	
	
	
	
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
					
					$items[] = $item;
				}
			}
			$module_menu["items"] = $items;
			$menu_elements[] = $module_menu;
		}
		$menu_elements [] = ["option" => htmlspecialchars("Cerrar sesión", ENT_QUOTES)." (".$_SESSION['username'].")", "url" => get_URL_BASE()."logout.php"];
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
				group by pe.id_entity";
		$data = query($q);
		
		return $data;
		
	}
	
	
	public static function GetEntityList($module){
		$module = quote($module);
		$data = query("SELECT * FROM app_entities e where e.id_module =$module order by id");
		return $data;
		
	}
	
	
	
	public static function GetEntity($entidad){
		$data = query1("SELECT * FROM app_entities e where e.entity =".quote($entidad));
		return $data;
		
	}
	
	public static function GetEntityId($entidad){
		$data = EntityManager::GetEntity($entidad);
		return $data['id'];
		
	}
	public static function GetEntityById($id){
		$id = quote($id);
		$data = query1("SELECT * FROM app_entities e where e.id =".$id);
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
		/*
		if (count($data)==0){
			//NO HAY VISTAS, CREAREMOS UNA SOBRE LA MARCHA CON UN SELECT * (HAREMOS ESTO TOLERANTE A ERRORES)
			$columnas = EntityManager::GetEstructura($entidad);
			$r = array();
			$r['name'] = "Default";
			//$r['controller'] = $entidad;
			$r['query'] = "select * from $entidad";
			$r['order_by'] = "1";
			$key = " ";
			$text = " ";
			foreach ($columnas as $col){
				
				if ($col["type"]=="text" && $text ==" ") $text = $col["dbcolumn"];
			}
			$r['search_fields'] = $text;
			
			dbinsert("app_views",$r);
			$data = query("Select id, name from app_views where id_entity =".quote($entity_id));
		}
		*/
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
	
}