<?php 

PluginManager::RegisterPlugin(new my_new_plugin());

class my_new_plugin extends PluginInterface {
	
	protected $data; //use it at will between related events
	
	public function postUpdate($item, $datos){ }
	public function postInsert($item, $datos){ 
	    $datos['codigo'] = nextSequence("ventas_ofertas");
	    $datos['id']= $item;
	    dbupdate("ventas_ofertas", $datos);
	}
	public function preDuplicate($item, &$datos){ }
	public function postDuplicate($item, $new_item){ }
	
	public function customContent($item){ 	}
	
	public function setDefaultValues(&$datos){  }
	
	public function preRenderform($item, &$datos){ 
		
	}
	public function onCustomButton($operation, $item, $datos){ 
		
	}
	public function postUploadFile($file){ $this->showMessage("postUploadFile");}

}
