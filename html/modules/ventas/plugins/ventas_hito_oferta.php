<?php 

PluginManager::RegisterPlugin(new ventas_hito_oferta_plugin());

class ventas_hito_oferta_plugin extends PluginInterface {
	
	protected $data; //use it at will between related events
	
	public function postUpdate($item, $datos){ }
	public function postInsert($item, $datos){ }
	public function preDuplicate($item, &$datos){ }
	public function postDuplicate($item, $new_item){ }
	
	public function customContent($item, $section){ 	}
	
	public function setDefaultValues(&$datos){  }
	
	public function preRenderform($item, &$datos){ 
	    
	    if ($item == "" || $item <0){
	        $aux = query1("select coalesce(max(orden),0)+1 as orden from ventas_hito_oferta where id_oferta =  ".quote($datos['id_oferta']));
	        $datos['orden'] =$aux['orden'];
	        
	    }	
	}
	public function onCustomButton($operation, $item, $datos){ 
		
	}
	public function postUploadFile($file){ $this->showMessage("postUploadFile");}
	
	public function preDelete($item){ }
	public function postDelete($item){ }

}
