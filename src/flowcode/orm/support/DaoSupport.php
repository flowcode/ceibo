<?php

namespace flowcode\orm\support;

use flowcode\mvc\kernel\DataSource;
use flowcode\orm\builder\MapperBuilder;
use flowcode\orm\domain\Relation;

/**
 * Description of DaoSupport.
 *
 * @author juanma
 */
class DaoSupport {

    private $dataSource;
    private $mappingFilePath;
    private $mapping;

    public function __construct() {
        $this->dataSource = new DataSource();
        $this->mappingFilePath = dirname(__FILE__) . "/../../inter/config/orm-mapping.xml";
        $this->mapping = simplexml_load_file($this->mappingFilePath);
    }

    /**
     * Save or Update an entity according to its mapping.
     * @param type $entity 
     */
    public function save($entity) {
        $id = "";
        if (is_null($entity->getId())) {
            // insert entity
            $queryIns = $this->buildInsertQuery($entity);
            $id = $this->dataSource->insertSQL($queryIns);
            $entity->setId($id);

            // relations
            $queryRel = $this->buildRelationQuery($entity);
            foreach (explode(";", $queryRel) as $q) {
                if (strlen($q) > 5)
                    $this->dataSource->executeInsert($q);
            }
        } else {
            $queryUpt = $this->buildUpdateQuery($entity);
            $this->dataSource->executeNonQuery($queryUpt);
            $id = $entity->getId();
            $this->updateRelations($entity);
        }
        return $id;
    }

    /**
     * Update the entity relations.
     * 
     * OneToOne o ManyToMany.
     * 
     * @param type $entity 
     */
    public function updateRelations($entity) {
        $mapper = MapperBuilder::buildFromMapping($this->mapping, get_class($entity));
        foreach ($mapper->getRelations() as $relation) {
            if ($relation->getCardinality() == Relation::$manyToMany) {
                // delete previous relations
                $queryDeletePrevious = $this->buildDeleteRelationQuery($relation, $entity);
                $this->dataSource->executeNonQuery($queryDeletePrevious);

                // insert new relations
                $queryRel = $this->buildRelationQuery($entity);
                foreach (explode(";", $queryRel) as $q) {
                    if (strlen($q) > 5)
                        $this->dataSource->executeInsert($q);
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
     * Build a delete query for an entity.
     * @param type $entity
     * @return string 
     */
    public function buildDeleteQuery($entity) {
        $mapper = MapperBuilder::buildFromMapping($this->mapping, get_class($entity));
        $query = "";
        foreach ($mapper->getRelations() as $relation) {
            $query .= $this->buildDeleteRelationQuery($relation, $entity);
        }

        $query .= "DELETE FROM " . $mapper->getTable() . " ";
        $query .= "WHERE id = '" . $entity->getId() . "';";

        return $query;
    }

    /**
     * Build a delete query for an entity an its relation.
     * @param type $relation
     * @param type $entity
     * @return string 
     */
    public function buildDeleteRelationQuery($relation, $entity) {
        $query = "DELETE FROM " . $relation->getTable() . " ";
        $query .= "WHERE " . $relation->getForeignColumn() . " = '" . $entity->getId() . "';";
        return $query;
    }

    /**
     * Return the entity insert query.
     * @param type $entity
     * @return string 
     */
    public function buildInsertQuery($entity) {

        $mapper = MapperBuilder::buildFromMapping($this->mapping, get_class($entity));
        $fields = "";
        $values = "";
        foreach ($mapper->getPropertys() as $property) {
            if ($property->getColumn() != "id") {
                $method = "get" . $property->getName();
                $entity->$method();
                $fields .= "`" . $property->getColumn() . "`, ";
                $values .= "'" . $entity->$method() . "', ";
            }
        }

        $fields = substr_replace($fields, "", -2);
        $values = substr_replace($values, "", -2);

        $query = "INSERT INTO `" . $mapper->getTable() . "` (" . $fields . ") VALUES (" . $values . ");";

        return $query;
    }

    public function buildRelationQuery($entity) {
        $mapper = MapperBuilder::buildFromMapping($this->mapping, get_class($entity));
        $relQuery = "";
        $getid = "getId";
        foreach ($mapper->getRelations() as $relation) {
            if ($relation->getCardinality() == Relation::$manyToMany) {
                $m = "get" . $relation->getName();
                foreach ($entity->$m() as $rel) {
                    $relQuery .= "INSERT INTO " . $relation->getTable() . " (" . $relation->getLocalColumn() . ", " . $relation->getForeignColumn() . ") ";
                    $relQuery .= "VALUES ('" . $entity->$getid() . "', '" . $rel->$getid() . "');";
                }
            }
            if ($relation->getCardinality() == Relation::$oneToMany) {
                $relMapper = MapperBuilder::buildFromName($this->mapping, $relation->getEntity());
                $m = "get" . $relation->getName();
                foreach ($entity->$m() as $rel) {
                    $setid = "set" . $relMapper->getNameForColumn($relation->getForeignColumn());
                    $rel->$setid($entity->$getid());
                    $relQuery .= $this->buildInsertQuery($rel);
                }
            }
        }

        return $relQuery;
    }

    /**
     * Return the entity insert query.
     * @param type $entity
     * @return string 
     */
    public function buildUpdateQuery($entity) {
        $mapper = MapperBuilder::buildFromMapping($this->mapping, get_class($entity));

        $fields = "";
        foreach ($mapper->getPropertys() as $property) {
            if ($property->getColumn() != "id") {
                $method = "get" . $property->getName();
                $entity->$method();
                $fields .= "`" . $property->getColumn() . "`='" . $entity->$method() . "', ";
            }
        }
        $fields = substr_replace($fields, "", -2);
        $query = "UPDATE `" . $mapper->getTable() . "` SET " . $fields . " WHERE id='" . $entity->getId() . "'";

        return $query;
    }

    /**
     * Get the query for select the related entitys.
     * @param type $entity
     * @param type $relation Name of the relation.
     */
    public function buildSelectRelation($entity, $mapper, $relation, $mapperRelation) {
        $query = "";

        $fields = "";
        foreach ($mapperRelation->getPropertys() as $property) {
            $fields .= "c." . $property->getColumn() . ", ";
        }
        $fields = substr_replace($fields, "", -2);

        if ($relation->getCardinality() == Relation::$manyToMany) {
            $query = "select " . $fields . " from " . $mapperRelation->getTable() . " c ";
            $query .= "inner join " . $relation->getTable() . " nc on nc." . $relation->getForeignColumn() . " = c.id ";
            $query .= "where nc." . $relation->getLocalColumn() . " = " . $entity->getId();
        }
        if ($relation->getCardinality() == Relation::$oneToMany) {
            $query = "select " . $fields . " from " . $mapperRelation->getTable() . " c ";
            $query .= "where c." . $relation->getForeignColumn() . " = " . $entity->getId();
        }
        return $query;
    }

    /**
     * Return an array of all entitys.
     * @param object $entity
     * @return array array of entitys.
     */
    public function findAll($entity, $ordenColumn = null, $ordenType = null) {
        $mapper = MapperBuilder::buildFromMapping($this->mapping, get_class($entity));

        $query = "SELECT * FROM `" . $mapper->getTable() . "` ";
        if(!is_null($ordenColumn)){
            $query .= "ORDER BY $ordenColumn ";
            if(!is_null($ordenType)){
                $query .= "$ordenType";
            }  else {
                $query .= "ASC";
            }
        }
        $result = $this->dataSource->executeQuery($query);

        $array = array();
        if ($result) {
            $class = $mapper->getClass();
            foreach ($result as $row) {
                $newEntity = new $class();
                $this->populateEntity($newEntity, $row, $mapper);
                $array[] = $newEntity;
            }
        }

        return $array;
    }

    /**
     * Find an entity bu its id.
     * @param type $class
     * @param type $id
     * @return \flowcode\orm\support\class 
     */
    public function findById($class, $id) {
        $mapper = MapperBuilder::buildFromMapping($this->mapping, $class);

        $newEntity = NULL;

        $query = "SELECT * FROM `" . $mapper->getTable() . "` WHERE id='$id'";
        $result = $this->dataSource->executeQuery($query);

        if ($result) {
            $class = $mapper->getClass();
            $newEntity = new $class();
            $this->populateEntity($newEntity, $result[0], $mapper);

            // populate relations
            foreach ($mapper->getRelations() as $relation) {

                $relMapper = MapperBuilder::buildFromName($this->mapping, $relation->getEntity());
                $queryRel = $this->buildSelectRelation($newEntity, $mapper, $relation, $relMapper);
                $resRel = $this->dataSource->executeQuery($queryRel);

                $classRel = $relMapper->getClass();
                $method = "set" . $relation->getName();
                if ($resRel) {
                    foreach ($resRel as $row) {
                        $newEntityRel = new $classRel();
                        $this->populateEntity($newEntityRel, $row, $relMapper, $relation->getForeignColumn());
                        $array[] = $newEntityRel;
                    }
                    $newEntity->$method($array);
                }
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
        $deleteQuerys = $this->buildDeleteQuery($entity);
        foreach (explode(";", $deleteQuerys) as $q) {
            if (strlen($q) > 5)
                $this->dataSource->executeNonQuery($q);
        }
        return true;
    }

    public function getDataSource() {
        return $this->dataSource;
    }

    public function setDataSource($dataSource) {
        $this->dataSource = $dataSource;
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

    public function populateEntity($entity, $values, $mapper = null, $relationColumn = null) {
        if (is_null($mapper)) {
            $mapper = MapperBuilder::buildFromMapping($this->mapping, get_class($entity));
        }
        foreach ($values as $key => $value) {
            if ($mapper->getNameForColumn($key) != NULL) {
                $method = "set" . $mapper->getNameForColumn($key);
                $entity->$method($value);
            }
        }
    }

}

?>
