<?php

namespace flowcode\ceibo\domain;

use flowcode\ceibo\domain\Mapper;

/**
 * Description of Query
 *
 * @author JMA <jaguero@flowcode.com.ar>
 */
class Query {

    private $queryString;
    private $mapper;
    private $wheres = array();

    function __construct(Mapper $mapper) {
        $this->mapper = $mapper;
    }

    public function AndWhere($where) {
        
    }

    public function getString() {
        $selectQuery = "SELECT * FROM " . $this->mapper->getTable() . " t1 ";
        
        $query = $selectQuery;
        
        return $query;
    }

}

?>
