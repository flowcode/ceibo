<?php

namespace flowcode\demo\domain;

/**
 * Description of Weapon
 *
 * @author JMA <jaguero@flowcode.com.ar>
 */
class Weapon {

    private $id;
    private $name;

    function __construct($id = NULL, $name = NULL) {
        if (!is_null($id))
            $this->id = $id;
        if (!is_null($name))
            $this->name = $name;
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

}

?>
