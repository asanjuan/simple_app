<?php

PluginManager::RegisterPlugin(new app_modules_plugin());

class app_modules_plugin extends PluginInterface {
	protected $datos; //resultado de la consulta
	
	public function postUpdate($item, $datos){}
	public function postInsert($item, $datos){ }
	public function preDuplicate($item, &$datos){ }
	public function postDuplicate($item, $new_item){ }
	public function customContent($item){ 
		
		return print_grid('1499C6B48BF03EE27798C87A0DB98954' , "id_module", $item,"");
	}
	public function setDefaultValues(&$datos){}
	public function preRenderform($item, &$datos){ 
		
		
			
	}
	public function onCustomButton($operation, $item, $datos){ 
		if ($operation == "ejecutar" ){
			try{
				
		
			} catch (PDOException $e) {
				$this->showMessage("Error en la consulta: " . $e->getMessage());
			}
		}
	}
	public function postUploadFile($filedata){ $this->showMessage("postUploadFile");}

}
