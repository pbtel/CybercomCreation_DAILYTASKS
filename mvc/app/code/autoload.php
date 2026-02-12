<?php 
    spl_autoload_register(function($class){
        $root = __DIR__ . '\\';
        $path = str_replace('_','/',$class);
        $path = sprintf("%s.php",$root .'/'. $path);
        require_once $path;

    })
?>