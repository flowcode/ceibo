<?php

namespace flowcode\orm\data;

interface DataSource {

    function getConnection();

    /**
     *  Execute Non Query:  
     * 
     *      Ejecuta una query sin esperar un valor de retorno.
     */
    function executeNonQuery($query);

    /**
     * Execute Query:
     * 
     *      Ejecuta una query devolviendo la tabla como resultado.  En caso de
     * no traer valores, retorna false.  En caso de error, muere informando el 
     * error.
     */
    function executeQuery($query);

    /**
     *  Execute Insert:
     * 
     *      Ejecuta una query de tipo insert devolviendo el id del registro asociado en la tabla.  Si la tabla no posee
     * campo id, la ejecución de la query se lleva a cabo pero el valor de retorno es indefinido.
     */
    function executeInsert($query);

    /**
     * Get an escaped string according to the datasource.
     */
    function escapeString($unescaped_string);
}

?>
