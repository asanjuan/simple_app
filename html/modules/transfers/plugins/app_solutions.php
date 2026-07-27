<?php 

PluginManager::RegisterPlugin(new my_new_plugin());

class my_new_plugin extends PluginInterface {
	
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
	    
	    
	    if ($operation == "snapshot"){
            try{ 
                //recuperamos los objetos de la solución y le generamos su json, luego lo pasamos a base64 para transportarlo fácilmente.
                $sql = "select * from app_solution_objects where id_solution = ".quote($item);
                $objetos = query($sql);
                //dump($objetos);
                
                foreach ($objetos as $objeto){
                    $prov = DeploymentRegistry::get($objeto['entity']);
                    $json = $prov->export($objeto['object_id']);
                    $b64 = ($json);
                    
                    dbupdate("app_solution_objects", ["id" => $objeto['id'], "json_data" => $b64] );
 
                }
                $this->showMessage("Imagen creada. Listo para transportar");
            } catch (PDOException $e) {
                $this->showMessage("Error: " . $e->getMessage());
            }
	        
	    }else if ($operation == "deploy"){
	        try{ 
                //recuperamos los objetos de la solución y le generamos su json, luego lo pasamos a base64 para transportarlo fácilmente.
                $sql = "select * from app_solution_objects where id_solution = ".quote($item);
                $objetos = query($sql);
                //dump($objetos);
                
                //recuperar la prioridad por tipo de objeto
                foreach ($objetos as &$objeto){
                    $priority = DeploymentRegistry::getPriority($objeto['entity']);
                    $objeto['priority'] = $priority;

                }
                unset($objeto);
                
                //ordenar por la prioridad
                usort($objetos, function($a, $b) {
                    return ($a['priority'] ?? PHP_INT_MAX) <=> ($b['priority'] ?? PHP_INT_MAX);
                });
                
                //recorrer
                foreach ($objetos as $objeto){
                    
                    $prov = DeploymentRegistry::get($objeto['entity']);
                    if ($prov->validate($objeto['json_data'])){
                        $b64 = $prov->apply($objeto['json_data']);
                        
                    }else {
                        $this->showMessage( $objeto['entity'] . " no válido");
                    }

                }
                 $this->showMessage("Despliegue completado");
                 
            } catch (PDOException $e) {
                $this->showMessage("Error: " . $e->getMessage());
            }
	    }
	}
	public function postUploadFile($file){ $this->showMessage("postUploadFile");}
	
	public function preDelete($item){ }
	public function postDelete($item){ }

}
