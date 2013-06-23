<?php

namespace flowcode\wing\mvc;

use flowcode\wing\mvc\Config;
use flowcode\wing\mvc\Controller;
use flowcode\wing\mvc\exception\ViewException;

/**
 * Description of View
 *
 * @author juanma
 */
class View implements IView {

    protected $decorators;
    protected $viewData;
    protected $viewName;
    protected $moduleName;
    protected $controllerName;
    protected $specificMaster;

    function __construct($viewData, $viewName, Controller $controller, $master = null) {
        $this->viewName = $viewName;
        $this->moduleName = $controller->getModule();
        $this->controllerName = $controller->getName();
        $this->viewData = $viewData;
        $this->specificMaster = $master;
        $this->decorators = array();
    }

    public static function getPlainView(Controller $controller, $viewName, $viewData) {
        return new PlainView($viewData, $viewName, $controller);
    }

    public static function getViewWithSpecificMaster(Controller $controller, $viewName, $viewData, $master) {
        return new View($viewData, $viewName, $controller, $master);
    }

    public static function getControllerView(Controller $controller, $viewName, $viewData) {
        return new View($viewData, $viewName, $controller, null);
    }

    public function render() {
        $settedMaster = null;
        $modulepath = Config::get("modulepath", "all");

        $viewData = $this->viewData;
        /* render view */
        $viewfile = __DIR__ . "/../../../../" . $modulepath . $this->getViewName() . ".view.php";
        if (file_exists($viewfile)) {
            ob_start();
            require_once $viewfile;
            $content = ob_get_contents();
            ob_end_clean();
        } else {
            throw new ViewException($viewfile);
        }

        /* not method masterview */
        if (is_null($this->specificMaster)) {
            $controllers = Config::getByModule($this->getModuleName(), "masterview", "controller");
            if (!is_null($controllers) && isset($controllers[$this->getControllerName()])) {
                /* check none masterpage */
                if ($controllers[$this->getControllerName()] != "none") {
                    $settedMaster = $controllers[$this->getControllerName()];
                }
            } else {

                /* not controller masterview */
                $moduleView = Config::getByModule($this->getModuleName(), "masterview", "module");
                if (!is_null($moduleView) && $moduleView != "none") {
                    $settedMaster = $moduleView;
                }
            }
        } else {
            $settedMaster = $this->specificMaster;
        }
        if (!is_null($settedMaster)) {
            $masterfile = __DIR__ . "/../../../../" . $modulepath . $settedMaster . ".view.php";
            if (file_exists($masterfile)) {
                require_once $masterfile;
            } else {
                throw new ViewException($settedMaster);
            }
        }  else {
            echo $content;
        }
    }

    public function getDecorators() {
        return $this->decorators;
    }

    public function setDecorators($decorators) {
        $this->decorators = $decorators;
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

    public function getModuleName() {
        return $this->moduleName;
    }

    public function setModuleName($moduleName) {
        $this->moduleName = $moduleName;
    }

    public function getControllerName() {
        return $this->controllerName;
    }

    public function setControllerName($controllerName) {
        $this->controllerName = $controllerName;
    }

}

?>
