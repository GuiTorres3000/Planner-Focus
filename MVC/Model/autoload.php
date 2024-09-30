<?php
include_once(__DIR__ . '/model.trait.php');

    function autoLoad($className){
        
        $file = __DIR__ . '/Classes/' . strtolower($className) . '.class.php';

        if(file_exists($file)){
            include_once $file;
            return;
        }    
   

    }
    spl_autoload_register('autoLoad');

?>