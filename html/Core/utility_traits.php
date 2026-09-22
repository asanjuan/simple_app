<?php

trait Messages {
	
	protected $errors = array();
	protected $messages = array();
	public function showMessage($msg){ $this->messages[] = ($msg);}
	public function showError($msg){ $this->errors[] = ($msg);}
	
}

trait Plugin_list {
	
	protected $plugins = array();
	
	
    public function loadPlugins($entidad){
		
		PluginManager::RegisterForm($this);
		
		//añadimos los plugins registrados
		$plugin_files = EntityManager::GetPluginFiles($entidad);
		foreach ($plugin_files as $fichero){
			require_once APP_ROOT."/".$fichero['fichero'];
		}	
	}

	public function addPlugin($plugin){
		$plugin->setForm($this);
		$this->plugins[] = $plugin;
	}
	
	
	//HOOKS
	protected function postUpdate($item, $datos){		
		foreach ($this->plugins as $plugin){
			$plugin->postUpdate($item, $datos);
		}
	}
	protected function setDefaults($item, &$datos){		
		foreach ($this->plugins as $plugin){
			$plugin->setDefaults($item, $datos);
		}
	}
	protected function postInsert($item, $datos){
		foreach ($this->plugins as $plugin){
			$plugin->postInsert($item, $datos);
		}
	}
	protected function preDuplicate($item, &$datos){
		foreach ($this->plugins as $plugin){
			$plugin->preDuplicate($item, $datos);
		}
	}
	protected function postDuplicate($item, $new_item){
		foreach ($this->plugins as $plugin){
			$plugin->postDuplicate($item, $new_item);
		}			
	}
	public function customContent($item, $section){
		$html = "";
		foreach ($this->plugins as $plugin){
			$html .= $plugin->customContent($item, $section );
		}
		return $html;		
	}
	protected function preRenderform($item, &$datos){
		foreach ($this->plugins as $plugin){
			$plugin->preRenderform($item, $datos);
		}	
	}
	protected function onCustomButton($operation, $item, $datos){
		foreach ($this->plugins as $plugin){
			$plugin->onCustomButton($operation, $item, $datos);
		}	
	}
	protected function postUploadFile($file){
		foreach ($this->plugins as $plugin){
			$plugin->postUploadFile($file);
		}	
	}
	protected function preDelete($item){
		foreach ($this->plugins as $plugin){
			$plugin->preDelete($item);
		}	
	}
	protected function postDelete($item){
		foreach ($this->plugins as $plugin){
			$plugin->postDelete($item);
		}	
	}
	
	protected function postTransition($item, $trans){
		foreach ($this->plugins as $plugin){
			$plugin->postTransition($item, $trans);
		}	
	}
	
}


//un truco para invocar a plugins cuando no están en el contexto de un formulario
class plugin_collector {
	use Messages;
	use Plugin_list;

}