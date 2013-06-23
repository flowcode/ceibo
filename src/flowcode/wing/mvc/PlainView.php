<?php

namespace flowcode\wing\mvc;

use flowcode\wing\mvc\Controller;
use flowcode\wing\mvc\IView;

/**
 * Description of View
 *
 * @author juanma
 */
class PlainView implements IView {

    protected $viewData;


    function __construct($viewData) {
        $this->viewData = $viewData;
    }

    public function render() {
        echo $this->viewData["data"];
    }

}

?>
