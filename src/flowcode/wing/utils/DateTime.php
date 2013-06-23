<?php

namespace flowcode\mvc\utils;

/**
 * Description of DateTime
 *
 * @author juanma
 */
class DateTime {

    /**
     * Get the month name by its number.
     * @param type $number 
     */
    public static function numberToName($number) {
        $index = intval($number);
        $months = array();
        $months["1"] = "enero";
        $months["2"] = "febrero";
        $months["3"] = "marzo";
        $months["4"] = "abril";
        $months["5"] = "mayo";
        $months["6"] = "junio";
        $months["7"] = "julio";
        $months["8"] = "agosto";
        $months["9"] = "septiembre";
        $months["10"] = "octubre";
        $months["11"] = "noviembre";
        $months["12"] = "diciembre";
        return $months[$index];
    }

}

?>
