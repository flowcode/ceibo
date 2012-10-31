<?php

namespace flowcode\orm\data;

class DataSource {

    private $db_server = "server";
    private $db_name = "dbname";
    private $db_user = "user";
    private $db_pass = "pass";

    public function __construct() {
//        $this->db_server = Config::get('database', 'dbserver');
//        $this->db_name = Config::get('database', 'dbname');
//        $this->db_user = Config::get('database', 'dbuser');
//        $this->db_pass = Config::get('database', 'dbpass');
    }

    function getConnection() {
        try {
            $con = mysql_connect($this->db_server, $this->db_user, $this->db_pass);

            if (!$con) {
                throw new \Exception("Could not connect: " . mysql_error());
            }

// selecciono la base
            mysql_select_db($this->db_name, $con);
            mysql_query("SET NAMES 'utf8'");
            return $con;
        } catch (\Exception $pEx) {
            throw new \Exception("Fallo al obtener la conexion. " . $pEx->getMessage());
        }
    }

    function executeSQL($query) {

        $connection = $this->getConnection();

        if (!mysql_query($query, $connection)) {
            die('Error: ' . mysql_error());
        }

        mysql_close($connection);
    }

    function insertSQL($query) {

        $id = -1;

        $connection = $this->getConnection();

        if (mysql_query($query, $connection)) {
            $id = mysql_insert_id();
        } else {
            die('Error: ' . mysql_error());
        }

        mysql_close($connection);

        return $id;
    }

    /**
     * Retorna una lista de la entidad pasada por parametro, de acuerdo a la consulta recibida.
     * @param type $query
     * @param type $entity 
     */
    function getListSQL($query, $class) {

        $connection = $this->getConnection();


        $result = mysql_query($query, $connection);

        if (!$result) {
            die('Error: ' . mysql_error());
        }

        $list = array();

        while ($row = mysql_fetch_array($result)) {
            $instance = new $class;
            $instance->hidrate($row);
            $list[] = $instance;
        }

        mysql_close($connection);

        return $list;
    }

    /**
     *  Execute Non Query:  
     * 
     *      Ejecuta una query sin esperar un valor de retorno.
     */
    function executeNonQuery($pQuery) {

        try {
            $connection = $this->getConnection();

            if (!mysql_query($pQuery, $connection)) {
                mysql_close($connection);
                throw new \Exception("SQL Error: " . mysql_error());
            }

            mysql_close($connection);
        } catch (\Exception $pEx) {
            throw new \Exception("Fallo al ejecutar la query: " . $pQuery . $pEx->getMessage());
        }
    }

    /**
     * Execute Query:
     * 
     *      Ejecuta una query devolviendo la tabla como resultado.  En caso de
     * no traer valores, retorna false.  En caso de error, muere informando el 
     * error.
     */
    function executeQuery($pQuery) {

        try {
// Por defecto se asume una consulta con resultado vacio.
            $table = FALSE;

            if (!is_null($pQuery) && is_string($pQuery)) {
                $connection = $this->getConnection();
                $resultQry = mysql_query($pQuery, $connection);

                if ($resultQry) {

// La query se ejecuto sin errores.
// die('previo rowTemplate');                

                    if (mysql_num_rows($resultQry) != 0) {

// La query trajo al menos un registro.
// Instancio el template de las filas.
                        $rowTemplate = array();
                        $terminar = FALSE;
                        $cntCols = mysql_num_fields($resultQry);
                        $cnt = 0;

                        while (!$terminar) {

                            $nombreCampo = mysql_field_name($resultQry, $cnt);

                            if ($nombreCampo) {
                                $rowTemplate[$nombreCampo] = $nombreCampo;
                                $cnt = $cnt + 1;
                            }

                            $terminar = (( $cntCols - $cnt ) == 0);
                        } // while
// Instancio la tabla.
                        $table = array();

                        while ($fila = mysql_fetch_assoc($resultQry)) {

// Instancio un row en base al template.
                            $rowAux = $rowTemplate;

                            foreach ($rowAux as $k => $v) {
                                $rowAux[$k] = $fila[$k];
                            }

                            $table[] = $rowAux;
                        }

                        mysql_free_result($resultQry);
                    }
// Libero la conexion.
                    mysql_close($connection);
                } else {
                    throw new \Exception("SQL Error: " . mysql_error());
                }
            }

            return $table;
        } catch (\Exception $pEx) {
            throw new \Exception("Fallo al ejecutar la query: " . $pQuery . "  " . $pEx->getMessage());
        }
    }

    /**
     *  Execute Insert:
     * 
     *      Ejecuta una query de tipo insert devolviendo el id del registro asociado en la tabla.  Si la tabla no posee
     * campo id, la ejecución de la query se lleva a cabo pero el valor de retorno es indefinido.
     */
    function executeInsert($pQuery) {

        $connection = NULL;

        try {
            $connection = $this->getConnection();
            $id = -1;

            if (mysql_query($pQuery, $connection)) {
                $id = mysql_insert_id();
            } else {
                throw new \Exception("SQL Error: " . mysql_error());
            }

            mysql_close($connection);
            return $id;
        } catch (\Exception $pEx) {
            if ($connection) {
                mysql_close($connection);
            }
            throw new \Exception("Fallo al ejecutar el insert.  " . $pEx->getMessage());
        }
    }

    public function getDb_server() {
        return $this->db_server;
    }

    public function setDb_server($db_server) {
        $this->db_server = $db_server;
    }

    public function getDb_name() {
        return $this->db_name;
    }

    public function setDb_name($db_name) {
        $this->db_name = $db_name;
    }

    public function getDb_user() {
        return $this->db_user;
    }

    public function setDb_user($db_user) {
        $this->db_user = $db_user;
    }

    public function getDb_pass() {
        return $this->db_pass;
    }

    public function setDb_pass($db_pass) {
        $this->db_pass = $db_pass;
    }

}

?>
