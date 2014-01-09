<?php

namespace flowcode\ceibo\builder;

use flowcode\ceibo\domain\Filter;
use flowcode\ceibo\domain\Mapper;
use flowcode\ceibo\domain\Property;
use flowcode\ceibo\domain\Relation;

/**
 * Description of MapperBuilder
 *
 * @author juanma
 */
class MapperBuilder {

    /**
     * Get an array of all mappers.
     * @param type $mapping
     * @return array $mpper
     */
    public static function getAll($mapping) {
        $mappingsArray = array();

        foreach ($mapping as $mappedEntity) {
            $instance = new Mapper();
            self::populateInstance($instance, $mappedEntity);
            $mappingsArray[] = $instance;
        }
        return $mappingsArray;
    }

    public static function buildFromClassName($mapping, $classname) {

        $instance = new Mapper();
        $instance->setClass($classname);

        foreach ($mapping as $mappedEntity) {
            $class = $mappedEntity->attributes()->class;
            if ($classname == $class) {
                self::populateInstance($instance, $mappedEntity);
                break;
            }
        }
        return $instance;
    }

    public static function buildFromName($mapping, $name) {

        $instance = new Mapper();
        $instance->setName($name);

        foreach ($mapping as $mappedEntity) {
            $nameAttr = $mappedEntity->attributes()->name;
            if ($instance->getName() == $nameAttr) {
                self::populateInstance($instance, $mappedEntity);
                break;
            }
        }
        return $instance;
    }

    public static function populateInstance(Mapper $instance, $mappedEntity) {
        $class = $mappedEntity->attributes()->class;
        $instance->setTable($mappedEntity->attributes()->table->__toString());
        $instance->setClass($class->__toString());

        // propertys
        $props = $mappedEntity->property;
        $propertys = array();
        foreach ($props as $property) {
            $name = $property->attributes()->name->__toString();
            $column = ($property->attributes()->column ? $property->attributes()->column->__toString() : null);
            $type = ($property->attributes()->type ? $property->attributes()->type->__toString() : null);
            $propertys[$name] = new Property($name, $column, $type);
        }
        $instance->setPropertys($propertys);

        // relations
        $rels = $mappedEntity->relation;
        $relations = array();
        foreach ($rels as $relation) {
            $relInstance = new Relation();
            $relInstance->setCardinality($relation->attributes()->cardinality->__toString());
            $relInstance->setEntity($relation->attributes()->entity->__toString());
            $relInstance->setName($relation->attributes()->name->__toString());
            $relInstance->setTable($relation->attributes()->table->__toString());
            if (is_object($relation->attributes()->lazy)) {
                $relInstance->setLazy($relation->attributes()->lazy->__toString());
            }

            $relInstance->setLocalColumn($relation->attributes()->localColumn->__toString());
            $relInstance->setForeignColumn($relation->attributes()->foreignColumn->__toString());

            $relations[$relInstance->getName()] = $relInstance;
        }
        $instance->setRelations($relations);

        // filters
        $fils = $mappedEntity->filter;
        $filters = array();
        foreach ($fils as $filter) {
            $filInstance = new Filter();
            $filInstance->setName($filter->attributes()->name->__toString());
            $filInstance->setItemsPerPage($filter->attributes()->perPage->__toString());

            $filteredColumns = $filter->attributes()->columns->__toString();
            $columList = explode(",", $filteredColumns);

            foreach ($columList as $columnName) {
                $filInstance->addFilteredColumn($columnName);
            }

            $filters[$filInstance->getName()] = $filInstance;
        }
        $instance->setFilters($filters);
    }

}

?>
