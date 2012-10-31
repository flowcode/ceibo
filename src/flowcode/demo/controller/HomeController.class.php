<?php

namespace flowcode\demo\controller;

use flowcode\smooth\mvc\Controller;
use flowcode\smooth\mvc\HttpRequest;

/**
 * Description of HomeController
 *
 * @author juanma
 */
class HomeController extends Controller {

    public function __construct() {
        $this->setIsSecure(FALSE);
    }
    
    public function welcomeAction(HttpRequest $httpRequest){
        return "ok";
    }

}

?>
