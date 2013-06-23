<?php

namespace flowcode\wing\config;

use flowcode\wing\mvc\WingSetup;

/**
 * Description of Setup
 *
 * @author juanma
 */
class Setup extends WingSetup {

    function __construct() {
        parent::__construct();

        /* controllers */
        $this->scanneableControllers["front"] = "\\flowcode\\front\\controller\\";
        $this->scanneableControllers["shop"] = "\\flowcode\\shop\\controller\\";
        $this->scanneableControllers["blog"] = "\\flowcode\\blog\\controller\\";
        $this->scanneableControllers["cms"] = "\\flowcode\\cms\\controller\\";
        $this->scanneableControllers["demo"] = "\\flowcode\\demo\\controller\\";
        $this->scanneableControllers["wing"] = "\\flowcode\\wing\\controller\\";

        /* dirs */
        $this->dirs['public'] = "/public";
        $this->dirs['log'] = "/log";

        /* default controller */
//        $this->defaultController = "\\flowcode\\wing\\controller\\DefaultController";
//        $this->defaultMethod = "hello";
        $this->defaultController = "\\flowcode\\cms\\controller\\PageController";
        $this->defaultMethod = "manage";
        $this->errorMethod = "error";

        /* cms login manager */
        $this->loginController = "\\flowcode\\cms\\controller\\AdminLoginController";
        $this->loginMethod = "index";
        $this->restrictedMethod = "restricted";
    }

}

?>
