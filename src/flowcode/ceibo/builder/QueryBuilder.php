<?php

namespace flowcode\ceibo\builder;

use flowcode\ceibo\builder\MapperBuilder;
use flowcode\ceibo\data\DataSource;
use flowcode\ceibo\domain\Mapper;
use flowcode\ceibo\domain\Relation;

/**
 * Description of QueryBuilder
 *
 * @author JMA <jaguero@flowcode.com.ar>
 */
class QueryBuilder {

    /**
     * Build a delete query for an entity.
     * @param type $entity
     * @return string 
     */
    public static function buildDeleteQuery($entity, Mapper $mapper) {
        $query = "";
        foreach ($mapper->getRelations() as $relation) {
            $query .= self::buildDeleteRelationQuery($relation, $entity);
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
    public static function buildDeleteRelationQuery(Relation $relation, $entity) {
        $query = "DELETE FROM `" . $relation->getTable() . "` ";
        $query .= "WHERE " . $relation->getLocalColumn() . " = '" . $entity->getId() . "';";
        return $query;
    }

    /**
     * Return the entity insert query.
     * @param type $entity
     * @return string 
     */
    public static function buildInsertQuery($entity, Mapper $mapper, DataSource $dataSource) {
        $fields = "";
        $values = "";
        foreach ($mapper->getPropertys() as $property) {
            if ($property->getColumn() != "id") {
                $method = "get" . $property->getName();
                $entity->$method();
                $fields .= "`" . $property->getColumn() . "`, ";

                if ($property->isNumeric()) {
                    $values .= $dataSource->escapeString($entity->$method()) . ", ";
                } else {
                    $values .= "'" . $dataSource->escapeString($entity->$method()) . "', ";
                }
            }
        }

        $fields = substr_replace($fields, "", -2);
        $values = substr_replace($values, "", -2);

        $query = "INSERT INTO `" . $mapper->getTable() . "` (" . $fields . ") VALUES (" . $values . ");";

        return $query;
    }

    /**
     * Return the insert relation query.
     * @param type $entity
     * @param \flowcode\ceibo\domain\Relation $relation
     * @return string $query.
     */
    public static function buildRelationQuery($entity, Relation $relation) {
        $relQuery = "";
        $getid = "getId";
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


        return $relQuery;
    }

    /**
     * Return the update query for the entity.
     * @param type $entity
     * @param \flowcode\ceibo\domain\Mapper $mapper
     * @param \flowcode\ceibo\data\DataSource $dataSource
     * @return string
     */
    public static function buildUpdateQuery($entity, Mapper $mapper, DataSource $dataSource) {
        $fields = "";
        foreach ($mapper->getPropertys() as $property) {
            if ($property->getColumn() != "id") {
                $method = "get" . $property->getName();
                $entity->$method();

                if ($property->isNumeric()) {
                    $fieldValue = "`=" . $dataSource->escapeString($entity->$method()) . ", ";
                } else {
                    $fieldValue = "`='" . $dataSource->escapeString($entity->$method()) . "', ";
                }
                $fields .= "`" . $property->getColumn() . $fieldValue;
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
    public static function buildSelectRelation($entity, $relation, $mapperRelation) {
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

    public static function buildJoinRelationQuery(Relation $relation, $mainSynonym, $joinSynonym) {
        $query = "";
        if ($relation->getCardinality() == Relation::$manyToMany) {
            $query .= "INNER JOIN " . $relation->getTable() . " $joinSynonym ";
            $query .= "ON $joinSynonym." . $relation->getForeignColumn() . " = " . $mainSynonym . ".id ";
        }

        return $query;
    }

    public function getDeleteQuery($entity, Mapper $mapper) {
        $query = self::buildDeleteQuery($entity, $mapper);
        return $query;
    }

    public function getDeleteRelationQuery($relation, $entity) {
        $query = self::buildDeleteRelationQuery($relation, $entity);
        return $query;
    }

    public function getInsertQuery($entity, Mapper $mapper, DataSource $dataSource) {
        $query = self::buildInsertQuery($entity, $mapper, $dataSource);
        return $query;
    }

    public function getRelationQuery($entity, Relation $relation) {
        $query = self::buildRelationQuery($entity, $relation);
        return $query;
    }

    public function getUpdateQuery($entity, Mapper $mapper, DataSource $dataSource) {
        $query = self::buildUpdateQuery($entity, $mapper, $dataSource);
        return $query;
    }

    public function getSelectRelationQuery($entity, $relation, $mapperRelation) {
        $query = self::buildSelectRelation($entity, $relation, $mapperRelation);
        return $query;
    }

    public function getJoinRelationQuery(Relation $relation, $mainSynonym, $joinSynonym) {
        $query = self::buildJoinRelationQuery($relation, $mainSynonym, $joinSynonym);
        return $query;
    }

}

?>
