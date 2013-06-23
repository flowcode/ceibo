<?php

namespace flowcode\wing\mvc;

use flowcode\wing\config\Setup;
use flowcode\wing\mvc\HttpRequest;
use flowcode\wing\mvc\HttpRequestBuilder;
use flowcode\wing\utils\KLogger;

class Kernel {

    public function __construct() {
        
    }

    public function init($mode) {
        session_start();

        if ('prod' == $mode) {
            register_shutdown_function(array($this, 'shutdown'));
        }
    }

    public function handleRequest($requestedUrl) {
        require_once (__DIR__ . '/Autoloader.php');

        $setup = new Setup();
        $controllersToScan = $setup->getScanneableControllers();

        $request = $this->getRequest($requestedUrl);

        // scan controller
        $enabledController = FALSE;
        $moduleName = null;
        foreach ($controllersToScan as $module => $controllerNamespace) {
            $class = $controllerNamespace . $request->getControllerClass();

            if ($this->validClass($class)) {
                $enabledController = TRUE;
                $moduleName = $module;
                break;
            }
        }


        if ($enabledController) {
            $controller = new $class();
            $controller->setModule($moduleName);
            $controller->setName($request->getControllerName());
        } else {

            $class = $setup->getDefaultController();
            $request->setAction($setup->getDefaultMethod());
            $controller = new $class();
        }

        // seguridad a nivel controller
        if ($controller->isSecure()) {

            if (!isset($_SESSION['user']['username'])) {

                // Si no esta atenticado, lo llevo a la pantalla de autenticacion.
                $request = new HttpRequest("");
                $request->setAction($setup->getLoginMethod());
                $class = $setup->getLoginController();
                $controller = new $class();
                $controller->setModule($moduleName);
            } else {

                // Si esta atenticado, verifico que tenga un rol valido para el controller.
                if (!$controller->canAccess($_SESSION['user'])) {
                    $request = new HttpRequest("");
                    $request->setAction($setup->getRestrictedMethod());
                    $request->setControllerName("usuario");
                    $class = $setup->getLoginController();
                    $controller = new $class();
                }
            }
        }

        $method = $request->getAction();
        $this->dispatch($controller, $method, $request);
    }

    private function dispatch(Controller $controller, $method, HttpRequest $request) {
        $view = $controller->$method($request);
        $view->render();
    }

    public function getRequest($requestedUrl) {
        $request = HttpRequestBuilder::buildFromRequestUrl($requestedUrl);
        return $request;
    }

    public function shutdown() {
        if (($error = error_get_last())) {
            ob_clean();
            $setup = new Setup();

            /* log error */
            KLogger::instance($setup->getLogDir())->logCrit($error["message"] . " on " . $error["file"] ." at ". $error["line"]);

            $request = new HttpRequest();
            $class = $setup->getDefaultController();
            $method = $setup->getErrorMethod();
            $controller = new $class();
            $this->dispatch($controller, $method, $request);
        }
    }

    private function validClass($classname) {
        $params = explode('\\', $classname);
        $filename = __DIR__ . '/../../..';

        $count = (count($params) - 1);
        for ($i = 1; $i <= $count; $i++) {
            $filename .= '/' . $params[$i];
        }
        $filename .= '.php';
        return file_exists($filename);
    }

}

?>
