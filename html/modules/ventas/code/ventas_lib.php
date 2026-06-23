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
    
    public static function Confirmar_Oferta($id_oferta){
        
        $map = BL_Ventas::getMapping_Quote2Order();
        $map_linea = BL_Ventas::getMapping_QuoteLine2OrderLine();
        
        $row = dbgetbyid("ventas_ofertas",$id_oferta);
        
        $pedido = $map->cloneRecord($row);
        $pedido['codigo'] = nextSequence("ventas_pedidos");
        $pedido['fecha_pedido']= date("Y-m-d");
        $pedido['status']= 1;
        
        
        $id_pedido = dbinsert("ventas_pedidos",$pedido);
        
        $lineas = query("Select * from ventas_ofertas_lineas where id_oferta = '$id_oferta'");
        
        foreach ($lineas as $linea){
            
            $new_record = $map_linea->cloneRecord($linea);
            $new_record['id_pedido'] = $id_pedido;
            $new_record['status']= 1;
            
            dbinsert("ventas_pedidos_lineas",$new_record);
            
        }
        
        
        
    }
    
}

