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
		if ($operation == "publish" ){
			try{
				
				//obtenemos el modulo
				$fichero = $datos['filename'];
				$id_module = $datos['id_module'];
				$module = EntityManager::GetModuleById($id_module);
				$directorio = "modules/".$module['folder']."/code";
				$filename = $directorio."/".$fichero;
				
				if (!file_exists($directorio)){
					mkdir($directorio, 0777, true);
				}
				//guardamos en BBDD
				$r = ["id"=> $item, "code" => $datos['code']];
				dbupdate("app_plugin_library",$r,"id");
				
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
				$id_module = $datos['id_module'];
				$module = EntityManager::GetModuleById($id_module);
				$directorio = "modules/".$module['folder']."/code";
				$filename = $directorio."/".$fichero;
				
				$content = file_get_contents($filename);
				
				$r = ["id"=> $item, "code" => $content]; //guardamos tipo fichero y su codigo
				dbupdate("app_plugin_library",$r,"id");
				
				$this->showMessage("Ultima version de codigo recuperada");
				
			} catch (PDOException $e) {
				$this->showMessage("Error al cargar la plantilla: " . $e->getMessage());
			}
		}
	}
	public function postUploadFile($filedata){ $this->showMessage("postUploadFile");}

}
