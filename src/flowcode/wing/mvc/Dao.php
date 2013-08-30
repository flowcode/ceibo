<?php

namespace flowcode\wing\mvc;

use Exception;
use flowcode\wing\mvc\Config;
use flowcode\wing\mvc\exception\DaoException;

/**
 * Description of Dao
 *
 * @author juanma
 */
abstract class Dao {

    private $db_server = "server";
    private $db_name = "dbname";
    private $db_user = "user";
    private $db_pass = "pass";

    public function __construct() {
        $this->db_server = Config::get('database', 'dbserver');
        $this->db_name = Config::get('database', 'dbname');
        $this->db_user = Config::get('database', 'dbuser');
        $this->db_pass = Config::get('database', 'dbpass');
    }

    /**
     * Return an instance from a raw array of data.
     */
    public abstract function getInstanceFormArray($raw);

    /**
     * 
     * @return type
     * @throws Exception
     */
    function getConnection() {
        try {
            $con = mysql_connect($this->db_server, $this->db_user, $this->db_pass);

            if (!$con) {
                throw new Exception("Could not connect: " . mysql_error());
            }

            mysql_select_db($this->db_name, $con);
            mysql_query("SET NAMES 'utf8'");
            return $con;
        } catch (Exception $pEx) {
            throw new DaoException("Fallo al obtener la conexion. " . $pEx->getMessage());
        }
    }

    /**
     * Executes a query without returning value.
     * @param type $query
     * @throws DaoException exception.
     */
    function executeNonQuery($query) {

        try {
            $connection = $this->getConnection();

            if (!mysql_query($query, $connection)) {
                mysql_close($connection);
                throw new DaoException("SQL Error: " . mysql_error());
            }

            mysql_close($connection);
        } catch (Exception $pEx) {
            throw new DaoException("Fallo al ejecutar la query: " . $query . $pEx->getMessage());
        }
    }

    function executeQuery($query) {
        $collection = array();
        try {
            if (!is_null($query) && is_string($query)) {
                $connection = $this->getConnection();
                $resultQry = mysql_query($query, $connection);

                if ($resultQry) {
                    if (mysql_num_rows($resultQry) != 0) {

                        while ($raw = mysql_fetch_array($resultQry)) {
                            $collection[] = $this->getInstanceFormArray($raw);
                        }
                        mysql_free_result($resultQry);
                    }
                    mysql_close($connection);
                } else {
                    throw new DaoException("SQL Error: " . mysql_error());
                }
            }
            return $collection;
        } catch (Exception $pEx) {
            throw new DaoException("Execution failed: " . $query . "  " . $pEx->getMessage());
        }
    }

    function executeInsert($query) {
        $connection = NULL;
        try {
            $connection = $this->getConnection();
            $id = NULL;
            if (mysql_query($query, $connection)) {
                $id = mysql_insert_id();
            } else {
                throw new DaoException("SQL Error: " . mysql_error());
            }
            mysql_close($connection);
            return $id;
        } catch (Exception $pEx) {
            if ($connection) {
                mysql_close($connection);
            }
            throw new DaoException("Failed on Insert execution.  " . $pEx->getMessage());
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
