<?php

namespace flowcode\orm\domain;

/**
 * Description of Relation
 *
 * @author juanma
 */
class Relation {

    private $cardinality;
    private $name;
    private $entity;
    private $table;
    private $localColumn;
    private $localMethod;
    private $foreignColumn;
    private $foreignMethod;
    
    public static $oneToMany = "one-to-many";
    public static $manyToMany = "many-to-many";

    function __construct() {
        
    }

    public function getCardinality() {
        return $this->cardinality;
    }

    public function setCardinality($cardinality) {
        $this->cardinality = $cardinality;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getTable() {
        return $this->table;
    }

    public function setTable($table) {
        $this->table = $table;
    }

    public function getLocalId() {
        return $this->localId;
    }

    public function setLocalMethod($localMethod) {
        $this->localMethod = $localMethod;
    }

    public function getForeignMethod() {
        return $this->foreignMethod;
    }

    public function setForeignMethod($foreignMethod) {
        $this->foreignMethod = $foreignMethod;
    }
    
    public function getLocalColumn() {
        return $this->localColumn;
    }

    public function setLocalColumn($localColumn) {
        $this->localColumn = $localColumn;
    }

    public function getForeignColumn() {
        return $this->foreignColumn;
    }

    public function setForeignColumn($foreignColumn) {
        $this->foreignColumn = $foreignColumn;
    }


    public function getEntity() {
        return $this->entity;
    }

    public function setEntity($entity) {
        $this->entity = $entity;
    }


}

?>
