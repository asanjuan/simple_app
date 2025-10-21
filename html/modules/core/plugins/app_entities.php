<?php

PluginManager::RegisterPlugin(new app_entities_plugin());


class app_entities_plugin extends PluginInterface {
	protected $datos; //resultado de la consulta
	
	public function postUpdate($item, $datos){}
	public function postInsert($item, $datos){ }
	public function preDuplicate($item, &$datos){ }
	public function postDuplicate($item, $new_item){ 
	    
	    //cambiamos el nombre
	    query("update app_entities set entity = concat(entity,'_copy'), plural_name = concat(plural_name,' COPY') where id=".quote($new_item));
	    
	    //duplicamos Columnas
		$data = query("SELECT * FROM app_entity_columns where id_entity=".quote($item));
		foreach ($data as $col) {
		    // code...
		    $col['id_entity'] = $new_item;
		    $col['id']='';
		    dbinsert('app_entity_columns', $col);
		}
		//duplicamos Vistas
		$data = query("SELECT * FROM app_views where id_entity=".quote($item));
		foreach ($data as $v) {
		    // code...
		    $v['id_entity'] = $new_item;
		    $v['id']='';
		    dbinsert('app_views', $v);
		}
		
		//duplicamos javascript
		$data = query("SELECT * FROM app_scripts where id_entity=".quote($item));
		foreach ($data as $v) {
		    // code...
		    $v['id_entity'] = $new_item;
		    $v['id']='';
		    $v['estado']=0;
		    dbinsert('app_scripts', $v);
		}
		
		//duplicamos plugins
		$data = query("SELECT * FROM app_plugins where id_entity=".quote($item));
		foreach ($data as $v) {
		    // code...
		    $v['id_entity'] = $new_item;
		    $v['id']='';
		    $v['estado']=0;
		    dbinsert('app_plugins', $v);
		}
		
		//duplicamos formularios
		$data = query("SELECT * FROM app_forms where id_entity=".quote($item));
		foreach ($data as $v) {
		    // code...
		    $v['id_entity'] = $new_item;
		    $old_form = $v['id'];
		    $v['id']='';
		    $new_form = dbinsert('app_forms', $v);
		    
		    $areas = query("SELECT * FROM app_form_areas where id_form=".quote($old_form));
    		foreach ($areas as $area) {
    		    // code...
    		    $area['id_form'] = $new_form;
    		    $old_area = $area['id'];
    		    $area['id']='';
    		    $new_area = dbinsert('app_form_areas', $area);
    		    
    		    $sections = query("SELECT * FROM app_form_sections where id_area=".quote($old_area));
        		foreach ($sections as $s) {
        		    // code...
        		    $s['id_area'] = $new_area;
        		    $old_section = $s['id'];
        		    $s['id']='';
        		    $new_section = dbinsert('app_form_sections', $s);
        		    
        		    $controls = query("SELECT * FROM app_user_controls where id_seccion=".quote($old_section));
            		foreach ($controls as $uc) {
            		    // code...
            		    $uc['id_seccion'] = $new_section;
            		    $uc['id']='';
            		    dbinsert('app_user_controls', $uc);
            		    
            		    
            		    
            		}
        		    
        		}
    		    
    		}
		    
		}
		
	    
	}
	
	public function customContent($item){ 

	}
	
	public function setDefaultValues(&$datos){}
	
	public function preRenderform($item, &$datos){ 

	}
	public function onCustomButton($operation, $item, $datos){ 
		if ($operation == "create" ){
			try{
				$tabla = $datos['entity'];
				$campo_principal = $datos['campo_principal'];
				
				$sql = "create table $tabla ( 
						id char(32) not null primary KEY,";
				if ($campo_principal != ""){
				    $sql .= " $campo_principal varchar(100) null,";
				} 
		
				$sql .= " fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
						fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP  )";
				query($sql);
				
				$this->showMessage("Tabla $tabla creada correctamente");
				
			} catch (PDOException $e) {
				$this->showMessage("Error en la consulta: " . $e->getMessage());
			}
		}else if ($operation == "create_cols" ){
			
			
				$estructura = EntityManager::GetEstructura($datos['entity']);
				$table_name = $datos['entity'];
				
				foreach($estructura as $columna){
					$col_name = $columna['dbcolumn'];
					$type = EntityManager::getDBtype($columna['type'], $columna['max']);
					
					$sql = "alter table $table_name add column $col_name $type NULL";
					try{
						query($sql);
						
					} catch (PDOException $e) {
						//$this->showMessage("Error en la consulta: " . $e->getMessage());
					}
					
				}
				
				$this->showMessage("Cambios aplicados");
				
			
			
		}else if ($operation == "drop" ){
			try{
				$tabla = $datos['entity'];
								
				$sql = "drop table $tabla ";
				query($sql);
				
				$this->showMessage("Tabla $tabla borrada correctamente");
				
			} catch (PDOException $e) {
				$this->showMessage("Error en la consulta: " . $e->getMessage());
			}
		}else if ($operation == "load" ){
			try{
				global $dbname;
				
				$tabla = $datos['entity'];
								
				$sql = "insert into app_entity_columns (id, id_entity, dbcolumn, label, type, `max`, hidden, disabled)
						select HEX(RANDOM_BYTES(16)) as id,  e.id as id_entity, COLUMN_NAME , COLUMN_NAME ,
						case data_type 
						when 'int' then 'int'   
						when 'varchar' then 'text' 
						when 'char' then (case when CHARACTER_MAXIMUM_LENGTH=32 THEN 'guid' else 'text' end) 						
						when 'text' then 'textarea'  
						when 'float' then 'decimal'  
						when 'double' then 'decimal'  
						when 'real' then 'decimal' 
						when 'timestamp' then 'datetime'
						else data_type
						end AS tpye, coalesce(CHARACTER_MAXIMUM_LENGTH,0) AS `max`,
						(case when extra ='auto_increment' then 1 else 0 end) as hidden, 
						(case data_type when 'timestamp' then 1 else 0 end )as disabled

						from information_schema.columns AS C 
						inner join app_entities e on e.entity = C.table_name 
						where table_schema = '$dbname'
						and C.table_name ='$tabla'";
				query($sql);
				
				$this->showMessage("Columnas importadas");
				
			} catch (PDOException $e) {
				$this->showMessage("Error en la consulta: " . $e->getMessage());
			}
		}
	}
	public function postUploadFile($filedata){ $this->showMessage("postUploadFile");}

}
