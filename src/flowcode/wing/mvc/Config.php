<?php

namespace flowcode\wing\mvc;

/**
 * Description of Config
 *
 * @author Juan Manuel Aguero <jaguero@flowcode.com.ar>
 */
class Config {

    public static function get($section, $param) {
        $path = "common/config/setup.php";
        $framework_base = dirname(__FILE__) . "/../../";
        require $framework_base . $path;
        if (isset($setup[$section][$param])) {
            return $setup[$section][$param];
        } else {
            return NULL;
        }
    }

    public static function getByModule($module, $section, $param = null) {
        $value = NULL;
        $path = "$module/config/setup.php";
        $framework_base = dirname(__FILE__) . "/../../";
        require $framework_base . $path;
        if (isset($setup[$section])) {
            if (!is_null($param)) {
                if (isset($setup[$section][$param])) {
                    return $setup[$section][$param];
                }
            } else {
                $value = $setup[$section];
            }
        }
        return $value;
    }

}

