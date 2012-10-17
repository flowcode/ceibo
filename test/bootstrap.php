<?php

/**
 * @author JMA <jaguero@flowcode.com.ar>
 */
// TODO: check include path
ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . dirname(__FILE__) . '/../../../../home/juanma/NetBeansProjects/smooth-orm/${php.global.include.path}');

class ClassAutoloader {

    public function __construct() {
        spl_autoload_register(array($this, 'loader'));
    }

    private function loader($className) {
        $params = explode('\\', $className);
        $filename = __DIR__ . '/../src/flowcode/' . $params[1] . '/' . $params[2] . '/' . $params[3] . '.php';
        include $filename;
    }

}

$autoloader = new ClassAutoloader();
?>
