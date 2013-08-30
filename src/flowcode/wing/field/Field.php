<?php

namespace flowcode\mvc\field;

/**
 * Description of SelectField
 *
 * @author juanma
 */
class Field {

    protected $value = "";
    protected $name = "";
    protected $options = array();
    protected $attributes = array();

    public function __construct($options = NULL, $attributes = NULL) {
        if (!is_null($options))
            $this->options = $options;
        if (!is_null($attributes))
            $this->attributes = $attributes;

        if (!isset($this->options["required"]) || $this->options["required"] != FALSE) {
            $this->options["required"] = TRUE;
        }
    }

    public function addAttribute($key, $value) {
        if (isset($this->attributes[$key]))
            $this->attributes[$key] = $value;
    }

    public function getOption($key) {
        $value = NULL;
        if (isset($this->options[$key])) {
            $value = $this->options[$key];
        }
        return $value;
    }
    
    public function addOption($key, $value){
        $this->options[$key] = $value;
    }

    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

}

?>
