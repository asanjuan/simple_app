<?php

define('APP_ROOT', __DIR__);
define('USE_CLASSMAP_CACHE_AUTOLOAD', true);

$classMap = [];
loadClassMap(APP_ROOT . '/Core');

function loadClassMap($dir)
{
    global $classMap ;
    $cache_file = APP_ROOT.'/classmap.json';

    if (USE_CLASSMAP_CACHE_AUTOLOAD && is_file($cache_file)){

        $contents = file_get_contents($cache_file);
        $classMap = json_decode($contents, true);
        
    }else {

        $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator( $dir)
        );

        foreach ($iterator as $file)
        {
            if (!$file->isFile() || $file->getExtension() !== 'php')
            {
                continue;
            }

            $content = file_get_contents($file->getPathname());

            if (preg_match_all(
                '/(?:class|interface|trait)\s+([a-zA-Z_][a-zA-Z0-9_]*)/',
                $content,
                $matches
            )) {
                foreach ($matches[1] as $className) {
                    $classMap[$className] = str_replace(APP_ROOT, "", $file->getPathname());
                }
            }
        }
        file_put_contents($cache_file, json_encode($classMap));
    
    }
}

//autoload de clases
spl_autoload_register(function ($class) use ($classMap) {

    if (isset($classMap[$class])) {
        require_once APP_ROOT . $classMap[$class];
    }
});

require_once APP_ROOT . '/config.php';
require_once APP_ROOT . '/utilities.php';
require_once APP_ROOT . '/database.php';

