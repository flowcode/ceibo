<?php

namespace flowcode\demo\domain;

/**
 * Description of Ovni
 *
 * @author JMA <jaguero@flowcode.com.ar>
 */
class Ovni {

    private $id;
    private $name;
    private $weapons;

    public function __construct() {
        $this->weapons = array();
    }
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getWeapons() {
        return $this->weapons;
    }

    public function setWeapons($weapons) {
        $this->weapons = $weapons;
    }


    
}

?>
