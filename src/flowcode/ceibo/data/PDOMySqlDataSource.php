<?php

namespace flowcode\ceibo\data;

use Exception;
use flowcode\ceibo\builder\MapperBuilder;
use flowcode\ceibo\builder\QueryBuilder;
use flowcode\ceibo\domain\Relation;
use PDO;
use PDOException;

class PDOMySqlDataSource implements DataSource {

    private $dbDsn = "";
    private $dbUser = "";
    private $dbPass = "";
    private $conn = null;

    public function __construct() {
        
    }

    /**
     * Open a mysql connection.
     * @return type
     * @throws Exception
     */
    function getConnection() {
        try {
            if (is_null($this->conn)) {
                $this->conn = new PDO($this->getDbDsn(), $this->getDbUser(), $this->getDbPass());
            }
            return $this->conn;
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    /**
     * 
     * @param string $sql
     * @return type
     * @throws Exception
     */
    function query($sql) {
        try {
            return $this->getConnection()->query($sql);
        } catch (Exception $pEx) {
            throw new Exception("Fallo al ejecutar la query: " . $query . "  " . $pEx->getMessage());
        }
    }

    function insertSingleRow($statement, $values) {
        $stmt = $this->getConnection()->prepare($statement);
        $affectedRows = $stmt->execute($values);
        return $affectedRows;
    }
    
    function deleteSingleRow($statement, $values) {
        $stmt = $this->getConnection()->prepare($statement);
        $affectedRows = $stmt->execute($values);
        return $affectedRows;
    }

    function updateSingleRow($statement, $values) {
        $stmt = $this->getConnection()->prepare($statement);
        $affectedRows = $stmt->execute($values);
        return $affectedRows;
    }

    function insertMultipleRow($statement, $values) {
        $stmt = $this->getConnection()->prepare($statement);
        $affectedRows = 0;
        foreach ($values as $valueRow) {
            $affectedRows += $stmt->execute($valueRow);
        }
        return $affectedRows;
    }

    function doInsert($entity, $mapper) {
        $affectedRows = 0;
        $statement = QueryBuilder::buildInsertQuery($entity, $mapper);
        $stmt = $this->getConnection()->prepare($statement);
        foreach ($mapper->getPropertys() as $property) {
            if ($property->getColumn() != "id") {
                $method = "get" . $property->getName();
                $stmt->bindParam(":" . $property->getColumn(), $entity->$method());
            }
        }
        $affectedRows = $stmt->execute();

        return $affectedRows;
    }

    function doInsertRelation($entity, $relation) {
        $statement = QueryBuilder::getInsertRelation($entity, $relation);
        $stmt = $this->getConnection()->prepare($statement);
        $getid = "getId";

        foreach ($entity->$m() as $rel) {
            $values = array();
            $values[":" . $relation->getLocalColumn()] = $entity->$getid();
            $values[":" . $relation->getForeignColumn()] = $rel->$getid();
            $stmt->execute($values);
        }
    }

    function beginTransaction() {
        return $this->getConnection()->beginTransaction();
    }

    function commitTransaction() {
        return $this->getConnection()->commit();
    }

    function rollbackTransaction() {
        return $this->getConnection()->rollBack();
    }

    public function getConnectionString() {
        return $this->connectionString;
    }

    public function setConnectionString($connectionString) {
        $this->connectionString = $connectionString;
    }

    public function getDbDsn() {
        return $this->dbDsn;
    }

    public function setDbDsn($dbDsn) {
        $this->dbDsn = $dbDsn;
    }

    public function getDbUser() {
        return $this->dbUser;
    }

    public function setDbUser($dbUser) {
        $this->dbUser = $dbUser;
    }

    public function getDbPass() {
        return $this->dbPass;
    }

    public function setDbPass($dbPass) {
        $this->dbPass = $dbPass;
    }

}

?>
