<?php

class SystemLog {
    
    public static function info(string $mensaje, string $details = ""): void {
        self::registrar('info', $mensaje,$details);
    }

    public static function error(string $mensaje, string $details = ""): void {
        self::registrar('error', $mensaje ,$details);
    }
    public static function warning(string $mensaje, string $details = ""): void {
        self::registrar('warning', $mensaje ,$details);
    }

    private static function registrar(string $nivel, string $mensaje,  string $details): void {
        // Ignoramos los argumentos y limitamos el rastro a los 2 últimos pasos
        // Paso 0: Es este propio método 'registrar'
        // Paso 1: Es el método público ('info' o 'error')
        // Paso 2: Es el archivo externo que llamó a Log::info() o Log::error()
        $rastro = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        
        // Extraemos los datos del archivo origen (Paso 2)
        $archivoOrigen = isset($rastro[1]['file']) ? basename($rastro[1]['file']) : 'Desconocido';
        $lineaOrigen = isset($rastro[1]['line']) ? $rastro[1]['line'] : 0;

        // Formato final de la línea de log
        $log = [
            "type" => $nivel,
            "origin" => "$archivoOrigen:$lineaOrigen",
            "description" => $mensaje,
            "details" => $details
        ];
        dbinsert("app_log", $log);
    }
}
