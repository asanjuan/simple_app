<?php


class BL_Ventas {
    
    public static function getMapping_Quote2Order(){
        $map = new Mapping();
        
        $map->addMapping('titulo', 'titulo');
        $map->addMapping('total_neto', 'total_neto');
        $map->addMapping('importe_total', 'importe_total');
        $map->addMapping('id_cuenta', 'id_cuenta');
        $map->addMapping('notas', 'notas');
        $map->addMapping('id', 'id_oferta');
        $map->addMapping('total_impuestos', 'total_impuestos');
        $map->addMapping('id_empresa', 'id_empresa');
        $map->addMapping('id_forma_pago', 'id_forma_pago');
        $map->addMapping('id_medio_pago', 'id_medio_pago');
        
        return $map;

    }
    public static function getMapping_QuoteLine2OrderLine(){
        $map = new Mapping();
        
        $map->addMapping('cantidad');
        $map->addMapping('Concepto');
        $map->addMapping('descuento');
        $map->addMapping('id_producto');
        $map->addMapping('impuesto');
        $map->addMapping('impuestos');
        $map->addMapping('orden');
        $map->addMapping('precio');
        $map->addMapping('status');
        $map->addMapping('total_impuestos');
        $map->addMapping('total_linea');
        $map->addMapping('total_neto');

        
        return $map;

    }
    
    public static function getMapping_OrderLine2InvoiceLine(){
        $map = new Mapping();
        
        $map->addMapping('cantidad');
        $map->addMapping('Concepto');
        $map->addMapping('descuento');
        $map->addMapping('id_producto');
        $map->addMapping('impuesto');
        $map->addMapping('impuestos');
        $map->addMapping('orden');
        $map->addMapping('precio');
        $map->addMapping('status');
        $map->addMapping('total_impuestos');
        $map->addMapping('total_linea');
        $map->addMapping('total_neto');

        
        return $map;

    }
    
    public static function getMapping_Order2Invoice(){
        
        $map = new Mapping();
        
        $map->addMapping('titulo', 'titulo');
        $map->addMapping('total_neto', 'total_neto');
        $map->addMapping('importe_total', 'importe_total');
        $map->addMapping('id_cuenta', 'id_cuenta');
        $map->addMapping('notas', 'notas');
        $map->addMapping('id', 'id_pedido');
        $map->addMapping('total_impuestos', 'total_impuestos');
        $map->addMapping('id_empresa', 'id_empresa');
        $map->addMapping('id_forma_pago', 'id_forma_pago');
        $map->addMapping('id_medio_pago', 'id_medio_pago');
        
        return $map;

    }
    
    public static function getMapping_QuotePayment2OrderPayment(){
        $map = new Mapping();
        
        $map->addMapping('orden');
        $map->addMapping('concepto');
        $map->addMapping('pct');

        return $map;

    }
    
    public static function Confirmar_Oferta($id_oferta){
        
        $map = BL_Ventas::getMapping_Quote2Order();
        $map_linea = BL_Ventas::getMapping_QuoteLine2OrderLine();
        $map_hitos = self::getMapping_QuotePayment2OrderPayment();
        
        $row = dbgetbyid("ventas_ofertas",$id_oferta);
        
        $pedido = $map->cloneRecord($row);
        $pedido['codigo'] = nextSequence("ventas_pedidos",date("Y"),$pedido['id_empresa']);
        
        $hoy = new DateTime();
	    $pedido['fecha_pedido'] = $hoy->format('Y-m-d');
        //$hoy->add(new DateInterval('P30D'));
        //$datos['valido_hasta'] = $hoy->format('Y-m-d');
        
        $pedido['status']= 1;
        
        
        $id_pedido = dbinsert("ventas_pedidos",$pedido);
        
        $lineas = query("Select * from ventas_ofertas_lineas where id_oferta = '$id_oferta'");
        
        foreach ($lineas as $linea){
            
            $new_record = $map_linea->cloneRecord($linea);
            $new_record['id_pedido'] = $id_pedido;
            $new_record['status']= 1;
            
            dbinsert("ventas_pedidos_lineas",$new_record);
            
        }
        
        $lineas = query("Select * from ventas_hito_oferta where id_oferta = '$id_oferta' order by orden");
        $fecha_hito = $hoy;
        foreach ($lineas as $linea){
            
            $new_record = $map_hitos->cloneRecord($linea);
            $new_record['id_pedido'] = $id_pedido;
            $new_record['fecha'] = $fecha_hito->format('Y-m-d');
            $new_record['importe'] = ($pedido['total_neto']*$new_record['pct'])/100;
            $new_record['estado']='P';
            $new_record['status']= 1;
             $new_record['id_oferta']= $id_oferta;
             
            //$new_record['status']= 1;
            
            dbinsert("ventas_hito_pedido",$new_record);
            $fecha_hito->add(new DateInterval('P30D')); // añadimos un periodo arbitrario de 1 mes para distribuir los hitos en varios meses por defecto
            
        }
    }
    
    
    public static function Facturar_Pedido($id_pedido){
        
        $map = BL_Ventas::getMapping_Order2Invoice();
        $map_linea = BL_Ventas::getMapping_OrderLine2InvoiceLine();
        
        $row = dbgetbyid("ventas_pedidos",$id_pedido);
        
        $factura = $map->cloneRecord($row);
        $factura['codigo'] = nextSequence("ventas_facturas",date("Y"),$factura['id_empresa']);
        
        $hoy = new DateTime();
	    $factura['fecha_factura'] = $hoy->format('Y-m-d');
        $hoy->add(new DateInterval('P30D'));
        $factura['vencimiento'] = $hoy->format('Y-m-d');
        
        $factura['status']= 1;
        
        SystemLog::info("factura", json_encode($factura));
        $id_factura = dbinsert("ventas_facturas",$factura);
        
        $lineas = query("Select * from ventas_pedidos_lineas where id_pedido = '$id_pedido'");
        
        foreach ($lineas as $linea){
            
            $new_record = $map_linea->cloneRecord($linea);
            $new_record['id_factura'] = $id_factura;
            $new_record['status']= 1;
            SystemLog::info("ventas_facturas_lineas", json_encode($new_record));
            dbinsert("ventas_facturas_lineas",$new_record);
            
        }
        
    }
}

