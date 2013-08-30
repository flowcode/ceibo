<?php

namespace flowcode\mvc\validator;

/**
 * Description of StringFieldValidator.
 *
 * @author juanma
 */
class StringFieldValidator implements Validator {

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

    public function validate($field) {
        $eval = TRUE;
        $str = $field->getValue();
        if (!empty($this->maxlength) && $this->maxlength < strlen($str)) {
            $this->message .= "Máximo " . $this->maxlength . " caracteres.";
            $eval = FALSE;
        }

        if (!empty($this->minlength) && $this->minlength > strlen($str)) {
            $this->message .= "Minimo " . $this->minlength . " caracteres.";
            $eval = FALSE;
        }
        $this->valid = $eval;
    }

    public function getMessage() {
        $msg = "";
        if (empty($this->message)) {
            $msg = "Verificar.";
        } else {
            $msg = $this->message;
        }
        return $msg;
    }

}

?>
