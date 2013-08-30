<?php

namespace flowcode\wing\controller;

use flowcode\wing\mvc\Controller;
use flowcode\wing\mvc\View;

/**
 * Description of DefaultController
 *
 * @author juanma
 */
class DefaultController extends Controller {

    function __construct() {
        $this->setIsSecure(false);
    }

    public function hello() {
        $viewData["data"] = "handled by default controller.";
        return View::getPlainView($this, "wing/view/default", $viewData);
    }

}

?>
