<?php

namespace flowcode\ceibo\data;

use Exception;
use PDO;
use PDOException;

class PDOMySqlDataSource implements DataSource {

    private $dbDsn = "";
    private $dbUser = "";
    private $dbPass = "";

    public function __construct() {
        
    }

    /**
     * Open a mysql connection.
     * @return type
     * @throws Exception
     */
    function getConnection() {
        try {
            return new PDO($this->getDbDsn(), $this->getDbUser(), $this->getDbPass());
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    /**
     *  Ejecuta una query sin esperar un valor de retorno.
     *      
     */
    function executeNonQuery($query) {
        try {
            $this->getConnection()->exec($query);
        } catch (Exception $ex) {
            throw new Exception("Fallo al ejecutar la query: " . $query . " datasource error: " . $ex->getMessage());
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
            $sth = $this->getConnection()->prepare($query);
            $sth->execute();
            return $sth->fetchAll();
        } catch (Exception $pEx) {
            throw new Exception("Fallo al ejecutar la query: " . $query . "  " . $pEx->getMessage());
        }
    }

    /**
     *  Execute Insert:
     * 
     *      Ejecuta una query de tipo insert devolviendo el id del registro asociado en la tabla.  Si la tabla no posee
     * campo id, la ejecución de la query se lleva a cabo pero el valor de retorno es indefinido.
     */
    function executeInsert($query) {

        try {
            $this->getConnection()->exec($query);
            $id = $this->getConnection()->lastInsertId();
            return $id;
        } catch (Exception $ex) {
            throw new Exception("Fallo al ejecutar el insert.  " . $ex->getMessage());
        }
    }

    public function escapeString($unescaped_string) {
        return mysql_real_escape_string($unescaped_string);
    }

    public function getConnectionString() {
        return $this->connectionString;
    }

    public function setConnectionString($connectionString) {
        $this->connectionString = $connectionString;
    }

    public function getDbDsn() {
        return $this->dbDsn;
    }

    public function setDbDsn($dbDsn) {
        $this->dbDsn = $dbDsn;
    }

    public function getDbUser() {
        return $this->dbUser;
    }

    public function setDbUser($dbUser) {
        $this->dbUser = $dbUser;
    }

    public function getDbPass() {
        return $this->dbPass;
    }

    public function setDbPass($dbPass) {
        $this->dbPass = $dbPass;
    }

}

?>
