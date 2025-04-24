<?php

PluginManager::RegisterPlugin(new app_entities_plugin());

class app_entities_plugin extends PluginInterface {
	protected $datos; //resultado de la consulta
	
	public function postUpdate($item, $datos){}
	public function postInsert($item, $datos){ }
	public function preDuplicate($item, &$datos){ }
	public function postDuplicate($item, $new_item){ }
	
	public function customContent($item){ 

	}
	
	public function setDefaultValues(&$datos){}
	
	public function preRenderform($item, &$datos){ 

	}
	public function onCustomButton($operation, $item, $datos){ 
		if ($operation == "create" ){
			try{
				$entity = EntityManager::GetEntityById($datos['id_entity']);
				$table_name = $entity['entity'];
				$col_name = $datos['dbcolumn'];
				$type = EntityManager::getDBtype($datos['type'], $datos['max']);
				
				$sql = "alter table $table_name add column $col_name $type NULL";
				//trace($sql);
				query($sql);
				
				$this->showMessage("COLUMNA $table_name.$col_name CREADA correctamente");
				
			} catch (PDOException $e) {
				$this->showMessage("Error en la consulta: " . $e->getMessage());
			}
		}else if ($operation == "drop" ){
			try{
				$entity = EntityManager::GetEntityById($datos['id_entity']);
				$table_name = $entity['entity'];
				$col_name = $datos['dbcolumn'];
				
				
				$sql = "alter table $table_name drop column $col_name";
				//trace($sql);
				query($sql);
				
				$this->showMessage("COLUMNA $table_name.$col_name BORRADA correctamente");
				
			} catch (PDOException $e) {
				$this->showMessage("Error en la consulta: " . $e->getMessage());
			}
		}
	}
	public function postUploadFile($filedata){ $this->showMessage("postUploadFile");}

}
