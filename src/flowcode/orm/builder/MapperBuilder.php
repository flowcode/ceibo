<?php

namespace flowcode\orm\builder;

use flowcode\orm\domain\Mapper;
use flowcode\orm\domain\Property;
use flowcode\orm\domain\Relation;

/**
 * Description of MapperBuilder
 *
 * @author juanma
 */
class MapperBuilder {

    public static function buildFromMapping($mapping, $classname) {

        $instance = new Mapper();
        $instance->setClass($classname);

        foreach ($mapping as $mappedEntity) {

            $class = $mappedEntity->attributes()->class;

            if ($instance->getClass() == $class) {

                $instance->setTable($mappedEntity->attributes()->table->__toString());

                // propertys
                $props = $mappedEntity->property;
                $propertys = array();
                foreach ($props as $property) {
                    $name = $property->attributes()->name->__toString();
                    $column = $property->attributes()->column->__toString();
                    $propertys[] = new Property($name, $column);
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
                    $relInstance->setLocalColumn($relation->attributes()->localColumn->__toString());
                    $relInstance->setForeignColumn($relation->attributes()->foreignColumn->__toString());

                    $relations[] = $relInstance;
                }
                $instance->setRelations($relations);

                break;
            }
        }
        return $instance;
    }

    public static function buildFromName($mapping, $name) {

        $instance = new Mapper();
        $instance->setName($name);

        foreach ($mapping as $mappedEntity) {

            $class = $mappedEntity->attributes()->class;
            $nameAttr = $mappedEntity->attributes()->name;

            if ($instance->getName() == $nameAttr) {

                $instance->setTable($mappedEntity->attributes()->table->__toString());
                $instance->setClass($class->__toString());

                // propertys
                $props = $mappedEntity->property;
                $propertys = array();
                foreach ($props as $property) {
                    $name = $property->attributes()->name->__toString();
                    $column = $property->attributes()->column->__toString();
                    $propertys[] = new Property($name, $column);
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
                    $relInstance->setLocalColumn($relation->attributes()->localColumn->__toString());
                    $relInstance->setForeignColumn($relation->attributes()->foreignColumn->__toString());

                    $relations[] = $relInstance;
                }
                $instance->setRelations($relations);

                break;
            }
        }
        return $instance;
    }

}

?>
