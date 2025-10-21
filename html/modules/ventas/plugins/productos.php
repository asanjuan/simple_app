<?php 
include_once('modules/ventas/plugins/ventas_lib.php');

PluginManager::RegisterPlugin(new my_new_plugin());

class my_new_plugin extends PluginInterface {
	
	protected $data; //use it at will between related events
	
	public function postUpdate($item, $datos){  }
	public function postInsert($item, $datos){ 
	    
	    //query("update productos set nombre= concat(nombre, ' insertado') where id=".quote($item));
	    
	}
	public function preDuplicate($item, &$datos){ 
	    $datos['nombre'] = $datos['nombre'] . " - copia";
	    
	}
	public function postDuplicate($item, $new_item){
	    
	}
	
	public function customContent($item){ 	}
	
	public function setDefaultValues(&$datos){  }
	
	public function preRenderform($item, &$datos){ 
	    
		
	}
	public function onCustomButton($operation, $item, $datos){ 
	    
	    if ($operation == "test_api"){
	        
            $params = get_all_config();
            $url = $params['API_CONSULTA']."?query=9";
            $securityToken = $params['API_TOKEN']; 
            
            $jsonData =getRequest($url, $securityToken );
            
            $this->showMessage("Obtenidos " . count($jsonData));

	        
	    }
		
	}
	public function postUploadFile($filedata){ $this->showMessage("postUploadFile");}

}
