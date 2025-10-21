<?php

PluginManager::RegisterPlugin(new my_new_plugin());

class my_new_plugin extends PluginInterface {
	
	protected $data; //use it at will between related events
	
	public function postUpdate($item, $datos){ }
	public function postInsert($item, $datos){ }
	public function preDuplicate($item, &$datos){ }
	public function postDuplicate($item, $new_item){ }
	
	public function customContent($item){ 
		$html = "<h3>Areas</h3>";
		$html .= print_grid('44F547C359298694C704DEE48FA6BCCB' , "id_form", $item,"");
		return $html;
	}
	
	public function setDefaultValues(&$datos){  }
	
	public function preRenderform($item, &$datos){ 
				
	}
	public function onCustomButton($operation, $item, $datos){ 
		if ($operation == "configure" ){
			try{
				$r = ["id" => $item, "filas" =>$datos["filas"], "columnas" =>$datos["columnas"] ];
				dbupdate("app_forms",$r);
				$id_entity = $datos["id_entity"];
				$metadatos = EntityManager::GetEntityById($id_entity);
				$nombre_entidad = $metadatos['entity'];
				//trace ($nombre_entidad);
				
				$default_width = intval(100/$datos["columnas"]);
				//creamos tantas areas como nos diga
				for ($i=0; $i< $datos["filas"]; $i++){
					$area = ["id_form" =>$item,  "Nombre" =>$nombre_entidad . " $i", "orden" =>$i ];
					$id_area = dbinsert("app_form_areas",$area);
					for ($j=0; $j< $datos["columnas"]; $j++){
						$seccion = [
							"id_area" =>$id_area,  
							"Nombre" =>$nombre_entidad . " $i $j", 
							"label" => $nombre_entidad . " $i $j",
							"width" => $default_width,							
							"orden" =>$j 
							];
						$id_seccion = dbinsert("app_form_sections",$seccion);
						if ($i==0 && $j ==0){
							//introducimos los controles de todos los campos en la primera seccion
							$estructura = EntityManager::GetEstructura($nombre_entidad);
							foreach($estructura as $campo){
								$control = [
								"id_seccion" =>$id_seccion,  
								"control_type" =>"column", 
								"dbcolumn" => $campo['dbcolumn'],					
								"orden" =>$campo['orden'],
								"width" => "col-100"
								];
								dbinsert("app_user_controls",$control);
							}
		
						}
					}
				}
				
				
			} catch (PDOException $e) {
				$this->showMessage("Error en la consulta: " . $e->getMessage());
			}
		}
		
	}
	public function postUploadFile($filedata){ $this->showMessage("postUploadFile");}

}
