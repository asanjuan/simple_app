<?php

PluginManager::RegisterPlugin(new app_modules_plugin());

class app_modules_plugin extends PluginInterface {
	protected $datos; //resultado de la consulta
	
	public function postUpdate($item, $datos){}
	public function postInsert($item, $datos){ }
	public function preDuplicate($item, &$datos){ }
	public function postDuplicate($item, $new_item){ }
	public function customContent($item, $section){ 
		
	
	}
	public function setDefaultValues(&$datos){}
	public function preRenderform($item, &$datos){ 
		
		
			
	}
	public function onCustomButton($operation, $item, $datos){ 
		if ($operation == "ejecutar" ){
			try{
				
		
			} catch (PDOException $e) {
				$this->showError("Error en la consulta: " . $e->getMessage());
			}
		}
	}
	public function postUploadFile($filedata){ $this->showMessage("postUploadFile");}

}
