<?php 

PluginManager::RegisterPlugin(new my_new_plugin());

class my_new_plugin extends PluginInterface {
	
	protected $data; //use it at will between related events
	
	public function postUpdate($item, $datos){ }
	public function postInsert($item, $datos){ 
	    $datos['codigo'] = nextSequence("ventas_pedidos");
	    $datos['id']= $item;
	    dbupdate("ventas_pedidos", $datos);
	}
	public function preDuplicate($item, &$datos){ }
	public function postDuplicate($item, $new_item){ 
	    
	    //duplicamos Columnas
		$data = query("SELECT * FROM ventas_pedidos_lineas where id_pedido=".quote($item));
		foreach ($data as $col) {
		    // code...
		    $col['id_pedido'] = $new_item;
		    $col['id']='';
		    dbinsert('ventas_pedidos_lineas', $col);
		}
	    
	}
	
	public function customContent($item){ 	}
	
	public function setDefaultValues(&$datos){  }
	
	public function preRenderform($item, &$datos){ 
		
	}
	public function onCustomButton($operation, $item, $datos){ 
		
	}
	public function postUploadFile($file){ $this->showMessage("postUploadFile");}

}
