<?php

namespace flowcode\mvc\field;

/**
 * Description of SelectField
 *
 * @author juanma
 */
class SelectField extends Field {

    public function __toString() {
        $html = "<select ";
        $html .= "name='" . $this->name . "' ";
        foreach ($this->attributes as $key => $value) {
            $html .= "$key='$value'";
        }
        $html .= ">";

        if (isset($this->options["empty_first"]) && $this->options["empty_first"]) {
            $html .= "<option value=''>Elegir...</option>";
        }

        if (isset($this->options["data"])) {
            foreach ($this->options["data"] as $entity) {
                $html .= "<option value='" . $entity->getId() . "' ";
                if($entity->getId() == $this->value){
                    $html .= " selected='selected' ";
                }
                $html .= ">";
                $html .= $entity->getNombre();
                $html .= "</option>";
            }
        }

        $html .= "</select>";
        return $html;
    }

}

?>
