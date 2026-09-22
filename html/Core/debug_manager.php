<?php
/*
CLASE PARA MOSTRAR VARIABLES DE DEPURACIÓN DE PLUGINS Y CODIGO DE SERVIDOR
*/
class DebugLog {
    
    private static array  $messages = [];

    public static function count() {
        return count(self::$messages);
    }
    public static function get(){
        return self::$messages;
    }
    public static function trace(string $mensaje, string $details = ""): void {
        self::registrar('info', $mensaje,$details);
    }

    public static function error(string $mensaje, string $details = ""): void {
        self::registrar('error', $mensaje ,$details);
    }
    public static function warning(string $mensaje, string $details = ""): void {
        self::registrar('warning', $mensaje ,$details);
    }

    private static function registrar(string $nivel, string $mensaje,  string $details): void {
        
        // Formato final de la línea de log
        $log = [
            "type" => $nivel,
            "description" => $mensaje
           
        ];
        self::$messages [] =  $log;
    }
}



