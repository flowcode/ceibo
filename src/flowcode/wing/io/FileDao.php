<?php

namespace flowcode\mvc\io;

/**
 * Description of FileDao
 *
 * @author juanma
 */
class FileDao {

    protected $file;

    function __construct($file = null) {
        if (is_null($file)) {
            $this->file = "dynamic-conf.xml";
        } else {
            $this->file = $file;
        }
    }

    public function save($array) {
        try {
            $doc = new \DOMDocument();
            $doc->formatOutput = true;

            $r = $doc->createElement("root");

            foreach ($array as $key => $value) {
                $name = $doc->createElement($key);
                $name->appendChild(
                        $doc->createTextNode($value)
                );
                $r->appendChild($name);
            }
            $doc->appendChild($r);

            $doc->save($this->file);
        } catch (\Exception $e) {
            throw new IOException("No se puedo escribir el archivo: " . $e->getMessage());
        }
    }

    public function getValue($key) {
        $value = NULL;
        $doc = new \DOMDocument('1.0');
        $doc->load($this->file);

        $root = $doc->getElementsByTagName("root");
        foreach ($root as $element) {
            $names = $element->getElementsByTagName($key);
            if (count($names) > 0) {
                $value = $names->item(0)->nodeValue;
                break;
            }
        }

        return $value;
    }

    public function getAll() {
        $values = array();
        $doc = new \DOMDocument('1.0');
        $doc->load($this->file);

        $root = $doc->getElementsByTagName("root");
        foreach ($root->item(0)->childNodes as $nodename) {
            if ($nodename->nodeName != "#text")
                $values[$nodename->nodeName] = $nodename->nodeValue;
        }

        return $values;
    }

    public function getFile() {
        return $this->file;
    }

    public function setFile($file) {
        $this->file = $file;
    }

}

?>
