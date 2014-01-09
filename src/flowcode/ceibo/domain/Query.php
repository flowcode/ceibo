<?php

namespace flowcode\ceibo\domain;

use flowcode\ceibo\data\DataSource;
use flowcode\ceibo\domain\Mapper;

/**
 * Description of Query
 *
 * @author JMA <jaguero@flowcode.com.ar>
 */
class Query {

    private $statement = null;
    private $mapper = null;
    private $dataSource = null;
    private $where = null;
    private $andWheres = array();
    private $bindValues = array();

    /**
     * 
     * @param Mapper $mapper
     * @param DataSource $dataSource
     */
    function __construct(Mapper $mapper, DataSource $dataSource) {
        $this->mapper = $mapper;
        $this->dataSource = $dataSource;
    }

    /**
     * Add an And Where condition to the query.
     * @param string $condition
     * @param array $values
     * @return Query same instace.
     */
    public function AndWhere($condition, array $values) {
        $this->andWheres[] = $condition;
        $this->addBindValues($values);
        return $this;
    }

    /**
     * Set where condition of the query.
     * @param string $condition
     * @param array $values
     * @return Query same instace.
     */
    public function Where($condition, $values) {
        $this->setWhere($condition);
        $this->addBindValues($values);
        return $this;
    }

    public function buildStatement() {
        $statement = "SELECT * FROM " . $this->mapper->getTable() . " ";

        if (!is_null($this->getWhere())) {
            $statement .= "WHERE " . $this->getWhere() . " ";
            foreach ($this->getAndWheres() as $andWhere) {
                $statement .= "AND " . $andWhere . " ";
            }
        }

        return $statement;
    }

    /**
     * Execute query and return a collection.
     * @return Collection collection.
     */
    public function execute() {
        $statement = $this->buildStatement();
        $result = $this->getDataSource()->query($statement, $this->getBindValues());

        if ($result) {
            $collection = new Collection($this->getMapper()->getClass(), $result, $this->getMapper());
        } else {
            $collection = new Collection($this->getMapper()->getClass(), array(), $this->getMapper());
        }
        return $collection;
    }

    public function getStatement() {
        return $this->statement;
    }

    public function getMapper() {
        return $this->mapper;
    }

    /**
     * 
     * @return DataSource dataSource.
     */
    public function getDataSource() {
        return $this->dataSource;
    }

    public function getWhere() {
        return $this->where;
    }

    public function getAndWheres() {
        return $this->andWheres;
    }

    public function setStatement($statement) {
        $this->statement = $statement;
    }

    public function setMapper($mapper) {
        $this->mapper = $mapper;
    }

    public function setDataSource($dataSource) {
        $this->dataSource = $dataSource;
    }

    public function setWhere($where) {
        $this->where = $where;
    }

    public function setAndWheres($andWheres) {
        $this->andWheres = $andWheres;
    }

    public function getBindValues() {
        return $this->bindValues;
    }

    public function setBindValues($bindValues) {
        $this->bindValues = $bindValues;
    }

    public function addBindValues(array $bindValues) {
        foreach ($bindValues as $bindKey => $bindValue) {
            $this->bindValues[$bindKey] = $bindValue;
        }
    }

}

?>
