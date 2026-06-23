<?php 

PluginManager::RegisterPlugin(new my_new_plugin());

class my_new_plugin extends PluginInterface {
	

	public function onCustomButton($operation, $item, $datos){ 
	    

	    
	    //$this->showMessage($entity_name);
	    
	    
	    if ($operation == "create"){
	        try{
	            
                $this->create_index($datos);
    	        
    	        $this->showMessage( "Indice creado ". $datos['name'] );
    	        
    	        
	        } catch (PDOException $e) {
				$this->showMessage("Error en la consulta: " . $e->getMessage());
			}
	     
	        
	    } else if ($operation == "list_create"){
	        
	        foreach($datos as $element){
	           try{
                    $rel = dbgetbyid("app_indexes",$element);
                    $this->create_index($rel);
                    
        	        $this->showMessage( "Indice creada ". $rel['name'] );
        	        
    	        } catch (PDOException $e) {
    				$this->showMessage("Error en la consulta: " . $e->getMessage());
    			}   
	        }
	        
	        
	    } else if ($operation == "drop"){
	        try{
                $this->drop_index($datos);
                $this->showMessage( "Indice borrado ". $datos['name'] );
                
	        } catch (PDOException $e) {
				$this->showMessage("Error en la consulta: " . $e->getMessage());
			}
			
	    } else if ($operation == "list_drop"){
	        foreach($datos as $element){
	           try{
                    $rel = dbgetbyid("app_indexes",$element);
	       
                    $this->drop_index($rel);
                    $this->showMessage( "Indice borrado ". $rel['name'] );
                    
    	        } catch (PDOException $e) {
    				$this->showMessage("Error en la consulta: " . $e->getMessage());
    			}
	        }
	    }
	    
		
	}
	
	public function create_index($datos){
        $metadata = EntityManager::GetEntityById($datos['id_entity']);
        $entity_name = $metadata['entity'];
        
        $unique = "";
        if ($datos['index_type']=="unique"){
            $unique = " UNIQUE ";
        }
        
        
        $sql = "CREATE $unique INDEX ".$datos['name'];
        $sql .= " ON $entity_name  (".$datos['columnas'].") ";

        
        
        query($sql);
        
        $r = [];
        $r['id'] = $datos['id'];
        $r['estado'] = 1; //activa
        dbupdate("app_indexes", $r);
        
       
	}

    public function drop_index($datos){
        
        $metadata = EntityManager::GetEntityById($datos['id_entity']);
        $entity_name = $metadata['entity'];
        
        $sql = "DROP INDEX ".$datos['name'];
        $sql .= " ON $entity_name  ";
        query($sql);
        
        $r = [];
        $r['id'] = $datos['id'];
        $r['estado'] = 0; //activa
        dbupdate("app_indexes", $r);
	}

}
