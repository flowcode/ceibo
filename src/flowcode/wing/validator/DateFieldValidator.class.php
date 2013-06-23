<?php

namespace flowcode\mvc\validator;

/**
 * Description of StringFieldValidator.
 *
 * @author juanma
 */
class DateFieldValidator implements Validator {

    private $maxlength;
    private $minlength;
    private $message;
    private $valid = FALSE;

    public function __construct($maxlength = NULL, $minlength = NULL, $message = NULL) {
        if (!is_null($maxlength)) {
            $this->maxlength = $maxlength;
        }
        if (!is_null($minlength)) {
            $this->minlength = $minlength;
        }
        if (!is_null($message)) {
            $this->message = $message;
        }
    }

    public function isValid() {
        return $this->valid;
    }

    public function getMessage() {
        $msg = "";
        if (empty($this->message)) {
            $msg = "Formato de fecha no válido.[dd/mm/aaaa]";
        } else {
            $msg = $this->message;
        }
        return $msg;
    }

    public function validate($field) {
        $eval = TRUE;
        $str = $field->getValue();
        if (!empty($this->maxlength) && $this->maxlength < strlen($str)) {
            $eval = FALSE;
        }

        if (!empty($this->minlength) && $this->minlength > strlen($str)) {
            $eval = FALSE;
        }
        $this->valid = $eval;
    }

}

?>
