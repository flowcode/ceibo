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
    private $filters;

    public function __construct() {
        $this->relations = array();
        $this->propertys = array();
        $this->filters = array();
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

    /**
     * Get a relation bby its name.
     * @param string $relationName
     * @return Relation
     */
    public function getRelation($relationName) {
        $relationInstance = null;
        if (isset($this->relations[$relationName])) {
            $relationInstance = $this->relations[$relationName];
        }
        return $relationInstance;
    }
    
    public function getProperty($propertyName) {
        $propertyInstance = null;
        if (isset($this->propertys[$propertyName])) {
            $propertyInstance = $this->propertys[$propertyName];
        }
        return $propertyInstance;
    }

    /**
     * Return the filter or null.
     * @param String $filtername
     * @return Filter $filter.
     */
    public function getFilter($filtername) {
        $filter = null;
        if (!is_null($this->filters) && isset($this->filters[$filtername])) {
            $filter = $this->filters[$filtername];
        }
        return $filter;
    }

    public function getFilters() {
        return $this->filters;
    }

    public function setFilters($filters) {
        $this->filters = $filters;
    }

}

?>
