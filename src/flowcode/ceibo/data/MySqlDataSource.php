<?php

namespace flowcode\ceibo\data;

class MySqlDataSource implements DataSource {

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

    /**
     * Open a mysql connection.
     * @return type
     * @throws \Exception
     */
    function getConnection() {
        try {
            $con = mysql_connect($this->db_server, $this->db_user, $this->db_pass);

            if (!$con) {
                throw new \Exception("Could not connect: " . mysql_error());
            }

            mysql_select_db($this->db_name, $con);
            mysql_query("SET NAMES 'utf8'");
            return $con;
        } catch (\Exception $pEx) {
            throw new \Exception("Fallo al obtener la conexion. " . $pEx->getMessage());
        }
    }

    /**
     *  Ejecuta una query sin esperar un valor de retorno.
     *      
     */
    function executeNonQuery($query) {

        try {
            $connection = $this->getConnection();

            if (!mysql_query($query, $connection)) {
                mysql_close($connection);
                throw new \Exception("SQL Error: " . mysql_error());
            }

            mysql_close($connection);
        } catch (\Exception $pEx) {
            throw new \Exception("Fallo al ejecutar la query: " . $query . $pEx->getMessage());
        }
    }

    /**
     * Execute Query:
     * 
     *      Ejecuta una query devolviendo la tabla como resultado.  En caso de
     * no traer valores, retorna false.  En caso de error, muere infceiboando el 
     * error.
     */
    function executeQuery($query) {

        try {
            $table = FALSE;

            if (!is_null($query) && is_string($query)) {
                $connection = $this->getConnection();
                $resultQry = mysql_query($query, $connection);

                if ($resultQry) {
                    if (mysql_num_rows($resultQry) != 0) {
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
                        }
                        $table = array();

                        while ($fila = mysql_fetch_assoc($resultQry)) {
                            $rowAux = $rowTemplate;

                            foreach ($rowAux as $k => $v) {
                                $rowAux[$k] = $fila[$k];
                            }

                            $table[] = $rowAux;
                        }

                        mysql_free_result($resultQry);
                    }
                    mysql_close($connection);
                } else {
                    throw new \Exception("SQL Error: " . mysql_error());
                }
            }

            return $table;
        } catch (\Exception $pEx) {
            throw new \Exception("Fallo al ejecutar la query: " . $query . "  " . $pEx->getMessage());
        }
    }

    /**
     *  Execute Insert:
     * 
     *      Ejecuta una query de tipo insert devolviendo el id del registro asociado en la tabla.  Si la tabla no posee
     * campo id, la ejecución de la query se lleva a cabo pero el valor de retorno es indefinido.
     */
    function executeInsert($query) {

        $connection = NULL;
        try {
            $connection = $this->getConnection();
            $id = -1;

            if (mysql_query($query, $connection)) {
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
