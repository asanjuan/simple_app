<?

PluginManager::RegisterPlugin(new my_new_plugin());

class my_new_plugin extends PluginInterface {
	
	protected $data; //use it at will between related events
	
	public function postUpdate($item, $datos){ }
	public function postInsert($item, $datos){ }
	public function preDuplicate($item, &$datos){ }
	public function postDuplicate($item, $new_item){ }
	
	public function customContent($item){ 
		$html = "<h3>Secciones</h3>";
		$html .= print_grid('D2E616EC2D515DBC1391A839BDD6BE0A', "id_area", $item,"");
		return $html;
	}
	
	public function setDefaultValues(&$datos){  }
	
	public function preRenderform($item, &$datos){ 
		
	}
	public function onCustomButton($operation, $item, $datos){ 
		
	}
	public function postUploadFile($filedata){ $this->showMessage("postUploadFile");}

}
