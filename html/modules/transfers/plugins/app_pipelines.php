<?php 

PluginManager::RegisterPlugin(new app_pipelines_plugin());

class app_pipelines_plugin extends PluginInterface {
	
	protected $data; //use it at will between related events
	
	public function postUpdate($item, $datos){ }
	public function postInsert($item, $datos){ }
	public function preDuplicate($item, &$datos){ }
	public function postDuplicate($item, $new_item){ }
	
	public function customContent($item){ 	}
	
	public function setDefaultValues(&$datos){  }
	
	public function preRenderform($item, &$datos){ 
		
	}
	public function onCustomButton($operation, $item, $datos){ 
	    
	    $id_solution = $datos['id_solution'];
	    $id_environment = $datos['id_environment'];
	    
	    //to-do: verificar que la solución está completamente capturada.
	    
	    if ($operation == "run") {
	        
	        
			$solution = dbgetbyid("app_solutions",$id_solution);
			sendToRemoteEnvironment("app_solutions", [$solution] , $id_environment);
			
			
			$objects = query("select * from app_solution_objects where id_solution = " . quote($id_solution));
			sendToRemoteEnvironment("app_solution_objects", $objects , $id_environment);
	    }
		
	}
	public function postUploadFile($file){ $this->showMessage("postUploadFile");}
	
	public function preDelete($item){ }
	public function postDelete($item){ }

}

function sendToRemoteEnvironment($entity, $records, $id_environment){
    $remote_controller = $entity;
			
	$remote = dbgetbyid("app_remote_environments",$id_environment);
	$remote['url'] .= '/api/entity.php?controller=' .$remote_controller;
	$apikey = $remote['apikey'];
	
	
	$headers = [];
	$headers [] = "Apikey: " .$apikey ;
	$respuesta = Http::post($remote['url'], $records, $headers);
    
}



