<?php

namespace flowcode\wing\mvc;

use flowcode\wing\mvc\Config;
use flowcode\wing\mvc\exception\ViewException;

/**
 * Description of View
 *
 * @author juanma
 */
class BareView implements IView {

    protected $viewData;
    protected $viewName;

    function __construct($viewData, $viewName) {
        $this->viewName = $viewName;
        $this->viewData = $viewData;
    }

    public function render() {
        $modulepath = Config::get("modulepath", "all");

        $viewData = $this->viewData;
        /* render view */
        $viewfile = __DIR__ . "/../../../../" . $modulepath . $this->getViewName() . ".view.php";
        if (file_exists($viewfile)) {
            require_once $viewfile;
        } else {
            throw new ViewException($viewfile);
        }
    }

    public function getViewData() {
        return $this->viewData;
    }

    public function setViewData($viewData) {
        $this->viewData = $viewData;
    }

    public function getViewName() {
        return $this->viewName;
    }

    public function setViewName($viewName) {
        $this->viewName = $viewName;
    }

}

?>
