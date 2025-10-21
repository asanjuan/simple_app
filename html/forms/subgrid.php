<?php 


require_once '../api/rest_api.php';
include_once '../config.php';
include_once '../utilities.php';
include_once '../database.php';
include_once '../classes/entity_manager.php';
include_once '../classes/loginmanager.php';
include_once '../classes/utility_traits.php';
include_once '../classes/plugin_manager.php';
include_once '../classes/form_manager.php';
include_once '../classes/security_manager.php';


function get_grid_last_filter($entity){
	if (isset($_SESSION[$entity.'_last_filter'])){
		return $_SESSION[$entity.'_last_filter'];
	}else{
		return '';
	}
}

function set_grid_last_filter($entity, $filter){
	$_SESSION[$entity.'_last_filter'] = $filter;
}

class Form_Subgrid extends RestApi {
	
	use Messages;
	use Plugin_list;
	
	protected $buttons;
	
	
	protected $this_controller = "";
	protected $key_field = "id";
	protected $view_list;
	protected $view_id_selected;

	protected $access_delete = true;
	protected $access_insert = true;
	protected $access_read = true;
	protected $access_update = true;
	protected $has_quick_create_form = false;
	protected $url_parent_related = "";

	protected function ExecutePost($data){
		
				
		$op = strtolower($data["operation"]);
		if (isset($data["op_data"])){
			//solo afecta al insert, porque pueden mezclarse variables del insert y el select
			$x = json_decode($data["op_data"],true); 
			$op = $x["operation"];
		}
		
				
		switch ($op){
			case "select" : $this->execute_select($data); break;
			case "delete" : $this->execute_delete($data); break;
			case "duplicate" : $this->execute_duplicate($data); break;
			case "render" : $this->execute_render($data); break;
			case "insert" : $this->execute_insert($data); break;
			case "export" : $this->execute_export($data); break;
			case "up" : $this->execute_up($data); break;
			case "down" : $this->execute_down($data); break;

			default: $this->execute_custom($data); break; 
			//$this->return_error(400, "$op not implemented"); break;
			
		}
		
	}
	
	
	public function loadPlugins($entidad){
		PluginManager::RegisterForm($this);
		$base_dir = dirname(__DIR__, 1);
		//añadimos los plugins registrados
		$plugin_files = EntityManager::GetPluginFiles($entidad);
		foreach ($plugin_files as $fichero){
			
			//if ($fichero['tipo']==0){
				include $base_dir."/".$fichero['fichero'];
			//}else{
			//	eval ($fichero['code']);
			//}
		}	
	}
	
	public function loadPermissions($entity_id , $enabled){
		
		$is_admin = SecurityManager::UserIsAdmin($_SESSION['userid']);
		
		if (!$is_admin){
			$perms = SecurityManager::GetEntityPermission($entity_id,$_SESSION['userid']);
			if ($perms!=null){
				$this->access_delete = boolval($perms['delete_access']);
				$this->access_insert = boolval($perms['insert_access']);
				$this->access_read = boolval($perms['read_access']);
				$this->access_update = boolval($perms['write_access']);
				
				
			}else {
				$this->access_delete = false;
				$this->access_insert = false;
				$this->access_read = false;
				$this->access_update = false;
			}
		}else {
			$this->access_delete = true;
			$this->access_insert = true;
			$this->access_read = true;
			$this->access_update = true;
		}

		// independientemente de lo anterior, si está bloqueado por lógica, se bloquea
		if ($enabled == 0){
			$this->access_delete = false;
			$this->access_insert = false;
			$this->access_update = false;
		}

	}
	
	
	public function execute_up($data){
		
		$elements = $data["list"];
		
		$view_id = $data["view"];

		$obj = EntityManager::GetVista($view_id);
		$metadata = EntityManager::GetEntityById($obj['id_entity']);
		$table = $metadata['entity'];
		$up_down_field = $obj['up_down_field'];
		
		$key_field = "id";
		
		
		foreach ($elements as $item){
			 query("update $table set $up_down_field = ifnull($up_down_field -1,0) where id = ".quote($item));	
		}
		
		//después de borrar, refrescamos grid
		$this->execute_select($data);
	}
	
	
	public function execute_down($data){
		$elements = $data["list"];
		
		$view_id = $data["view"];

		$obj = EntityManager::GetVista($view_id);
		$metadata = EntityManager::GetEntityById($obj['id_entity']);
		$table = $metadata['entity'];
		$up_down_field = $obj['up_down_field'];
		
		$key_field = "id";
		
		
		foreach ($elements as $item){
			 query("update $table set $up_down_field = ifnull($up_down_field +1,0) where id = ".quote($item));	
		}
		
		//después de borrar, refrescamos grid
		$this->execute_select($data);
	
	}
	
	public function execute_delete($data){
		$elements = $data["list"];
		
		$view_id = $data["view"];

		$obj = EntityManager::GetVista($view_id);
		$metadata = EntityManager::GetEntityById($obj['id_entity']);
		$table = $metadata['entity'];
		$this->loadPlugins($table);

		$key_field = "id";
		
		foreach ($elements as $item){
			$x = array();
			try{
				$this->preDelete($item);
				$x[$key_field] = $item;
				dbdelete($table,$x);
				$this->postDelete($item);
			}catch(Exception $ex){
				$this->showMessage( $ex->getMessage());
			}
			
		}

		//después de borrar, refrescamos grid
		$this->execute_select($data);
		
		
	}

	public function execute_custom($data){
		
		$elements = $data["list"];
		$operation = $data["operation"];
		$view_id = $data["view"];

		$obj = EntityManager::GetVista($view_id);
		$metadata = EntityManager::GetEntityById($obj['id_entity']);
		$table = $metadata['entity'];
		$this->loadPlugins($table);
		//dump($elements);
		
		try{
			$this->onCustomButton($operation,'', $elements);
		}catch(Exception $ex){
			$this->showMessage( $ex->getMessage());
		}
		//después de borrar, refrescamos grid
		$this->execute_select($data);
		
		
	}

	public function execute_duplicate($data){
		$elements = $data["list"];
		
		$view_id = $data["view"];
		//trace($view_id);
		$obj = EntityManager::GetVista($view_id);
		$metadata = EntityManager::GetEntityById($obj['id_entity']);
		$table = $metadata['entity'];
		//dump($data);
		$this->loadPlugins($table);
		
		foreach ($elements as $item){
			try{
				$x = dbgetbyid($table,$item);
			
				$this->preDuplicate($item, $x);

				$new_id = dbinsert($table,$x);
				$x['id'] = $new_id;
				
				$this->postInsert($new_id, $x);
				$this->postDuplicate($item, $new_id);
			}catch(Exception $ex){
				$this->showMessage( $ex->getMessage());
			}
		}

		//después de borrar, refrescamos grid
		$this->execute_select($data);
	}

	public function execute_select($data){
		
		if (isset($data["field"]) && $data["field"] != ""){
			$filter = $data["field"] ."=" . quote($data["value"]);
			$this->url_parent_related = "&parent_column=".$data["field"]."&parent_value=".$data["value"];
		}
		
		$page = 1;
		if (isset($data["page"]) && is_numeric( $data["page"])){
			$page = $data["page"];
		}
		$items_per_page = get_config('VIEW_PAGE_SIZE');
		if (isset($data["pagesize"]) && is_numeric( $data["pagesize"])){
			$items_per_page = $data["pagesize"];
		}
		$view_id = $data["view"];
		$this->view_id_selected = $view_id;

		$search_text = $data["search_text"];
				
		$obj = EntityManager::GetVista($view_id);
		
		$metadata = EntityManager::GetEntityById($obj['id_entity']);
		$this->view_list = EntityManager::GetVistas($obj['id_entity']);
		$this->has_quick_create_form = FormManager::HasQuickForm($obj['id_entity']);
		$this->buttons = EntityManager::GetFormButtons($metadata['entity']);
		//dump($this->buttons);

		$up_down_field = $obj['up_down_field'];
		$imagefield = $obj['image_fields'];

		//CACHE DE ANTERIORES BUSQUEDAS
		if (isset($data["search_text"])){
			set_grid_last_filter($metadata['entity'],$search_text);
		}else{
			$search_text = get_grid_last_filter($metadata['entity']);
		}

		$this->loadPermissions($obj['id_entity'], $data["enabled"]);
		
		$sql = $obj['query'];
		$search_fields = $obj['search_fields'];
		
		if ($search_fields != "" && $search_text != ""){
			$filters = explode(",", $search_fields);
			$search_condition = "";

			foreach($filters as $search_field){
				$search_condition = appendOR($search_condition, $search_field . " like '%".$search_text."%'");
				//trace($search_condition);
			}
			$sql =  appendcondition($sql, $search_condition);

		}
		
		$this->this_controller = $metadata['entity'];
		$this->key_field = "id";
		$orderby = $obj['order_by'];
		
		
		echo $this->list_controller($sql, $filter, $search_text, $orderby, $page, $items_per_page, $up_down_field,$imagefield );
	}
	
	
	public function list_controller($sql, $filter,$search_text, $orderby, $page, $items_per_page, $up_down_field,$imagefield){

		//global $conn;
		
			
		$offset = ($page - 1) * $items_per_page;
		if ($filter != ""){
			$sql =  appendcondition($sql, $filter);
		}
		
		$total_registros =0;
			try{
			$total_registros = count_records($sql); //obtenemos un total de los registros
		
		}catch (Exception $ex){
			$this->showMessage($ex->getMessage());
			
		}
		
		$total_pages = ceil($total_registros / $items_per_page);
		if ($page > $total_pages) $page = 1; // por si acaso
		
		if ($orderby != "") $sql .= " order by " .$orderby;
		
		$sql .= " LIMIT  $offset, $items_per_page";
		
		$datos = [];
		try{
			$datos = query($sql); //añadir los filtros que falten
		
		}catch (Exception $ex){
			$this->showMessage($ex->getMessage());
			
		}		
		
		
		$key_field = "id";
		//if (!$this->access_update) $key_field = "";
		
		$listado_html = generarTablaHTML($datos, $this->this_controller, $key_field, false,true,$imagefield);
		$bloque_mensajes = "";
		foreach ($this->messages as $msg ){
	
			$bloque_mensajes .= '<div class="message"> ' . ($msg) . '</div>';
			
		}

		$botones = '<div class="grid-buttons-group">';
		if ($this->access_insert) {
			if ($this->has_quick_create_form){
				$botones .=  '<a class="boton-enlace btn-new" href="#" data-operation="new"><i class="fas fa-plus"></i> <span> Nuevo </span></a> ';		
			}else{
				$botones .=  '<a class="boton-enlace" href="'.build_URL_Controller_new($this->this_controller).$this->url_parent_related.'" ><i class="fas fa-plus"></i> <span> Nuevo </span></a> ';		
			}
			
		}
		if ($this->access_delete) $botones .=  '<a class="boton-enlace btn-del" href="#" data-operation="del"><i class="fas fa-trash"></i> <span> Eliminar </span></a> ';
		if ($this->access_read)$botones .=  '<a class="boton-enlace btn-export" href="#" data-operation="export"><i class="fas fa-file-export"></i><span> Export </span></a> ';
		if ($this->access_insert) $botones .=  '<a class="boton-enlace btn-duplicate" href="#" data-operation="duplicate"> <i class="fas fa-clone"></i> <span> Duplicar</span> </a> ';
		if ($up_down_field != ""){
			$botones .=  '<a class="boton-enlace btn-up" href="#" data-operation="up"> <i class="fas fa-arrow-up"></i></a> ';
			$botones .=  '<a class="boton-enlace btn-down" href="#" data-operation="down"> <i class="fas fa-arrow-down"></i></a> ';			
		}
		$botones .= '</div>';
		
		$opt_vistas = generate_optionset("id","name",$this->view_list, $this->view_id_selected );
		$vistas_html = '<div> '.$opt_vistas.'</div>';

		//custom buttons
		$custom_buttons = "";
		foreach ($this->buttons as $btn){
			if ($btn['type']=="list"||$btn['type']=="script_list"){
				$custom_buttons .=  '<a class="boton-enlace btn-custom" href="#" data-operation="'.$btn['code'].'">'.$btn['nombre'].'</a> ';
			}
			
		} 
		$custom_buttons = '<div> '.$custom_buttons.'</div>';

		$busqueda = '<div class="grid-search-group"><input type="text" name="q" value="'.$search_text.'">';
		$busqueda .=  '<a class="boton-enlace btn-search" href="#" data-operation="search"><i class="fas fa-search"></i><span> Buscar </span> </a>';  
		$busqueda .=  '<a class="boton-enlace btn-reset" href="#" data-operation="search"><i class="fa-solid fa-square-xmark"></i> <span> Reset </span></a>';  
		$busqueda .=  '</div>';
		
		$grid_toolbar = '<div class="gridtoolbar">'.$vistas_html.$botones .$busqueda.$custom_buttons.'</div>';
		
		$paginacion = "";
		if ($total_pages > 1){
			if ( $page > 1) {
				$paginacion .= '<a class="boton-enlace grid-pagina" href="#" data-page="'.($page-1).'"> << Anterior </a> ';
			}
			if ( $page < $total_pages) {
				$paginacion .= '<a class="boton-enlace grid-pagina" href="#" data-page="'.($page+1).'"> Siguiente >> </a> ';
			}
		}
		$total = "<strong>  $page / $total_pages ($total_registros registros)</strong>";
		return  $bloque_mensajes.$grid_toolbar . $listado_html . $paginacion .$total;
		
	}
	
	public function execute_render($data){
		//obtenemos el controller name
		$view_id = $data["view"];
		$obj = EntityManager::GetVista($view_id);
		$metadata = EntityManager::GetEntityById($obj['id_entity']);
		$controller = $metadata['entity'];
		$this->loadPlugins($controller);
		
		$record = array();
		if (isset($data["field"]) && $data["field"] != ""){
			$record[$data["field"]] = $data["value"];
		}
		
		$estructura = EntityManager::GetEstructura($controller);
		
		set_default_formulas($estructura, $record);
		$this->preRenderform('',$record);
		
		$form = FormManager::Run($controller,$record,"quick_create");
		//$form = generate_form_fields($estructura ,$record, false);
		
		
		$html = '<form id="modal-form-data" action="'.get_URL_BASE().'/forms/subgrid.php" method="post" enctype="multipart/form-data">
 
			<div id="modal-form-insert">'.$form.'</div>
			
			</form>
			<div > 
			<button id="acceptButton" >Aceptar</button>
			<button id="closeButton"  onclick="javascript:ocultarFormulario();">Cerrar</button> 			 
			</div>';
		
		echo $html;
	}
	
	
	public function execute_insert($data){
		
		//solo afecta al insert, porque pueden mezclarse variables del insert y el select
		$op_data = json_decode($data["op_data"],true); 
		
		$view_id = $op_data["view"];
		
		$obj = EntityManager::GetVista($view_id);
		$metadata = EntityManager::GetEntityById($obj['id_entity']);
		$controller = $metadata['entity'];
		
		$this->loadPlugins($controller);
		
		$key_field = "id";
				
		$record = array();
		
		//recuperamos la estructura a tratar
		$estructura = EntityManager::GetEstructura($controller);
		foreach($estructura  as $campo){
			if ( $campo["dbcolumn"] != $key_field  && isset($data[$campo["dbcolumn"]])){
				$record[$campo["dbcolumn"]] = mask($data[$campo["dbcolumn"]], $campo["type"]);
			}
		}
		//var_dump($estructura);
				
		
		if (count($_FILES)>0){
			
			
			foreach($estructura  as $campo){
				if ( $campo["type"] == 'file' ){
					trace('este es el campo de tipo fichero '. $campo["dbcolumn"]);
					$file_id = $campo["dbcolumn"];
					dump($_FILES);
					if (isset($_FILES[$file_id ]) && $_FILES[$file_id]["name"]!= ""){
						
						//el directorio de destino será relativo a la entidad que estamos manteniendo
						$directorio = dirname(__DIR__, 1)."/entity_files/".$controller."/";
						if (!file_exists($directorio)){
							mkdir($directorio, 0777, true);
						}
						$rand = newRandomID(4);
						//mover a directorio de destino
						$archivo = $directorio .$rand."_". basename($_FILES[$file_id]["name"]);
						
	
						//establecemos la ruta en el registro
						$record[$campo["dbcolumn"]] = "/entity_files/".$controller."/".$rand."_".basename($_FILES[$file_id]["name"]);
						
						if (!move_uploaded_file($_FILES[$file_id]["tmp_name"], $archivo)){
							
							trace('error al guardar fichero');
						}
						
					} 
				}
			}
		}
		
		//insertamos en BBDD
		$id_generado = dbinsert($controller, $record );
		
		//evento after insert
		$this->postInsert($id_generado, $record);

	}
	
	public function execute_export($data){
		
		
		
		$page = 1;
		if (isset($data["page"]) && is_numeric( $data["page"])){
			$page = $data["page"];
		}
		$items_per_page = get_config('VIEW_PAGE_SIZE');
		if (isset($data["pagesize"]) && is_numeric( $data["pagesize"])){
			$items_per_page = $data["pagesize"];
		}
		$view_id = $data["view"];
		$search_text = $data["search_text"];
		
		$obj = EntityManager::GetVista($view_id);
		$metadata = EntityManager::GetEntityById($obj['id_entity']);
		$controller = $metadata['entity'];
		
		$sql = $obj['query'];
		$search_fields = $obj['search_fields'];
		
		if (isset($data["field"]) && $data["field"] != ""){
			$filter = $data["field"] ."=" . quote($data["value"]);
			$sql =  appendcondition($sql, $filter);
		}
		if ($search_fields != "" && isset($data["search_text"]) && $data["search_text"] != ""){
			$filters = explode(",", $search_fields);
			$search_condition = "";

			foreach($filters as $search_field){
				$search_condition = appendOR($search_condition, $search_field . " like '%".$data["search_text"]."%'");
				//trace($search_condition);
			}
			$sql =  appendcondition($sql, $search_condition);
			//trace($sql);
		}
		
		
		$orderby = $obj['order_by'];
		
		$datos = query($sql); //añadir los filtros que falten
		
		if (count($datos) >0){
			$ejemplo = $datos[0];
			// Crear y descargar el archivo CSV
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename="'.$controller.'.csv"');

			$output = fopen('php://output', 'w');
			$listaDeClaves = array_keys($ejemplo);
				
			fputcsv($output, $listaDeClaves, ';');
			
			foreach ($datos as $fila){
				// Cambiar el formato de coma decimal
				//$fila = array_map(function ($value) {
				//	return str_replace('.', ',', $value);
				//}, $fila);
				
				fputcsv($output, $fila, ';');
			}
			fclose($output);
			die();
		}
		

	}
	
}
$obj = new Form_Subgrid();
$obj->Run();



