<?php

namespace flowcode\wing\mvc\exception;

use Exception;

/**
 * Description of EntityDaoException
 *
 * @author Maximiliano Monzón.
 */
class DaoException extends Exception {

    // Redefine the exception so message isn't optional
    public function __construct($message, $entity, $code = 0, Exception $previous = null) {
        // some code
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);

        $this->entity = $entity;
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function getEntity() {
        return $this->entity;
    }

    private $entity;

}

// EntityDaoException
?>
