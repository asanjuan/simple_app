<?php 

PluginManager::RegisterPlugin(new my_new_plugin());

class my_new_plugin extends PluginInterface {
	

	public function onCustomButton($operation, $item, $datos){ 
	    

	    
	    //$this->showMessage($entity_name);
	    
	    
	    if ($operation == "create"){
	        try{
	            
                $this->create_relationship($datos);
    	        
    	        $this->showMessage( "Relación creada ". $datos['name'] );
    	        
    	        
	        } catch (PDOException $e) {
				$this->showMessage("Error en la consulta: " . $e->getMessage());
			}
	     
	        
	    } else if ($operation == "list_create"){
	        
	        foreach($datos as $element){
	           try{
                    $rel = dbgetbyid("app_relations",$element);
                    $this->create_relationship($rel);
                    
        	        $this->showMessage( "Relación creada ". $rel['name'] );
        	        
    	        } catch (PDOException $e) {
    				$this->showMessage("Error en la consulta: " . $e->getMessage());
    			}   
	        }
	        
	        
	    } else if ($operation == "drop"){
	        try{
                $this->drop_relationship($datos);
                
	        } catch (PDOException $e) {
				$this->showMessage("Error en la consulta: " . $e->getMessage());
			}
			
	    } else if ($operation == "list_drop"){
	        foreach($datos as $element){
	           try{
                    $rel = dbgetbyid("app_relations",$element);
	       
                    $this->drop_relationship($rel);
                    $this->showMessage( "Relación borrada ". $rel['name'] );
                    
    	        } catch (PDOException $e) {
    				$this->showMessage("Error en la consulta: " . $e->getMessage());
    			}
	        }
	    }
	    
		
	}
	
	public function create_relationship($datos){
        $metadata = EntityManager::GetEntityById($datos['id_entity']);
        $entity_name = $metadata['entity'];
        
        $metadata = EntityManager::GetEntityById($datos['id_related_entity']);
        $related_entity = $metadata['entity'];
        
        $sql = "ALTER TABLE $entity_name ADD CONSTRAINT ".$datos['name'];
        $sql .= " FOREIGN KEY (".$datos['field'].") ";
        $sql .= " REFERENCES $related_entity (id) ";
        $sql .= " ON DELETE ". $datos['on_delete'];
        $sql .= " ON UPDATE ". $datos['on_update'];
        
        query($sql);
        
        $r = [];
        $r['id'] = $datos['id'];
        $r['estado'] = 1; //activa
        dbupdate("app_relations", $r);
        
       
	}

    public function drop_relationship($datos){
        
        $metadata = EntityManager::GetEntityById($datos['id_entity']);
        $entity_name = $metadata['entity'];
        
        $sql = "ALTER TABLE $entity_name  DROP FOREIGN KEY ".$datos['name'];
        query($sql);
        
        $r = [];
        $r['id'] = $datos['id'];
        $r['estado'] = 0; //activa
        dbupdate("app_relations", $r);
	}

}
