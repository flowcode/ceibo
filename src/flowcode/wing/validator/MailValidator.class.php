<?php

namespace flowcode\mvc\validator;

/**
 * Description of MailValidator
 *
 * @author juanma
 */
class MailValidator implements Validator {

    protected $valid = FALSE;

    public function isValid() {
        return $this->valid;
    }

    public function getMessage() {
        return "No válido";
    }

    public function validate($field) {
        $eval = FALSE;
        $str = $field->getValue();
        if (preg_match("/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9-])+(\.[a-zA-Z0-9_-]+)*\.([a-zA-Z]{2,6})$/", $str)) {
            $eval = TRUE;
        }
        $this->valid = $eval;
    }

}

?>
