<?php

namespace flowcode\mvc\field;

/**
 * Description of SelectField
 *
 * @author juanma
 */
class RadioButtonField extends Field {

    public function __toString() {
        $html = "";

        if (isset($this->options["data"])) {
            foreach ($this->options["data"] as $key => $value) {
                $html .= "<input type='radio' ";
                if($this->value == $value){
                    $html .= "checked='checked' ";
                }
                $html .= "name='" . $this->name . "' ";
                $html .= "value='" . $value . "'";
                $html .= "/>";
                $html .= "<label>$key</label>";
            }
        } else {
            $html .= "<input type='radio' ";
            $html .= "name='" . $this->name . "' ";
            foreach ($this->attributes as $key => $value) {
                $html .= "$key='$value' ";
            }
            $html .= "value='" . $this->value . "'";
            $html .= "/>";
        }
        return $html;
    }

}

?>
