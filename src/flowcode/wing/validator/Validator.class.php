<?php

namespace flowcode\mvc\validator;

/**
 *
 * @author juanma
 */
interface Validator {
    public function isValid();
    public function validate($field);
    public function getMessage();
}

?>
