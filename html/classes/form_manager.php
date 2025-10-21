<?php 

class FormManager {

	//--------------------------------------------------------	
	//main method
	//--------------------------------------------------------
	public static function Run($entity_name, $datos, $type = 'main'){
		
		$html = "";
		$entity_id = EntityManager::GetEntityId($entity_name);
		$formdata = FormManager::GetForms($entity_id, $type);
		$estructura = EntityManager::GetEstructura($entity_name);
		$status = get_status_field($estructura);
		
		$form_enabled = 1;
		if ($status != null && !is_null($datos[$status['dbcolumn']]) && $datos[$status['dbcolumn']]== 0){ //registro inactivo
			$form_enabled = 0;
			foreach($estructura as &$campo){
				$campo['disabled'] = 1; 
			}
		}

		if (count($formdata)>0){
			//nos quedamos con el primero (de momento)
			foreach($formdata as $form){
				$html .= FormManager::PrintForm($form,$estructura, $datos, $form_enabled);
			}
					
		}else {
			$html = generate_form_fields($estructura, $datos);
		}
		
		return $html;
		
	}
	


	public static function HasQuickForm($entity_id){
		$data = query("SELECT 1 FROM app_forms WHERE id_entity = ".quote($entity_id) . " and tipo='quick_create'");
		if (count($data)>0){return true;}
		return false;
	}
	
	public static function GetForms($entity_id, $type){
		$data = query("SELECT * FROM app_forms WHERE id_entity = ".quote($entity_id) . " and tipo=".quote($type));
		
		return $data;
		
	}
	
	public static function GetAreas($form_id){
		$data = query("SELECT * FROM app_form_areas WHERE id_form = ".quote($form_id) . " order by orden");
		
		return $data;
	}
	
	public static function GetSections($area_id){
		$data = query("SELECT * FROM app_form_sections WHERE id_area = ".quote($area_id) . " order by orden");
		
		return $data;
		
	}
	public static function GetFormControls($section_id){
		$data = query("SELECT * FROM app_user_controls WHERE id_seccion = ".quote($section_id) . " order by orden");
		
		return $data;
	}
	
	public static function PrintForm($formdata,$estructura, $datos, $form_enabled){
		$html = "";
		
		$areadata = FormManager::GetAreas($formdata['id']);
		

		foreach($areadata as $area){
			$html .= FormManager::PrintArea($area,$estructura, $datos, $form_enabled);
		}

		return $html;

		
	}
	
	public static function PrintArea($areadata,$estructura, $datos, $form_enabled){
		$html = '<div class="formarea" id="'.$areadata['nombre'].'">';
		//$html .= $areadata['nombre'];
		
		$list = FormManager::GetSections($areadata['id']);
		//$html .= " " . count($list);

		foreach($list as $item_list){
			$html .= FormManager::PrintSection($item_list,$estructura, $datos, $form_enabled);
		}
		$html .= "</div>";
		return $html;
		
	}
	
	public static function PrintSection($sectiondata,$estructura, $datos, $form_enabled){
		
		$html = '<div class="formsection" id="'.$sectiondata['nombre'].'" style="width:'.$sectiondata['width'].'%">';
		$html .= '<div class="control-list" >';
		if (isset($sectiondata['label']))
			$html .= '<div class="formcontrol col-100"><strong>'.$sectiondata['label'].' </strong></div>';
		
		$list = FormManager::GetFormControls($sectiondata['id']);

		
		foreach($list as $item_list){
			$tipo  = $item_list['control_type'];
			$view_id  = $item_list['id_vista'];
			$graph_id = $item_list['id_grafico'];
			$view_column_rel  = $item_list['view_column_rel'];
			$lbl = $item_list['label'];
			$width = $item_list['width'];
			$control_name = $item_list['nombre'];
			
			if ($tipo=='column'){
				$html .= FormManager::PrintFormControl($item_list,$estructura, $datos);
			}else if ($tipo=='view' && $datos['id'] != ''){
				
				if ($lbl != ''){
					$html .= '<label style="display:block;font-weight:bold;padding-top:20px;" >'.$lbl.'</label>';
				}
				
				
				$html .= print_grid($view_id , $view_column_rel , $datos['id'],$control_name, $form_enabled);
			}else if ($tipo=='graph' && $datos['id'] != ''){
				
				if ($lbl != ''){
					$html .= '<label style="display:block;font-weight:bold;padding-top:20px;" >'.$lbl.'</label>';
				}
				$html .= print_graphic($graph_id , $view_column_rel , $datos['id'], $lbl);
			}
			
		}
		$html .= "</div>";
		$html .= "</div>";
		return $html;
		
	}
	public static function PrintFormControl($controldata,$estructura, $datos){
		
		$campo = getFieldDefinition($estructura, $controldata['dbcolumn']);
		
		$html = generate_field($campo, $datos, true, $controldata);	
			
		return $html;
		
	}
	
	
	
}


//utilidad para recuperar un elemento del array de estructura de campos
function getFieldDefinition($estructura,$field){
	$fld = null;
	
	foreach($estructura  as $campo){
		if ( $campo["dbcolumn"] == $field ){
			$fld = $campo;
			break;
		}
	}
	return $fld;
}


	

function generate_field($campo, $datos, $mostrar_calculados , $controldata) {
	
	//tomamos datos del POST para refrescar los datos
	$html = "";
	$width = $controldata['width'];

	$val = (isset($datos[$campo["dbcolumn"]]) ? $datos[$campo["dbcolumn"]] : $campo["value"] );
	if ($mostrar_calculados && $campo["type"] == "calc" && isset($campo["formula"]) && $campo["formula"] != ''){
		$val = "valor calculado";
		$val = calcular_campo_calculado($campo["formula"], $datos);
	}
	$disabled = (($campo["disabled"]==1) ? "disabled=true" : "" );
	$required = (($campo["required"]==1) ? "required=true" : "" );
	$locked = (($campo["disabled"]==1) ? "true" : "false" );
	$val = htmlspecialchars($val, ENT_QUOTES);
	
	if ( $campo["type"]!= "calc" || ($mostrar_calculados && $campo["type"] == "calc")){
			
		if ($campo["hidden"]==1){
			
			$html .= '<input type="hidden" id="'.$campo["dbcolumn"].'" name="'.$campo["dbcolumn"].'" value="'.$val.'">';
			//$html .= '<td>  <input type="hidden" id="'.$campo["dbcolumn"].'" name="'.$campo["dbcolumn"].'" value="'.$val.'">';
			
		}else {
			$html .= '<div class="formcontrol '.$width.'">';
			
			$html .= '<label >'.$campo["label"].'</label>';

			if (empty($campo["user_control"])){
				
				switch ($campo["type"]){
					case "int":
						$html .= '<input '.$disabled.' '.$required.'  type="number" id="'.$campo["dbcolumn"].'" name="'.$campo["dbcolumn"].'" value="'.$val.'" />';
						break;
					case "decimal":
						$html .= '<input '.$disabled.' '.$required.'  type="number" step="any" id="'. $campo["dbcolumn"].'"  name="'.$campo["dbcolumn"].'" value="'.$val.'" />';
						break;
						
					case "text":
					case "guid":
						$html .= '<input '.$disabled.' '.$required.'  type="text" maxlength="'.$campo["max"].'" id="'.$campo["dbcolumn"].'" name="'.$campo["dbcolumn"].'" value="'.$val.'"/>';
						break;
					case "textarea":
						$html .= '<textarea class="'.$campo["class"].'" '.$disabled.' '.$required.'  id="'.$campo["dbcolumn"].'" name="'.$campo["dbcolumn"].'" >' .$val. '</textarea>'; 

						//$html .= '<input type="text" maxlength="'.$campo["max"].'" id="'.$campo["dbcolumn"].'" name="'.$campo["dbcolumn"].'" value="'.$val.'"/>';
						break;
					case "date":
						$html .= '<input '.$disabled.' '.$required.'   type="date"  id="'.$campo["dbcolumn"].'" name="'.$campo["dbcolumn"].'" value="'.$val.'"/>';
						break;
					case "datetime":
						$html .= '<input '.$disabled.' '.$required.'   type="datetime-local"  id="'.$campo["dbcolumn"].'" name="'.$campo["dbcolumn"].'" value="'.$val.'"/>';
						break;
					case "password":
						$html .= '<input '.$disabled.' '.$required.'  type="password" maxlength="'.$campo["max"].'" id="'.$campo["dbcolumn"].'" name="'.$campo["dbcolumn"].'" />';
						break;
					case "color":
						$html .= '<input '.$disabled.' '.$required.'  type="color" id="'.$campo["dbcolumn"].'" name="'.$campo["dbcolumn"].'" value="'.$val.'"/>';
						break;
						
					case "calc":
						if ($mostrar_calculados) $html .= '<span style="display:block;border:solid 1px #cccccc;padding:5px;font-weight: bold;background-color:#eeeeee;border-radius:5px;">'.$val.'</span>';
						break;
					case "file":
						$html .= '<div><input type="file" name="'.$campo["dbcolumn"].'" /></div>';
						if (isset($datos[$campo["dbcolumn"]])) {
							$html .= '<div><a href="'.$datos[$campo["dbcolumn"]].'" download target="_blank">'.basename($datos[$campo["dbcolumn"]]).'</a></div>';
						}else{
							$html .= '<div>Max '.ini_get('upload_max_filesize') .'</div>';
						}
						break;
				}	
			}
			else {
				switch ($campo["user_control"]){
					case "option":
						$html .= '<select id="'.$campo["dbcolumn"].'" name="'.$campo["dbcolumn"].'" '.$disabled.' '.$required.' >';
						$html .= '<option value=""> - - - </option>';
						foreach ($campo["options"] as $opt){
							$selected = "";
							if ($opt["value"]== $val) $selected = "selected";
							$html .= '<option value="'.$opt["value"].'" '.$selected.'>'.$opt["description"].'</option>';
						}
						$html .= '</select>';
						break;
					case "lookup":
						$desc_actual = get_lookup_desc($val, $campo["lookup_table"], $campo["lookup_column"],  $campo["lookup_description"]);
						if (!isset($campo["lookup_controller"])) $campo["lookup_controller"] = $campo["lookup_table"]; // por defecto, por si acaso
						$html .= print_lookup_field($campo["dbcolumn"], $campo["lookup_table"], $campo["lookup_column"],  $campo["lookup_description"], $val, $desc_actual, $campo["lookup_controller"],$required, $disabled);
						break;
					case "image":
						$html .= '<div><input type="file" name="'.$campo["dbcolumn"].'" /></div>';
						if (isset($datos[$campo["dbcolumn"]])) {
							$html .= '<div style="border:solid 1px #cccccc;text-align:center;background-color:white;padding:5px">';
							$html .= '<img id="'.$campo["dbcolumn"].'_image" class="image_form" src="'.$datos[$campo["dbcolumn"]].'" />';
							$html .= '</div>';
							$html .= '<button type="button" class="image-clear" data-column="'.$campo["dbcolumn"].'" ><i class="fas fa-trash"></i> Borrar</button>';
						}else{
							$html .= '<div>Max '.ini_get('upload_max_filesize') .'</div>';
						}
						break;
					case "phpcode":
						$html .= '<textarea style="display:none"  id="'.$campo["dbcolumn"].'" name="'.$campo["dbcolumn"].'" >' .$val. '</textarea>'; 
						$html .= '<div class="php_editor" data-enabled='.$locked.' data-control="'.$campo["dbcolumn"].'" id="'.$campo["dbcolumn"].'_editor">'.$val.'</div>';
						
						break;
					case "javascript":
					
						$html .= '<textarea style="display:none"  id="'.$campo["dbcolumn"].'" name="'.$campo["dbcolumn"].'" >' .$val. '</textarea>'; 
						$html .= '<div class="js_editor" data-enabled="'.$locked.'" data-control="'.$campo["dbcolumn"].'" id="'.$campo["dbcolumn"].'_editor">'.$val.'</div>';
						
						break;
					case "sql_code":

						$html .= '<textarea style="display:none"  id="' . $campo["dbcolumn"] . '" name="' . $campo["dbcolumn"] . '" >' . $val . '</textarea>';
						$html .= '<div class="sql_editor" data-enabled="'.$locked.'" data-control="' . $campo["dbcolumn"] . '" id="' . $campo["dbcolumn"] . '_editor">' . $val . '</div>';

						break;
					case "richtext":
					
						$html .= '<textarea class="richtext" data-enabled="'.$locked.'" '.$disabled.' '.$required.'  id="'.$campo["dbcolumn"].'" name="'.$campo["dbcolumn"].'" >' .$val. '</textarea>'; 

						break;
				}	
				
			}
			$html .= '</div>'; //formcontrol
		}
		
		
	}
			

	return $html;
}