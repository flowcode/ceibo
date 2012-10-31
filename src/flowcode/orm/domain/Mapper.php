<?php

namespace flowcode\orm\domain;

/**
 * Description of Mapper
 *
 * @author juanma
 */
class Mapper {

    private $name;
    private $table;
    private $class;
    private $propertys;
    private $relations;
    private $mapping;

    public function __construct() {
        $this->relations = array();
        $this->propertys = array();
    }

    public function getNameForColumn($column) {
        $name = NULL;
        foreach ($this->propertys as $prop) {
            if ($prop->getColumn() == $column) {
                $name = $prop->getName();
                break;
            }
        }
        return $name;
    }

    public function getTable() {
        return $this->table;
    }

    public function setTable($table) {
        $this->table = $table;
    }

    public function getClass() {
        return $this->class;
    }

    public function setClass($class) {
        $this->class = $class;
    }

    public function getRelations() {
        return $this->relations;
    }

    public function setRelations($relations) {
        $this->relations = $relations;
    }

    public function getPropertys() {
        return $this->propertys;
    }

    public function setPropertys($propertys) {
        $this->propertys = $propertys;
    }

    public function getMapping() {
        return $this->mapping;
    }

    public function setMapping($mapping) {
        $this->mapping = $mapping;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function createObject($raw) {
        $entity = new $this->class;
        foreach ($raw as $key => $value) {
            if ($this->getNameForColumn($key) != NULL) {
                $method = "set" . $this->getNameForColumn($key);
                $entity->$method($value);
            }
        }
        return $entity;
    }

}

?>
