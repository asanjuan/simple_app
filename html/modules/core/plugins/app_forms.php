<?php

PluginManager::RegisterPlugin(new my_new_plugin());

class my_new_plugin extends PluginInterface {
	
	protected $data; //use it at will between related events
	
	public function postUpdate($item, $datos){ }
	public function postInsert($item, $datos){ }
	public function preDuplicate($item, &$datos){ }
	public function postDuplicate($item, $new_item){
	    
	    $new_form = $new_item;
		$old_form =  $item;   
	    $areas = query("SELECT * FROM app_form_areas where id_form=".quote($old_form));
		foreach ($areas as $area) {
		    // code...
		    $area['id_form'] = $new_form;
		    $old_area = $area['id'];
		    $area['id']='';
		    $new_area = dbinsert('app_form_areas', $area);
		    
		    $sections = query("SELECT * FROM app_form_sections where id_area=".quote($old_area));
    		foreach ($sections as $s) {
    		    // code...
    		    $s['id_area'] = $new_area;
    		    $old_section = $s['id'];
    		    $s['id']='';
    		    $new_section = dbinsert('app_form_sections', $s);
    		    
    		    $controls = query("SELECT * FROM app_user_controls where id_seccion=".quote($old_section));
        		foreach ($controls as $uc) {
        		    // code...
        		    $uc['id_seccion'] = $new_section;
        		    $uc['id']='';
        		    dbinsert('app_user_controls', $uc);
        		}
    		    
    		}
		    
		}
	}
	
	public function customContent($item, $section){ 
		//$html = "<h3>Areas</h3>";
		//$html .= print_grid('44F547C359298694C704DEE48FA6BCCB' , "id_form", $item,"");
		//return $html;
	}
	
	public function setDefaultValues(&$datos){  }
	
	public function preRenderform($item, &$datos){ 
				
	}
	public function onCustomButton($operation, $item, $datos){ 
		if ($operation == "configure" ){
		    //dump($datos);
		    //trace($item);
			try{
				//$r = ["id" => $item, "filas" =>$datos["filas"], "columnas" =>$datos["columnas"] ];
				//dbupdate("app_forms",$r);
				$datos = dbgetbyid("app_forms",$item);
				//dump($datos);
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
