<?php 

PluginManager::RegisterPlugin(new my_new_plugin());

class my_new_plugin extends PluginInterface {
	
	protected $data; //use it at will between related events
	protected $id_oferta_padre;
	
	public function postUpdate($item, $datos){
	   
	   $this->procesarLinea($item, $datos);
	   $this->UpdateOferta( $datos['id_oferta']);
	   
	}
	
	public function UpdateOferta($id_oferta){
	    
	    $totales = subtotals("ventas_ofertas_lineas", ["total_neto","total_impuestos"], "id_oferta", $id_oferta);
	    $oferta=[];
	    //trace("totales " . $id_oferta);
	    //dump($totales);
	    $oferta['id']= $id_oferta;
	    $oferta['total_neto']= round($totales["total_neto"],2);
	    $oferta['total_impuestos']= round($totales["total_impuestos"],2);
	    $oferta['importe_total']= round($totales["total_neto"],2) + round($totales["total_impuestos"],2);
	    
	    //dump($oferta);
	    dbupdate("ventas_ofertas",$oferta);
	    
	}
	
	public function procesarLinea($item, $datos){
	    
	    $id_producto = $datos['id_producto'];
	    $cantidad = $datos['cantidad'];
	    $precio_unit = floatval($datos['precio']);
	    $descuento = floatval($datos['descuento']);
	    $pct_impuesto = floatval($datos['impuesto']);
	    
	    
	    $producto = dbgetbyid("productos",$id_producto);
	    //dump($producto);
	    
	    if ($producto!=null){
	        if($precio_unit == 0) $precio_unit =  floatval($producto['precio']);
	        
	        $id_impuesto = $producto['id_impuesto'];
	        if ($id_impuesto != ""){
	            $impuesto = dbgetbyid("impuestos",$id_impuesto);
	            //dump($impuesto);
	            $pct_impuesto = $impuesto['porcentaje'];
	        }
	    }
	    $total_neto = $precio_unit * $cantidad * (1.0- $descuento/100.0 );
	    $total_impuestos = ($pct_impuesto/100.0) * $total_neto;
	    
	    $r = [];
	    $r['id'] = $item;
	    $r['impuesto'] = $pct_impuesto;
	    $r['precio'] = $precio_unit;
	    $r['total_neto'] = $total_neto;
	    $r['total_impuestos'] = $total_impuestos;
	    $r['total_linea'] = $total_neto + $total_impuestos;
	    //dump($r);
	    
	    dbupdate("ventas_ofertas_lineas", $r);
	    
	    
	    
	}
	
	public function postInsert($item, $datos){
	    
        $this->procesarLinea($item, $datos);
	    $this->UpdateOferta($datos['id_oferta']);
	    
	}
	
	public function preDelete($item){
	    
	    $record = dbgetbyid("ventas_ofertas_lineas",$item);
	    $this->id_oferta_padre = $record['id_oferta'];
	}
	public function postDelete($item){
	    
	    $this->UpdateOferta($this->id_oferta_padre);
	}
	
	public function preDuplicate($item, &$datos){ }
	public function postDuplicate($item, $new_item){ }
	
	public function customContent($item){ 	}
	
	public function setDefaultValues(&$datos){  }
	
	public function preRenderform($item, &$datos){ 
	    //número de orden en el alta
	    if ($item == "" || $item <0){
	        $aux = query1("select coalesce(max(orden),0)+1 as orden from ventas_ofertas_lineas where id_oferta =  ".quote($datos['id_oferta']));
	        $datos['orden'] =$aux['orden'];
	        
	    }else { //establece el estado del registro como bloqueado o activo en función de la cabecera
	        $id_oferta = $datos['id_oferta'];
	        $ofe = dbgetbyid("ventas_ofertas",$id_oferta);
	        $datos['status'] = $ofe['status'];
	    }
		
	}
	public function onCustomButton($operation, $item, $datos){ 
		
	}
	public function postUploadFile($file){ $this->showMessage("postUploadFile");}

}



