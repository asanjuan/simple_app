<?php 

PluginManager::RegisterPlugin(new my_new_plugin());

class my_new_plugin extends PluginInterface {
	
	protected $data; //use it at will between related events
	
	public function postUpdate($item, $datos){ }
	public function postInsert($item, $datos){ 
	    $datos['codigo'] = nextSequence("ventas_ofertas");
	    $datos['estado'] =0; //borrador 
	    
	    $hoy = new DateTime();
	    $datos['fecha_oferta'] = $hoy->format('Y-m-d');
        $hoy->add(new DateInterval('P30D'));
        $datos['valido_hasta'] = $hoy->format('Y-m-d');
        
	    $datos['id']= $item;
	    dbupdate("ventas_ofertas", $datos);
	}
	public function preDuplicate($item, &$datos){ }
	public function postDuplicate($item, $new_item){ 
	    
	    //duplicamos Columnas
		$data = query("SELECT * FROM ventas_ofertas_lineas where id_oferta=".quote($item));
		foreach ($data as $col) {
		    // code...
		    $col['id_oferta'] = $new_item;
		    $col['id']='';
		    dbinsert('ventas_ofertas_lineas', $col);
		}
	    
	}
	public function postTransition($item, $trans){
	   /* 
	    $r = array();
	    $r['id']= $item;
	    $r['notas'] = json_encode($trans);
	    dbupdate("ventas_ofertas", $r);
	    */
	    
	}
	public function customContent($item){ 	}
	
	public function setDefaultValues(&$datos){  }
	
	public function preRenderform($item, &$datos){ 
		
	}
	public function onCustomButton($operation, $item, $datos){ 
		
	}
	public function postUploadFile($file){ $this->showMessage("postUploadFile");}

}
