<?php

namespace flowcode\wing\utils;

/**
 * Description of PermalinkBuilder
 *
 * @author juanma
 */
class PermalinkBuilder {

    private $inputString;
    private $similarCount;
    private $permalink;

    public function __construct() {
        $this->inputString = "";
        $this->permalink = "";
        $this->similarCount = 0;
    }

    public function build() {
        $permalink = "";
        $search = array(' ', 'á', 'Á', 'é', 'É', 'í', 'Í', 'ó', 'Ó', 'ú', 'Ú', 'ñ', 'Ñ', '.', '´', '!', '?', '¿', ':');
        $replace = array('-', 'a', 'a', 'e', 'e', 'i', 'i', 'o', 'o', 'u', 'u', 'ni', 'ni', '', '', '', '', '-');
        $subject = strtolower($this->inputString);
        $permalink = str_replace($search, $replace, $subject);
        $permalink = preg_replace("/[^A-Za-z0-9 \- ]/", '', $permalink);

        if ($this->similarCount > 0) {
            $permalink .= "-" . ($this->similarCount + 1);
        }
        $this->permalink = $permalink;
    }

    public function setInputString($inputString) {
        $this->inputString = $inputString;
    }

    public function setSimilarCount($similarCount) {
        $this->similarCount = $similarCount;
    }

    public function getPermalink() {
        return $this->permalink;
    }

}

?>
