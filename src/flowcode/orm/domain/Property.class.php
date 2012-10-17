<?php

namespace flowcode\orm\domain;

/**
 * Description of Property
 *
 * @author juanma
 */
class Property {

    private $name;
    private $column;

    function __construct($name, $column) {
        $this->name = $name;
        $this->column = $column;
    }

        public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getColumn() {
        return $this->column;
    }

    public function setColumn($column) {
        $this->column = $column;
    }

}

?>
