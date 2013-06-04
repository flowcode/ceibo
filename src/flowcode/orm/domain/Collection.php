<?php

namespace flowcode\orm\domain;

use Exception;
use flowcode\orm\domain\Mapper;
use Iterator;

/**
 * Description of Collection
 *
 * @author juanma
 */
class Collection implements Iterator {

    protected $mapper;
    protected $total = 0;
    protected $raw = array();
    private $result;
    private $pointer;
    private $objects = array();
    private $targetClass;

    /**
     * New Collection instance.
     * @param type $targetClass
     * @param array $raw
     * @param \flowcode\orm\domain\Mapper $mapper
     */
    function __construct($targetClass, array $raw = NULL, Mapper $mapper = NULL) {
        if (!is_null($raw) && !is_null($mapper)) {
            $this->raw = $raw;
            $this->total = count($raw);
        }
        $this->mapper = $mapper;
        $this->targetClass = $targetClass;
        $this->pointer = 0;
    }

    function add($object) {
        $class = $this->targetClass;
        if (!($object instanceof $class)) {
            throw new Exception("This is a {$class} collection");
        }
        $this->notifyAccess();
        $this->objects[$this->total] = $object;
        $this->total++;
    }

    public function getTargetClass() {
        return $this->targetClass;
    }

    public function setTargetClass($targetClass) {
        $this->targetClass = $targetClass;
    }

    protected function notifyAccess() {
        
    }

    private function getRow($num) {
        $this->notifyAccess();
        if ($num >= $this->total || $num < 0) {
            return NULL;
        }

        if (isset($this->objects[$num])) {
            return $this->objects[$num];
        }

        if (isset($this->raw[$num])) {
            $this->objects[$num] = $this->mapper->createObject($this->raw[$num]);
            return $this->objects[$num];
        }
    }

    public function rewind() {
        $this->pointer = 0;
    }

    public function current() {
        return $this->getRow($this->pointer);
    }

    public function key() {
        return $this->pointer;
    }

    public function next() {
        $row = $this->getRow($this->pointer);
        if ($row) {
            $this->pointer++;
        }
        return $row;
    }

    public function valid() {
        return (!is_null($this->current()));
    }

    public function count() {
        return $this->total;
    }

}

?>
