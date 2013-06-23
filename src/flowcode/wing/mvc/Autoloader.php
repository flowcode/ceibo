<?php

/**
 * Autoloader para cargar clases. 
 */
class ClassAutoloader {

    public function __construct() {
        spl_autoload_register(array($this, 'loader'));
    }

    private function loader($className) {
        
        $params = explode('\\', $className);
        $filename = __DIR__ . '/../..';
        
        $count = (count($params)-1);
        for ($i = 1; $i <= $count; $i++) {
            $filename .= '/' . $params[$i];
        }
        $filename .= '.php';
        
        require_once $filename;
    }

}

$autoloader = new ClassAutoloader();
?>
