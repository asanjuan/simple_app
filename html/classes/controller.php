<?php
require 'classes/utility_traits.php';


class Controller {
	
	use Messages;
	use Plugin_list;
	
	public $entity_type = "table";
	public $plural_name ="";
	protected $singular_name = "";
	protected $this_controller = "";
	protected $db_table = "";
	protected $key_field = "";
	protected $search_field = "";
	protected $filter ;
	protected $calling_url = "";
	protected $key_type = "int";
	protected $custom_buttons = array();
	protected $custom_links = array();
	protected $parent_column = "";
	protected $parent_value = "";
	protected $reports;

	//estructura de los datos 
	public $estructura = array();
		
	protected $access_update = true;
	protected $access_insert = true;
	protected $access_delete = true;
	


	public function setReports($reports){
		$this->reports = $reports;
	}
	public function setPermissions($insert, $update, $delete){
		
		$this->access_update = $update;
		$this->access_insert = $insert;
		$this->access_delete = $delete;
		
	}
	
	
	public  function addCustomButton($action, $label, $type){
		//$type = list, form
		$this->custom_buttons [] = ["action" => $action, "label" => $label, "type" => $type];	
	}
	
	public  function addCustomLink($url, $label, $img='templates/img/lapiz-blog.svg',$new_window = false){		
		$this->custom_links [] = ["url" => $url, "label" => $label, "img" => $img , "new_window" => $new_window];	
		
	}
	

	public function Setup(
		$plural_name,
		$singular_name,
		$this_controller,
		$db_table,
		$key_field,
		$search_field,
		$estructura,
		$type = "table"
		){
			
		$this->plural_name = $plural_name;
		$this->singular_name = $singular_name;
		$this->this_controller = $this_controller;
		$this->db_table = $db_table;
		$this->key_field = "id";
		$this->estructura = $estructura;
		$this->search_field = $search_field ;
		$this->entity_type = $type;
		
	}
	

	

	public function Run(){
		
		global $conn;
		$url_controller_item = ""; 
		$url_controller_del = ""; 
		$url_controller_new = build_URL_Controller_new($this->this_controller);
		
		$listado =  true;
		$alta = false;
		$edit = false;
		$del = false;
		$duplicate = false;
		
		$item = "";
		$operation = "";
		
		$template_name = "";
		
		//entity type.
		switch ($this->entity_type ){
			case "table": $template_name = 'templates/edit_form.php'; break;
			case "action": 
				$template_name = 'templates/action_form.php'; 
				$url_controller_new = build_URL_Controller_search($this->this_controller);
				break;
			case "panel": $template_name = 'templates/panel_form.php'; break;
		}
		
	
		if(isset($_GET["item"])) {
			$item = $_GET["item"];
			$url_controller_item = build_URL_Controller_item( $this->this_controller, $item);
			$url_controller_del = build_URL_Controller_del($this->this_controller, $item);
			$listado =  false;
			$alta = false;
			$edit = true;
			$del = false;
		}
		
		if(isset($_GET["new"])) {
			$listado =  false;
			$url_controller_item = $url_controller_new;
			$alta = true;
			$edit = false;
			$del = false;
			
		}


		if(isset($_GET["operation"])) {
			$operation = $_GET["operation"];

		}
		if(isset($_POST["operation"])) {
			$operation = $_POST["operation"];

		}
		
		$r = array(); //el registro a tratar

		if ($operation != ""){
			if (count($_FILES)>0){
				
				foreach($this->estructura  as $campo){
					if ( $campo["type"] == 'file' ){
						//trace('este es el campo de tipo fichero '. $campo["dbcolumn"]);
						$file_id = $campo["dbcolumn"];
						
						if (isset($_FILES[$file_id ]) && $_FILES[$file_id]["name"]!= ""){
							
							//el directorio de destino ser� relativo a la entidad que estamos manteniendo
							$directorio = dirname(__DIR__)."/entity_files/".$this->db_table."/";
							//$this->messages[] = $directorio;
							
							if (!file_exists($directorio)){
								mkdir($directorio, 0777, true);
							}
							//mover a directorio de destino
							$archivo = $directorio . basename($_FILES[$file_id]["name"]);
							
							//establecemos la ruta en el registro
							$fichero_relativo = "/entity_files/".$this->db_table."/".basename($_FILES[$file_id]["name"]); 
							$r[$campo["dbcolumn"]] = $fichero_relativo;
							
							if (!move_uploaded_file($_FILES[$file_id]["tmp_name"], $archivo)){
								
								$this->messages[] = 'error al guardar fichero';
							}
							$this->postUploadFile($fichero_relativo);
							
						} 
					}
				}
			}
		}

		if ($alta){
			
			
			$id_generado = -1;

			
			if ($operation == "guardar"){
				
				
				foreach($this->estructura  as $campo){
					if ( $campo["type"] != "calc"  && 
						$campo["type"] != "file"  && 
						$campo["dbcolumn"] != $this->key_field &&  
						empty($campo["disabled"])
						&& isset($_POST[$campo["dbcolumn"]])
						){
						$r[$campo["dbcolumn"]] = mask($_POST[$campo["dbcolumn"]], $campo["type"]);
					}
				}
				
				
				
				try {
					if ($this->key_type != "text"){
						unset($r[$this->key_field]);
						$id_generado = dbinsert($this->db_table, $r);
						$this->postInsert($id_generado, $r);
						$_POST[$this->key_field] = $id_generado; //para que se actualice en el formulario
					}else {
						dbinsert($this->db_table, $r);
						$id_generado = $_POST[$this->key_field];
						$this->postInsert($id_generado, $r);
					}
	
					$this->messages[] = "Inserción correcta";
					
					
				} catch (PDOException $e) {
					$this->messages[] = "Error en la conexi�n: " . $e->getMessage();
				}
				

				$datos = dbgetbyid($this->db_table,$id_generado);
				
				$this->preRenderform($id_generado,$datos);
				$campos_html = FormManager::Run($this->this_controller, $datos);
				
				$custom_content = $this->customContent($id_generado);
				
				include $template_name ;
				
			}else {
				//combinamos la informaci�n del POST con la estructura y lo volvemos a pintar
				if ($operation != ""){
					$this->onCustomButton($operation, $item, $_POST);
				}
				
				$this->preRenderform($id_generado, $_POST); //false no muestra campos calculados
				$campos_html = FormManager::Run($this->this_controller, $_POST);
				
				$custom_content = $this->customContent('');
				
				include $template_name ;
			}


		}else if ($edit){
		
			
			if ($operation != ""){
				
								
				if ($operation == "guardar"){
					try{
						
						
						//var_dump($this->estructura);
						foreach($this->estructura  as $campo){
							
							if ( $campo["type"] != "calc" && 
								$campo["type"] != "file" && 
								$campo["dbcolumn"] != $this->key_field &&  
								empty($campo["disabled"])
								&& isset($_POST[$campo["dbcolumn"]])
								){
								$r[$campo["dbcolumn"]] = mask($_POST[$campo["dbcolumn"]], $campo["type"]);
							}
						}
						
						$r[$this->key_field] = $item;
						dbupdate($this->db_table, $r, $this->key_field);
						$this->postUpdate($item, $r);
						$this->messages[] = "Dato actualizado correctamente";
						
					} catch (PDOException $e) {
						$this->messages[] = "Error en la conexi�n: " . $e->getMessage();
					}
					
				} else  if ($operation == "duplicate"){
					$item = $this->duplicate_item($item);
					$url_controller_item = build_URL_Controller_item( $this->this_controller, $item);
					send_redirect($url_controller_item);
					die();
					
				} else {
					$this->onCustomButton($operation, $item, $_POST);
				}
				//recargamos
				$datos = dbgetbyid($this->db_table,$item);
				
				$this->preRenderform($item,$datos);
				$campos_html = FormManager::Run($this->this_controller, $datos);
				
				$custom_content = $this->customContent($item);
				include $template_name ;
					
			}else {
				
				
				$datos = dbgetbyid($this->db_table,$item);
				
				$this->preRenderform($item,$datos);
				
				$campos_html = FormManager::Run($this->this_controller, $datos);

				$custom_content = $this->customContent($item);
				include $template_name ;
				
			}
		}

		
	}//fin Run
	
	
	public function duplicate_item($elemento){
			
		//$sql = "Select * from ".$this->db_table." where ".$this->key_field."=" . quote($elemento, $this->key_type) ;	
		//$datos = query1($sql);
		$datos = dbgetbyid($this->db_table,$elemento);
				
		unset($datos[$this->key_field]); // = '';
		$new_item = '';
		
		$this->preDuplicate($elemento, $datos);
		
		$new_item = dbinsert($this->db_table, $datos);

		$this->postInsert($new_item, $datos);
		$this->postDuplicate($elemento, $new_item);
		
		
		return $new_item;
	}
	

}

