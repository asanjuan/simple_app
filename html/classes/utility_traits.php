<?php

trait Messages {
	
	protected $messages = array();
	public function showMessage($msg){ $this->messages[] = ($msg);}
	
}

trait Plugin_list {
	
	protected $plugins = array();
	

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
	protected function customContent($item){
		$html = "";
		foreach ($this->plugins as $plugin){
			$html .= $plugin->customContent($item);
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