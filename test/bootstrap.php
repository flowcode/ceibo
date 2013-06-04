<?php

/**
 * @author JMA <jaguero@flowcode.com.ar>
 */

class ClassAutoloader {

    public function __construct() {
        spl_autoload_register(array($this, 'loader'));
    }

    private function loader($className) {
        $params = explode('\\', $className);
        $filename = __DIR__ . '/../src';
        foreach ($params as $dir) {
            $filename .= "/" . $dir;
        }
        $filename .= ".php";

        include $filename;
    }

}

$autoloader = new ClassAutoloader();
?>
