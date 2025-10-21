<?php

class PluginManager {
	// Variable estática para almacenar la única instancia de la clase
    private static $form;
	
    
	public static function RegisterPlugin($obj_plugin){
		self::$form->addPlugin($obj_plugin);
	}
	public static function RegisterForm($obj_form){
		
		self::$form = $obj_form;
	}
	
}

// clase controller que contenga toda la lógica común de un controlador de entidad
// representa un mantenimiento para ALTA - BAJA - MODIFICACIÓN Y LISTADO DE MANTENIMIENTO de una entidad de base de datos genérica
class PluginInterface {
	public $form ;
	public function setForm($form){ $this->form = $form; }
	public function showMessage($msg){ $this->form->showMessage($msg);}
	
	public function setDefaults($item, $datos){}
	public function postUpdate($item, $datos){}
	public function postInsert($item, $datos){}
	public function preDuplicate($item, &$datos){}
	public function postDuplicate($item, $new_item){}
	public function customContent($item){}
	public function setDefaultValues(&$datos){}
	public function preRenderform($item, &$datos){}
	public function onCustomButton($operation, $item, $datos){}
	public function postUploadFile($file){ }
	public function preDelete($item){}
	public function postDelete($item){}
	public function postTransition($item, $trans){}
}

    