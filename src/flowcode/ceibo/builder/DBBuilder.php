<?php

namespace flowcode\ceibo\builder;

/**
 * Description of DBBuilder
 *
 * @author juanma
 */
class DBBuilder {

    public static function getQuerys($mapping) {
        $dbms_schema = "";
        $mappers = MapperBuilder::getAll($mapping);
        foreach ($mappers as $mapper) {
            $create_sql = "CREATE TABLE `" . $mapper->getTable() . "` (";
            foreach ($mapper->getPropertys() as $property) {
                $create_sql .="`" . $property->getColumn() . "` " . $property->getType() . " NOT NULL,";
            }
            $create_sql .= "PRIMARY KEY (`id`) ) ENGINE = InnoDB;";
            $dbms_schema .= $create_sql;
        }

        return $dbms_schema;
    }

}

?>
