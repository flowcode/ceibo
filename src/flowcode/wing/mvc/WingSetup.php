<?php

namespace flowcode\wing\mvc;

/**
 * Description of WingSetup}
 *
 * @author juanma
 */
class WingSetup {

    protected $scanneableControllers;
    protected $dirs;
    protected $defaultController;
    protected $defaultMethod;
    protected $errorMethod;
    protected $loginController;
    protected $loginMethod;
    protected $restrictedMethod;

    function __construct() {
        $this->scanneableControllers = array();
        $this->dirs = array();
        $this->defaultController = array();
        $this->defaultMethod = array();
    }

    public function getScanneableControllers() {
        return $this->scanneableControllers;
    }

    public function setScanneableControllers($scanneableControllers) {
        $this->scanneableControllers = $scanneableControllers;
    }

    public function getDefaultController() {
        return $this->defaultController;
    }

    public function getDefaultMethod() {
        return $this->defaultMethod;
    }

    public function getDirs() {
        return $this->dirs;
    }

    public function getLogDir() {
        $root = __DIR__ . "/../../../..";
        if (isset($this->dirs["log"])) {
            return $root . $this->dirs["log"];
        }
    }

    public function setDirs($dirs) {
        $this->dirs = $dirs;
    }

    public function getLoginController() {
        return $this->loginController;
    }

    public function setLoginController($loginController) {
        $this->loginController = $loginController;
    }

    public function getLoginMethod() {
        return $this->loginMethod;
    }

    public function setLoginMethod($loginMethod) {
        $this->loginMethod = $loginMethod;
    }

    public function getRestrictedMethod() {
        return $this->restrictedMethod;
    }

    public function setRestrictedMethod($restrictedMethod) {
        $this->restrictedMethod = $restrictedMethod;
    }

    public function getErrorMethod() {
        return $this->errorMethod;
    }

    public function setErrorMethod($errorMethod) {
        $this->errorMethod = $errorMethod;
    }

}

?>
