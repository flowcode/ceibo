<?php

namespace flowcode\wing\mvc;

/**
 * Description of Router.
 *
 * @author juanma
 */
class Router {

    
    public static function get($section, $param) {
        
        $framework_base = dirname ( __FILE__ )."/../../";
        
        // Parse with sections
        require $framework_base . "common/config/routing.php";
        if (isset($setup[$section][$param])) {
            return $setup[$section][$param];
        } else {
            return NULL;
        }
        
    }

}

