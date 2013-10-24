<?php

namespace flowcode\ceibo\data;

interface DataSource {

    function getConnection();

    /**
     * Insert a single row according to statement.
     * @param string $statement
     * @param array $values
     */
    function insertSingleRow($statement, $values);
    
    /**
     * Delete a single row according to statement.
     * @param string $statement
     * @param array $values
     */
    function deleteSingleRow($statement, $values);

    /**
     * Update a single row according to statement.
     * @param string $statement
     * @param array $values
     */
    function updateSingleRow($statement, $values);

    /**
     * Insert multiple rows according to statement.
     * @param type $statement
     * @param type $values
     */
    function insertMultipleRow($statement, $values);

    /**
     * Query data source returning a collection.
     * @param string $sql
     */
    function query($sql);

    function beginTransaction();

    function commitTransaction();

    function rollbackTransaction();
}

?>
