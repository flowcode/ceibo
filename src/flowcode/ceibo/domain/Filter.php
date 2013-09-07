<?php

namespace flowcode\ceibo\domain;

/**
 * Description of Filter
 *
 * @author juanma
 */
class Filter {

    private $name;
    private $columns;
    private $itemsPerPage;

    function __construct() {
        $this->columns = array();
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getColumns() {
        return $this->columns;
    }

    public function setColumns($columns) {
        $this->columns = $columns;
    }

    public function addFilteredColumn($columnName) {
        $this->columns[] = $columnName;
    }
    
    public function getItemsPerPage() {
        return $this->itemsPerPage;
    }

    public function setItemsPerPage($itemsPerPage) {
        $this->itemsPerPage = $itemsPerPage;
    }





}

?>
