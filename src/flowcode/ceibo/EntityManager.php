<?php

namespace flowcode\ceibo;

use flowcode\ceibo\builder\MapperBuilder;
use flowcode\ceibo\builder\QueryBuilder;
use flowcode\ceibo\data\DataSource;
use flowcode\ceibo\domain\Collection;
use flowcode\ceibo\domain\Relation;
use flowcode\ceibo\EntityManager;
use flowcode\wing\utils\Pager;

/**
 * Description of EntityManager
 *
 * @author JMA <jaguero@flowcode.com.ar>
 */
class EntityManager {

    private static $instance;
    private $conn = NULL;
    private $mappingFilePath = NULL;
    private $mapping = NULL;

    private function __construct(DataSource $dataSource = NULL) {
        if (!is_null($dataSource))
            $this->conn = $dataSource;
    }

    private function load() {
        if (is_null($this->mapping)) {
            if (!is_null($this->mappingFilePath)) {
                $this->mapping = simplexml_load_file($this->mappingFilePath);
            }
        }
    }

    /**
     * Get an EntityManager instance.
     * @return EntityManager $em.
     */
    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new EntityManager();
        }
        return self::$instance;
    }

    /**
     * Save or Update an entity according to its mapping.
     * @param type $entity 
     */
    public function save($entity) {
        $this->load();
        $mapper = MapperBuilder::buildFromClassName($this->mapping, get_class($entity));
        if (is_null($entity->getId())) {
            $affectedRows = $this->insertEntity($entity, $mapper);
        } else {
            $affectedRows = $this->updateEntity($entity, $mapper);
        }
        if (0 < $affectedRows) {
            return true;
        } else {
            return false;
        }
    }

    public function insertEntity($entity, $mapper) {
        $insertStmt = QueryBuilder::buildInsertQuery($entity, $mapper);
        $values = array();
        $affectedRows = 0;
        foreach ($mapper->getPropertys() as $property) {
            if ($property->getColumn() != "id") {
                $method = "get" . $property->getName();
                $values[":" . $property->getColumn()] = $entity->$method();
            }
        }
        try {
            /* begin transac */
            $this->getDataSource()->beginTransaction();

            /* insert */
            $affectedRows = $this->getDataSource()->insertSingleRow($insertStmt, $values);

            /* relations */
            foreach ($mapper->getRelations() as $relation) {
                $this->getDataSource()->doInsertRelation($entity, $relation);

                $insertRelStmt = QueryBuilder::buildRelationQuery($entity, $relation);
                $valuesUpt = array();
                $m = "get" . $relation->getName();
                $getid = "getId";
                foreach ($entity->$m() as $rel) {
                    $valueRow = array();
                    $valueRow[":" . $relation->getLocalColumn()] = $entity->$getid();
                    $valueRow[":" . $relation->getForeignColumn()] = $rel->$getid();
                    $valuesUpt[] = $valueRow;
                }

                $this->getDataSource()->insertMultipleRow($insertRelStmt, $valuesUpt);
            }

            /* end transaction */
            $this->getDataSource()->commitTransaction();
        } catch (PDOException $e) {
            $this->getDataSource()->rollbackTransaction();
        }
        return $affectedRows;
    }

    public function updateEntity($entity, $mapper) {

        $udateStatement = QueryBuilder::buildUpdateQuery($entity, $mapper);
        $values = array();
        $affectedRows = 0;
        foreach ($mapper->getPropertys() as $property) {
            $method = "get" . $property->getName();
            $values[":" . $property->getColumn()] = $entity->$method();
        }

        $conn = $this->getDataSource();
        try {
            $conn->beginTransaction();
            /* update entity */
            $affectedRows = $conn->updateSingleRow($udateStatement, $values);

            /* update relations */
            foreach ($mapper->getRelations() as $relation) {
                if ($relation->getCardinality() == Relation::$manyToMany) {
                    // delete previous relations
                    $queryDeletePrevious = QueryBuilder::buildDeleteRelationQuery($relation);
                    $conn->deleteSingleRow($queryDeletePrevious, array(":id" => $entity->getId()));

                    // insert new relations
                    $insertRelStmt = QueryBuilder::buildRelationQuery($entity, $relation);
                    $values = array();
                    $m = "get" . $relation->getName();
                    $getid = "getId";
                    foreach ($entity->$m() as $rel) {
                        $valueRow = array();
                        $valueRow[":" . $relation->getLocalColumn()] = $entity->$getid();
                        $valueRow[":" . $relation->getForeignColumn()] = $rel->$getid();
                        $values[] = $valueRow;
                    }
                    $conn->insertMultipleRow($insertRelStmt, $values);
                }
                if ($relation->getCardinality() == Relation::$oneToMany) {
                    $relMapper = MapperBuilder::buildFromName($this->mapping, $relation->getEntity());
                    $m = "get" . $relation->getName();
                    $setid = "set" . $relMapper->getNameForColumn($relation->getForeignColumn());

                    // save actual relations
                    foreach ($entity->$m() as $relEntity) {
                        $relEntity->$setid($entity->getId());
                        $this->save($relEntity);
                    }

                    //  delete old relations.
                    // TODO: delete old relations
                }
            }
            $conn->commitTransaction();
        } catch (PDOException $e) {
            $conn->rollbackTransaction();
        }

        return $affectedRows;
    }

    /**
     * Update the entity relations.
     * 
     * OneToOne o ManyToMany.
     * 
     * @param type $entity 
     */
    public function updateRelations($entity, $mapper) {
        foreach ($mapper->getRelations() as $relation) {
            if ($relation->getCardinality() == Relation::$manyToMany) {
                // delete previous relations
                $queryDeletePrevious = QueryBuilder::buildDeleteRelationQuery($relation, $entity);
                foreach (explode(";", $queryDeletePrevious) as $q) {
                    if (strlen($q) > 5)
                        $this->getDataSource()->executeNonQuery($q);
                }

                // insert new relations
                $queryRel = QueryBuilder::buildRelationQuery($entity, $relation);
                foreach (explode(";", $queryRel) as $q) {
                    if (strlen($q) > 5)
                        $this->getDataSource()->executeInsert($q);
                }
            }
            if ($relation->getCardinality() == Relation::$oneToMany) {
                $relMapper = MapperBuilder::buildFromName($this->mapping, $relation->getEntity());
                $m = "get" . $relation->getName();
                $setid = "set" . $relMapper->getNameForColumn($relation->getForeignColumn());

                // save actual relations
                foreach ($entity->$m() as $relEntity) {
                    $relEntity->$setid($entity->getId());
                    $this->save($relEntity);
                }

                //  delete old relations.
                // TODO: delete old relations
            }
        }
    }

    /**
     * Return an array of all entitys.
     * @param object $entity
     * @return array array of entitys.
     */
    public function findAll($name, $ordenColumn = null, $ordenType = null) {
        $this->load();
        $mapper = MapperBuilder::buildFromName($this->mapping, $name);

        $query = "SELECT * FROM `" . $mapper->getTable() . "` ";
        if (!is_null($ordenColumn)) {
            $query .= "ORDER BY $ordenColumn ";
            if (!is_null($ordenType)) {
                $query .= "$ordenType";
            } else {
                $query .= "ASC";
            }
        }
        $raw = $this->getDataSource()->query($query);
        if ($raw) {
            $collection = new Collection($mapper->getClass(), $raw, $mapper);
        } else {
            $collection = new Collection($mapper->getClass(), array(), $mapper);
        }
        return $collection;
    }

    /**
     * Find an entity bu its id.
     * @param type $class
     * @param type $id
     * @return \flowcode\ceibo\support\class 
     */
    public function findById($name, $id) {
        $this->load();
        $mapper = MapperBuilder::buildFromName($this->mapping, $name);

        $newEntity = NULL;

        $query = "SELECT * FROM `" . $mapper->getTable() . "` WHERE id = :id";

        $result = $this->getDataSource()->query($query, array(":id" => $id));

        if ($result) {
            $newEntity = $mapper->createObject($result[0]);

            /* relations */
            foreach ($mapper->getRelations() as $relation) {

                $relMapper = MapperBuilder::buildFromName($this->mapping, $relation->getEntity());

                $queryRel = QueryBuilder::buildSelectRelation($relation, $relMapper);

                $resRel = $this->getDataSource()->query($queryRel, array(":id" => $newEntity->getId()));

                $method = "set" . $relation->getName();
                if ($resRel) {
                    $collection = new Collection($relMapper->getClass(), $resRel, $relMapper);
                } else {
                    $collection = new Collection($relMapper->getClass(), array(), $relMapper);
                }
                $newEntity->$method($collection);
            }
        }
        return $newEntity;
    }

    /**
     * Delete an entity and its relations.
     * @param type $entity
     * @return boolean 
     */
    public function delete($entity) {
        $this->load();
        $mapper = MapperBuilder::buildFromClassName($this->mapping, get_class($entity));
        $conn = $this->getDataSource();
        try {
            $conn->beginTransaction();

            /* delete relations */
            foreach ($mapper->getRelations() as $relation) {
                /* many to many */
                if ($relation->getCardinality() == Relation::$manyToMany) {
                    $queryDeletePrevious = QueryBuilder::buildDeleteRelationQuery($relation);
                    $conn->deleteSingleRow($queryDeletePrevious, array(":id" => $entity->getId()));
                }
            }
            $deleteStmt = QueryBuilder::buildDeleteQuery($mapper);
            $conn->deleteSingleRow($deleteStmt, array(":id" => $entity->getId()));
            $conn->commitTransaction();
        } catch (PDOException $e) {
            $conn->rollbackTransaction();
        }

        return true;
    }

    /**
     * 
     * @param \flowcode\ceibo\Entity $entity
     * @param type $relationName
     * @return \flowcode\ceibo\class
     */
    public function findRelation($entity, $relationName) {
        $this->load();
        $mapper = MapperBuilder::buildFromClassName($this->mapping, get_class($entity));
        $relation = $mapper->getRelation($relationName);
        $relationMapper = MapperBuilder::buildFromName($this->mapping, $relation->getEntity());

        $selectQuery = "SELECT tmain.* FROM `" . $relationMapper->getTable() . "` tmain ";
        $joinQuery = QueryBuilder::buildJoinRelationQuery($relation, "tmain", "j1");
        $whereQuery = "WHERE j1." . $relation->getLocalColumn() . " = '" . $entity->getId() . "'";

        $query = $selectQuery . $joinQuery . $whereQuery;
        $queryResult = $this->getDataSource()->query($query);
        if ($queryResult) {
            $collection = new Collection($relationMapper->getClass(), $queryResult, $relationMapper);
        } else {
            $collection = new Collection($relationMapper->getClass(), array(), $relationMapper);
        }

        return $collection;
    }

    /**
     * Finds entitys wich apply the filter.
     * Example: "name = 'some name'".
     * @param type $name
     * @param type $filter
     * @param type $orderColumn
     * @param type $orderType
     * @return \flowcode\ceibo\class
     */
    public function findByWhereFilter($name, $filter, $orderColumn = null, $orderType = NULL) {
        $this->load();
        $mapper = MapperBuilder::buildFromName($this->mapping, $name);

        $query = "SELECT * FROM `" . $mapper->getTable() . "` ";
        $query .= "WHERE 1 ";
        if (!is_null($filter)) {
            $query .= "AND " . $filter;
        }

        if (!is_null($orderColumn)) {
            $query .= "ORDER BY `$orderColumn` ";
            if (!is_null($orderType)) {
                $query .= "$orderType";
            } else {
                $query .= "ASC ";
            }
        }
        $result = $this->getDataSource()->query($query);

        if ($result) {
            $collection = new Collection($mapper->getClass(), $result, $mapper);
        } else {
            $collection = new Collection($mapper->getClass(), array(), $mapper);
        }

        return $collection;
    }

    /**
     * Finds entitys by its generic filter defined in the configured mapping.
     * @param type $name
     * @param type $filter
     * @param type $page
     * @param type $orderColumn
     * @param type $orderType
     * @return Pager
     */
    public function findByGenericFilter($name, $filter = null, $page = 1, $orderColumn = null, $orderType = null) {
        $this->load();
        $mapper = MapperBuilder::buildFromName($this->mapping, $name);

        $selectQuery = "";
        $whereQuery = "";
        $orderQuery = "";

        $selectQuery .= "SELECT * FROM `" . $mapper->getTable() . "` ";
        $filterList = array();
        if (!is_null($filter)) {
            $filterList = explode(" ", $filter);
        }

        if (!is_null($filter)) {
            $whereQuery .= " WHERE 1=2 ";
            foreach ($filterList as $searchedWord) {
                foreach ($mapper->getFilter("generic")->getColumns() as $filteredColumn) {
                    $whereQuery .= " OR $filteredColumn LIKE '%" . $searchedWord . "%'";
                }
            }
        } else {
            $whereQuery .= " WHERE 1 ";
        }

        if (!is_null($orderColumn)) {
            $orderQuery .= "ORDER BY $orderColumn ";
            if (!is_null($orderType)) {
                $orderQuery .= "$orderType";
            } else {
                $orderQuery .= "ASC";
            }
        }

        $from = ($page - 1) * $mapper->getFilter("generic")->getItemsPerPage();
        $pageQuery = " LIMIT $from , " . $mapper->getFilter("generic")->getItemsPerPage();

        $query = $selectQuery . $whereQuery . $orderQuery . $pageQuery;
        $result = $this->getDataSource()->query($query);

        if ($result) {
            $collection = new Collection($mapper->getClass(), $result, $mapper);
        } else {
            $collection = new Collection($mapper->getClass(), array(), $mapper);
        }

        $selectCountQuery = "SELECT count(*) as total FROM `" . $mapper->getTable() . "` ";
        $query = $selectCountQuery . $whereQuery;
        $result = $this->getDataSource()->query($query);
        $itemCount = $result[0]["total"];
        $pager = new Pager($collection, $itemCount, $mapper->getFilter("generic")->getItemsPerPage(), $page);

        return $pager;
    }

    /**
     * Execute raw query.
     * @param string $query
     * @return array
     */
    public function executeRawQuery($query) {
        return $this->getDataSource()->executeQuery($query);
    }

    /**
     * Get the current DataSource instance.
     * @return DataSource
     */
    public function getDataSource() {
        return $this->conn;
    }

    /**
     * Function to set a DataSource instance.
     * @param DataSource $dataSource
     */
    public function setDataSource(DataSource $dataSource) {
        $this->conn = $dataSource;
    }

    public function getMappingFilePath() {
        return $this->mappingFilePath;
    }

    public function setMappingFilePath($mappingFilePath) {
        $this->mappingFilePath = $mappingFilePath;
    }

    public function getMapping() {
        return $this->mapping;
    }

    public function setMapping($mapping) {
        $this->mapping = $mapping;
    }

}

?>
