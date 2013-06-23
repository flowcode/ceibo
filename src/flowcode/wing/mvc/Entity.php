<?php

namespace flowcode\wing\mvc;

/**
 *
 * @author juanma
 */
class Entity {

    protected $id;

    public function __construct() {
        $this->id = NULL;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id = NULL) {
        if (!empty($id) && !is_null($id))
            $this->id = $id;
    }

    /**
     * Get the array representation of entity.
     * @return array values 
     */
    public function toArray() {
        $arr = array();

        if (is_array($this)) {
            foreach ($this as $value) {
                $arr[] = $this->toArray($value);
            }
        }
        if (is_object($this)) {
            $ardef = array();
            $arObj = (array) $this;
            foreach ($arObj as $key => $value) {
                $attribute = str_replace(get_class($this), "", $key);
                if (is_object($value) || is_array($value)) {
                    $value = $this->toArray($value);
                }
                $arr[$attribute] = $value;
            }
        }
        return $arr;
    }

    public function toSimpleArray() {
        $arr = array();
        $arObj = (array) $this;
        foreach ($arObj as $key => $value) {
            $attribute = str_replace(get_class($this), "", $key);
            if (!is_object($value) && !is_array($value)) {
                $arr[$attribute] = $value;
            }
        }
        return $arr;
    }

}

?>
