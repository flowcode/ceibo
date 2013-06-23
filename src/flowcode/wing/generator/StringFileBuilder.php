<?php

namespace flowcode\wing\generator;

use flowcode\orm\domain\Mapper;

/**
 * Description of StringFileBuilder
 *
 * @author Juan Manuel Agüero <jaguero@flowcode.com.ar>
 */
class StringFileBuilder {

    /**
     * 
     * @param Mapper $mapper
     * @return string
     */
    public static function getDaoFileString(Mapper $mapper, $module) {
        $fileString = '<?php

namespace flowcode\\' . $module . '\dao;

use ' . $mapper->getClass() . ';
use flowcode\orm\EntityManager;

/**
 * Description of ' . ucfirst($mapper->getName()) . 'Dao
 *
 * @author juanma
 */
class ' . ucfirst($mapper->getName()) . 'Dao {

    public function __construct() {
        
    }

    public function save(' . ucfirst($mapper->getName()) . ' $' . $mapper->getName() . ') {
        EntityManager::getInstance()->save($' . $mapper->getName() . ');
    }

    public function findAll() {
        return EntityManager::getInstance()->findAll("' . $mapper->getName() . '");
    }

    public function delete(' . ucfirst($mapper->getName()) . ' $' . $mapper->getName() . ') {
        EntityManager::getInstance()->delete($' . $mapper->getName() . ');
    }

    public function findById($id) {
        return EntityManager::getInstance()->findById("' . $mapper->getName() . '", $id);
    }

    public function findByFilter($filter = null, $page = 1) {
        $em = EntityManager::getInstance();
        $pager = $em->findByGenericFilter("' . $mapper->getName() . '", $filter, $page);
        return $pager;
    }
    ';
        foreach ($mapper->getRelations() as $relation) {
            $fileString .='
    public function find' . $relation->getName() . '(' . ucfirst($mapper->getName()) . ' $' . $mapper->getName() . ') {
        $em = EntityManager::getInstance();
        $entitys = $em->findRelation($' . $mapper->getName() . ', "' . $relation->getName() . '");
        return $entitys;
    }
';
        }

        $fileString .='

}

?>
';
        return $fileString;
    }

    /**
     * 
     * @param Mapper $mapper
     * @return string
     */
    public static function getServiceFileString(Mapper $mapper, $module) {
        $fileString = '<?php

namespace flowcode\\' . $module . '\service;

use flowcode\\' . $module . '\dao\\' . ucfirst($mapper->getName()) . 'Dao;
use flowcode\\' . $module . '\domain\\' . ucfirst($mapper->getName()) . ';

/**
 * Description of ' . ucfirst($mapper->getName()) . 'Service
 *
 * @author juanma
 */
class ' . ucfirst($mapper->getName()) . 'Service {
    private $' . $mapper->getName() . 'Dao;
    
    public function __construct() {
        $this->' . $mapper->getName() . 'Dao = new ' . ucfirst($mapper->getName()) . 'Dao();
    }
    
    public function findAll(){
        return $this->' . $mapper->getName() . 'Dao->findAll();
    }
    
    public function save(' . ucfirst($mapper->getName()) . ' $' . $mapper->getName() . '){
        $this->' . $mapper->getName() . 'Dao->save($' . $mapper->getName() . ');
    }
    
    public function delete(' . ucfirst($mapper->getName()) . ' $' . $mapper->getName() . '){
        $this->' . $mapper->getName() . 'Dao->delete($' . $mapper->getName() . ');
    }
    
    
    public function findById($id){
        return $this->' . $mapper->getName() . 'Dao->findById($id);
    }
    
    /**
     * Find all ' . $mapper->getName() . 's by filter.
     * @return \flowcode\wing\utils\Pager. 
     */
    public function findByFilter($filter = null, $page = 1) {
        $pager = $this->' . $mapper->getName() . 'Dao->findByFilter($filter, $page);
        return $pager;
    }
    ';
        foreach ($mapper->getRelations() as $relation) {
            $fileString .='
    public function find' . $relation->getName() . '(' . ucfirst($mapper->getName()) . ' $' . $mapper->getName() . ') {
        return $this->' . $mapper->getName() . 'Dao->find' . $relation->getName() . '($' . $mapper->getName() . ');
    }
';
        }

        $fileString .='
    
}

?>
';
        return $fileString;
    }

    /**
     * Retorna un string con el contenido de la clase del controller.
     * @param \flowcode\orm\domain\Mapper $mapper
     * @param type $module
     * @return string
     */
    public static function getAdminControllerFileString(Mapper $mapper, $module) {
        $fileString = '';
        $fileString .= '<?php

namespace flowcode\\' . $module . '\controller;

use flowcode\\' . $module . '\domain\\' . ucfirst($mapper->getName()) . ';
use flowcode\\' . $module . '\service\\' . ucfirst($mapper->getName()) . 'Service;';

        foreach ($mapper->getRelations() as $relation) {
            $fileString .='
use flowcode\\' . $module . '\service\\' . ucfirst($relation->getEntity()) . 'Service;
use flowcode\\' . $module . '\domain\\' . ucfirst($relation->getEntity()) . ';';
        }

        $fileString .='
use flowcode\wing\mvc\Controller;
use flowcode\wing\mvc\HttpRequest;
use flowcode\wing\mvc\View;

/**
 * Description of Admin' . ucfirst($mapper->getName()) . '
 *
 * @author juanma
 */
class Admin' . ucfirst($mapper->getName()) . 'Controller extends Controller {

    private $' . $mapper->getName() . 'Service;

    function __construct() {
        $this->setIsSecure(TRUE);
        $this->addPermission("admin-login");
        $this->' . $mapper->getName() . 'Service = new ' . ucfirst($mapper->getName()) . 'Service();
    }

    function index(HttpRequest $httpRequest) {

        $viewData["filter"] = $httpRequest->getParameter("search");
        $viewData["page"] = $httpRequest->getParameter("page");
        if (is_null($viewData["page"]) || empty($viewData["page"])) {
            $viewData["page"] = 1;
        }
        $viewData["pager"] = $this->' . $mapper->getName() . 'Service->findByFilter($viewData["filter"], $viewData["page"]);

        return View::getControllerView($this, "' . $module . '/view/admin/' . $mapper->getName() . 'List", $viewData);
    }

    function create(HttpRequest $HttpRequest) {
        $viewData["' . $mapper->getName() . '"] = new ' . ucfirst($mapper->getName()) . '();';

        foreach ($mapper->getRelations() as $relation) {
            $fileString .='
        $' . $relation->getEntity() . 'Srv = new ' . ucfirst($relation->getEntity()) . 'Service();
        $viewData["' . lcfirst($relation->getName()) . '"] = $' . $relation->getEntity() . 'Srv->findAll();';
        }

        $fileString .='
        
        return View::getControllerView($this, "' . $module . '/view/admin/' . $mapper->getName() . 'Form", $viewData);
    }

    function save(HttpRequest $httpRequest) {

        /* creo la nueva instancia y seteo valores */
        $' . $mapper->getName() . ' = new ' . ucfirst($mapper->getName()) . '();
        ';

        /* generate from propertys */
        foreach ($mapper->getPropertys() as $property) {
            $fileString .= '$' . $mapper->getName() . '->set' . $property->getName() . '($httpRequest->getParameter("' . $property->getColumn() . '"));
        ';
        }
        foreach ($mapper->getRelations() as $relation) {
            $fileString .= '
        $' . lcfirst($relation->getName()) . ' = array();
        if (isset($_POST["' . lcfirst($relation->getName()) . '"])) {
            foreach ($_POST["' . lcfirst($relation->getName()) . '"] as $id' . lcfirst($relation->getEntity()) . ') {
                $' . $relation->getEntity() . ' = new ' . ucfirst($relation->getEntity()) . '();
                $' . $relation->getEntity() . '->setId($id' . lcfirst($relation->getEntity()) . ');
                $' . lcfirst($relation->getName()) . '[] = $' . $relation->getEntity() . ';
            }
        }
        $' . $mapper->getName() . '->set' . $relation->getName() . '($' . lcfirst($relation->getName()) . ');';
        }

        $fileString .= '
        // la guardo
        $id = $this->' . $mapper->getName() . 'Service->save($' . $mapper->getName() . ');

        $viewData["response"] = "success";
        return View::getControllerView($this, "cms/view/admin/form-response", $viewData);
    }

    function edit(HttpRequest $HttpRequest) {

        // en el primer parametro tiene que venir el id
        $params = $HttpRequest->getParams();
        $id = $params[0];

        $viewData["' . $mapper->getName() . '"] = $this->' . $mapper->getName() . 'Service->findById($id);';
        foreach ($mapper->getRelations() as $relation) {
            $fileString .='
        $' . $relation->getEntity() . 'Srv = new ' . ucfirst($relation->getEntity()) . 'Service();
        $viewData["' . lcfirst($relation->getName()) . '"] = $' . $relation->getEntity() . 'Srv->findAll();';
        }

        $fileString .='

        return View::getControllerView($this, "' . $module . '/view/admin/' . $mapper->getName() . 'Form", $viewData);
    }

    function delete($HttpRequest) {
        // en el primer parametro tiene que venir el id
        $params = $HttpRequest->getParams();
        $id = $params[0];

        $' . $mapper->getName() . ' = $this->' . $mapper->getName() . 'Service->findById($id);
        $this->' . $mapper->getName() . 'Service->delete($' . $mapper->getName() . ');

        $viewData["response"] = "success";
        return View::getControllerView($this, "cms/view/admin/form-response", $viewData);
    }

}
?>
';
        return $fileString;
    }

    public static function getDomainFileString(Mapper $mapper, $module) {
        $fileString = '';
        $fileString .= '<?php

namespace flowcode\\' . $module . '\domain;

use flowcode\\' . $module . '\service\\' . ucfirst($mapper->getName()) . 'Service;
use flowcode\wing\mvc\Entity;

/**
 * Description of ' . ucfirst($mapper->getName()) . '
 *
 * @author Juan Manuel Agüero <jaguero@flowcode.com.ar>
 */
class ' . ucfirst($mapper->getName()) . ' extends Entity {
    
            ';
        /* generate from propertys */
        foreach ($mapper->getPropertys() as $property) {
            if ($property->getColumn() != "id") {
                $fileString .= '
    private $' . $property->getColumn() . ';';
            }
        }
        foreach ($mapper->getRelations() as $relation) {
            $fileString .='
    private $' . lcfirst($relation->getName()) . ';';
        }
        $fileString .='
            
    function __construct() {
        parent::__construct();
        ';
        foreach ($mapper->getRelations() as $relation) {
            $fileString .='
        $this->' . lcfirst($relation->getName()) . ' = NULL;';
        }

        $fileString .='
    }
    ';
        /* generate from propertys */
        foreach ($mapper->getPropertys() as $property) {
            if ($property->getColumn() != "id") {
                $fileString .= '
    public function get' . $property->getName() . '() {
        return $this->' . $property->getColumn() . ';
    }

    public function set' . $property->getName() . '($' . $property->getColumn() . ') {
        $this->' . $property->getColumn() . ' = $' . $property->getColumn() . ';
    }';
            }
        }

        foreach ($mapper->getRelations() as $relation) {
            $fileString .='
                
    public function get' . $relation->getName() . '() {
        if ($this->' . lcfirst($relation->getName()) . ' == NULL) {
            $' . $mapper->getName() . 'Srv = new ' . ucfirst($mapper->getName()) . 'Service();
            $this->' . lcfirst($relation->getName()) . ' = $' . $mapper->getName() . 'Srv->find' . $relation->getName() . '($this);
        }
        return $this->' . lcfirst($relation->getName()) . ';
    }

    public function set' . $relation->getName() . '($' . lcfirst($relation->getName()) . ') {
        $this->' . lcfirst($relation->getName()) . ' = $' . lcfirst($relation->getName()) . ';
    }';
        }

        if ($mapper->getProperty("Name") != NULL) {
            $fileString .='
                
    public function __toString() {
        return $this->name;
    }';
        }
        $fileString .='

}
?>

';
        return $fileString;
    }

}

?>
