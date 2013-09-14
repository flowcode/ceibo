<?php

namespace flowcode\ceibo\domain;

/**
 * Description of Property
 *
 * @author juanma
 */
class Property {

    private $name;
    private $column;
    private $type;

    /**
     * Construct a property instance.
     * @param type $name
     * @param type $column
     * @param type $type
     */
    function __construct($name, $column = null, $type = null) {
        $this->name = $name;
        if (!is_null($column))
            $this->column = $column;
        if (!is_null($type))
            $this->type = $type;
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

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

    /**
     * Return true if property is numeric a type.
     * @return boolean
     */
    public function isNumeric() {
        $IsNumeric = false;
        if ($this->type == "integer" || $this->type == "bigint") {
            $IsNumeric = true;
        }
        return $IsNumeric;
    }

}

?>
