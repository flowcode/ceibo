<?php

namespace flowcode\ceibo\data;

use flowcode\ceibo\support\TestCase;
use PDO;


/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PDOMySqlDataSourceTest
 *
 * @author Juan Manuel Agüero <jaguero@flowcode.com.ar>
 * @group database
 */
class PDOMySqlDataSourceTest extends TestCase {

    protected $instance;
    protected $dbh;

    protected function setUp() {

        /* setup connection */
        $this->instance = new PDOMySqlDataSource();
        $this->instance->setDbDsn("mysql:host=localhost;dbname=wing-ceibo-test*");
        $this->instance->setDbUser("root");
        $this->instance->setDbPass("root");

        /* setup database */
        $this->dbh = new PDO("mysql:host=localhost;dbname=wing-ceibo-test", "root", "root");
        $this->dbh->exec("drop table `ovni`;");
        $this->dbh->exec("drop table `ovni_weapon`;");
        $this->dbh->exec("drop table `weapon`;");
        $this->dbh->exec("CREATE  TABLE `ovni` (`id` INT NOT NULL AUTO_INCREMENT ,`name` VARCHAR(45) NULL ,PRIMARY KEY (`id`) )ENGINE = InnoDB;");
        $this->dbh->exec("CREATE  TABLE `weapon` (`id` INT NOT NULL AUTO_INCREMENT ,`name` VARCHAR(45) NULL , PRIMARY KEY (`id`) )ENGINE = InnoDB;");
        $this->dbh->exec("CREATE  TABLE `ovni_weapon` (`id_ovni` INT NOT NULL ,`id_weapon` INT NOT NULL , PRIMARY KEY (`id_ovni`, `id_weapon`) )ENGINE = InnoDB;");

        $stmt = $this->dbh->prepare("INSERT INTO ovni (id, name) VALUES(?,?)");
        $this->dbh->beginTransaction();
        $stmt->execute(array('', 'ovni1'));
        $this->dbh->commit();
    }

    public function testExecuteQuery_select_success() {

        $query = "select * from ovni";
        $raw = $this->instance->executeQuery($query);

        $this->getConnection()->createQueryTable('ovni', $query);

        $this->assertEquals(1, count($raw));
        $this->assertEquals(count($raw), $this->getConnection()->getRowCount('ovni'));
    }

    public function testExecuteQuery_countResult_success() {

        $raw = $this->instance->executeQuery("SELECT COUNT(*) as count FROM ovni;");
        $this->assertEquals(1, $raw[0]['count']);
    }

    public function testExecuteNonQuery_select_success() {
        $raw = $this->instance->executeNonQuery("truncate table ovni");

        $this->getConnection()->createQueryTable('ovni', "select * from ovni");

        $this->assertEquals(0, count($raw));
        $this->assertEquals(count($raw), $this->getConnection()->getRowCount('ovni'));
    }

    protected function tearDown() {
        
    }

    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet() {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/_files/expected.xml');
    }

}

?>
