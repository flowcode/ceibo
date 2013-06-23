<?php

namespace flowcode\mvc\form;

/**
 * Description of Form
 *
 * @author juanma
 */
class Form {

    protected $fields;
    protected $validators;
    protected $errors;

    public function __construct() {
        $this->fields = array();
        $this->validators = array();
        $this->errors = array();
    }

    public function getField($fieldName) {
        $value = "";
        if (isset($this->fields[$fieldName])) {
            $value = $this->fields[$fieldName];
        }
        return $value;
    }

    public function getFieldError($fieldName) {
        $value = "";
        if (isset($this->errors[$fieldName])) {
            $value = $this->errors[$fieldName];
        }
        return $value;
    }

    public function addField($fieldname, $field, $validator = NULL) {
        if (!is_null($field)) {
            $this->fields[$fieldname] = $field;
            $field->setName($fieldname);
        }
        if (!is_null($validator)) {
            $this->addValidator($fieldname, $validator);
        }
    }

    /**
     * Add a Validator for a field correspondant to its name.
     * @param String $fieldname
     * @param Validator $validator 
     */
    public function addValidator($fieldname, $validator) {
        $this->validators[$fieldname][] = $validator;
    }
    
    public function removeValidator($fieldname){
        unset($this->validators[$fieldname]);
    }

    /**
     * Verify each field with its correspondant validator, if exist.
     * @return boolean 
     */
    public function isValid() {
        $valid = TRUE;
        foreach ($this->fields as $key => $field) {
            
            // required
            if ($field->getOption("required") && $field->getValue() == "") {
                $this->errors[$key] = "Requerido";
                $valid = FALSE;
            }
            
            // custom validators
            if (isset($this->validators[$key])) {

                foreach ($this->validators[$key] as $validator) {

                    $validator->validate($field);
                    if (!$validator->isValid()) {
                        $this->errors[$key] = $validator->getMessage();
                        $valid = FALSE;
                    }
                }
            }
        }
        return $valid;
    }

    /**
     * Bind each array value with the form field.
     * @param type $array 
     */
    public function bind($array) {
        foreach ($array as $fieldname => $value) {
            if($this->getField($fieldname) != "")
                $this->getField ($fieldname)->setValue($value);
        }
    }

}

?>
