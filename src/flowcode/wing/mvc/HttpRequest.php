<?php

namespace flowcode\wing\mvc;

/**
 * 
 */
class HttpRequest {

    private $requestedUrl;
    private $controller;
    private $action;
    private $params;

    public function __construct() {
        $this->params = array();
    }
    
    public function setRequestedUrl($requestedUrl){
        $this->requestedUrl = $requestedUrl;
    }

    public function getControllerClass() {
        return ucwords($this->controller) . "Controller";
    }

    public function getControllerName() {
        return $this->controller;
    }


    public function setControllerName($controllerName) {
        $this->controller = $controllerName;
    }
    
    public function getAction() {
        return $this->action;
    }

    public function setAction($actionName) {
        $this->action = $actionName;
    }

    /**
     * Retorna los parametros del request.
     * @return type 
     */
    public function getParams() {
        return $this->params;
    }

    public function setParams($params) {
        $this->params = $params;
    }

    /**
     * Retorna el valor del parametro.
     * Si no existe retorna NULL.
     * 
     * @param String $parameter
     * @return String value. 
     */
    public function getParameter($parameter) {
        $value = NULL;
        if ($parameter != NULL) {
            for ($index = 0; $index < (count($this->params) - 1); $index++) {
                if ($this->params[$index] == $parameter) {
                    $value = $this->params[$index + 1];
                    break;
                }
            }
        }
        if (is_null($value)) {
            if (isset($_POST[$parameter])) {
                $value = $_POST[$parameter];
            }
        }
        if (is_null($value)) {
            if (isset($_GET[$parameter])) {
                $value = $_GET[$parameter];
            }
        }
        if (is_string($value)) {
            $value = urldecode($value);
        }
        return $value;
    }

    public function getRequestedUrl() {
        return $this->requestedUrl;
    }

}

?>
