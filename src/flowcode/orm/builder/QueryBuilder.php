<?php

use flowcode\orm\builder\MapperBuilder;

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
    public static function buildDeleteQuery($entity, $mapper) {
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
    public static function buildDeleteRelationQuery($relation, $entity) {
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

}

?>
