<?php 


PluginManager::RegisterPlugin(new ventas_facturas_plugin());	    

class ventas_facturas_plugin extends PluginInterface {
	
	protected $data; //use it at will between related events
	
	public function postUpdate($item, $datos){ 
	    
	    
	}
	public function postInsert($item, $datos){ 
	    $datos['codigo'] = nextSequence("ventas_facturas",date("Y"),$datos['id_empresa']);
	    $datos['estado'] = 0; //borrador 
	    
	    $hoy = new DateTime();
	    $datos['fecha_factura'] = $hoy->format('Y-m-d');
        $hoy->add(new DateInterval('P30D'));
        $datos['vencimiento'] = $hoy->format('Y-m-d');
        
        
	    $datos['id']= $item;
	    dbupdate("ventas_facturas", $datos);
	}
	public function preDuplicate($item, &$datos){ 
	    $datos['codigo'] = null;
	    $datos['status'] = 1;
	}
	public function postDuplicate($item, $new_item){ 
	    
	    //duplicamos lineas de oferta
		$data = query("SELECT * FROM ventas_facturas_lineas where id_factura=".quote($item));
		foreach ($data as $col) {
		    // code...
		    $col['id_factura'] = $new_item;
		    $col['id']='';
		    dbinsert('ventas_facturas_lineas', $col);
		}
		
	}
	
	public function postTransition($item, $trans){
	    
	    
	    
	}
	public function customContent($item, $section){ 

	}
	
	public function setDefaultValues(&$datos){  }
	
	public function preRenderform($item, &$datos){ 
		
	}
	public function onCustomButton($operation, $item, $datos){ 
		
	}
	public function postUploadFile($file){ }

}
