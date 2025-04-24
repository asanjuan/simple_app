<?php 

PluginManager::RegisterPlugin(new my_new_plugin());

class my_new_plugin extends PluginInterface {
	

	public function onCustomButton($operation, $item, $datos){ 
	    
	    $metadata = EntityManager::GetEntityById($datos['id_entity']);
	    $entity_name = $metadata['entity'];
	    
	    $metadata = EntityManager::GetEntityById($datos['id_related_entity']);
	    $related_entity = $metadata['entity'];
	    
	    $this->showMessage($entity_name);
	    
	    
	    if ($operation == "create"){
	        try{

    	        $sql = "ALTER TABLE $entity_name ADD CONSTRAINT ".$datos['name'];
    	        $sql .= " FOREIGN KEY (".$datos['field'].") ";
    	        $sql .= " REFERENCES $related_entity (id) ";
    	        $sql .= " ON DELETE ". $datos['on_delete'];
    	        $sql .= " ON UPDATE ". $datos['on_update'];
    	        $this->showMessage( $sql );
    	        query($sql);
	        } catch (PDOException $e) {
				$this->showMessage("Error en la consulta: " . $e->getMessage());
			}
	        
	        
	    }
	    else if ($operation == "drop"){
	        try{

    	        $sql = "ALTER TABLE $entity_name  DROP FOREIGN KEY ".$datos['name'];
    	        query($sql);
	        } catch (PDOException $e) {
				$this->showMessage("Error en la consulta: " . $e->getMessage());
			}
	    }
	    
		
	}


}
