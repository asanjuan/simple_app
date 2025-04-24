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
		if ($operation == "template" ){
			try{
				$content = file_get_contents("modules/core/plugins/plugin.tpl");
				$r = ["id"=> $item, "code" => $content];
				dbupdate("app_plugins",$r,"id");
				$this->showMessage("cargar plantilla");
				
				
			} catch (PDOException $e) {
				$this->showMessage("Error al cargar la plantilla: " . $e->getMessage());
			}
		}else if ($operation == "publish" ){
			try{
				
				//obtenemos el modulo
				$fichero = $datos['filename'];
				$id_entity = $datos['id_entity'];
				$metadata = EntityManager::GetEntityById($id_entity);
				$module = EntityManager::GetModuleById($metadata['id_module']);
				$directorio = "modules/".$module['folder']."/plugins";
				$filename = $directorio."/".$fichero;
				
				if (!file_exists($directorio)){
					mkdir($directorio, 0777, true);
				}
				//guardamos en BBDD
				$r = ["id"=> $item, "code" => $datos['code']];
				dbupdate("app_plugins",$r,"id");
				
				//publicamos el fichero
				file_put_contents($filename, $datos['code']);
				
				$this->showMessage("Plugin $fichero publicado.");
				
			} catch (PDOException $e) {
				$this->showMessage("Error al publicar el plugin: " . $e->getMessage());
			}
		}else if ($operation == "load" ){
			try{
				//obtenemos el modulo
				$fichero = $datos['filename'];
				$id_entity = $datos['id_entity'];
				$metadata = EntityManager::GetEntityById($id_entity);
				$module = EntityManager::GetModuleById($metadata['id_module']);
				
				$content = file_get_contents("modules/".$module['folder']."/plugins/".$fichero);
				
				$r = ["id"=> $item, "code" => $content]; //guardamos tipo fichero y su codigo
				dbupdate("app_plugins",$r,"id");
				
				$this->showMessage("Ultima version de plugin recuperada");
				
			} catch (PDOException $e) {
				$this->showMessage("Error al cargar la plantilla: " . $e->getMessage());
			}
		}
	}
	public function postUploadFile($filedata){ $this->showMessage("postUploadFile");}

}
