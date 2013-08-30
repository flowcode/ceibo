<?php

namespace flowcode\wing\generator;

/**
 * Description of FSHelper
 *
 * @author Juan Manuel Agüero <jaguero@flowcode.com.ar>
 */
class FSHelper {

    public static function writeToFile($fileLocation, $fileString) {
        $fp = fopen($fileLocation, "w+");
        if ($fp == false) {
            die("No se ha podido crear el archivo: $fileLocation");
        }
        fwrite($fp, $fileString);
        fclose($fp);
    }

    public static function buildModuleStructure($module) {
        $success = true;

        $moduleLocation = __DIR__ . "/../../$module";
        if (!is_dir($moduleLocation)) {
            mkdir($moduleLocation);
        }

        $moduleDaoLocation = $moduleLocation . "/dao";
        if (!is_dir($moduleDaoLocation)) {
            mkdir($moduleDaoLocation);
        }

        $moduleServiceLocation = $moduleLocation . "/service";
        if (!is_dir($moduleServiceLocation)) {
            mkdir($moduleServiceLocation);
        }

        $moduleDomainLocation = $moduleLocation . "/domain";
        if (!is_dir($moduleDomainLocation)) {
            mkdir($moduleDomainLocation);
        }

        $moduleControllerLocation = $moduleLocation . "/controller";
        if (!is_dir($moduleControllerLocation)) {
            mkdir($moduleControllerLocation);
        }


        $moduleViewLocation = $moduleLocation . "/view";
        if (!is_dir($moduleViewLocation)) {
            mkdir($moduleViewLocation);
        }
        $moduleViewLocation2 = $moduleLocation . "/view/admin";
        if (!is_dir($moduleViewLocation2)) {
            mkdir($moduleViewLocation2);
        }
        $moduleConfigLocation = $moduleLocation . "/config";
        if (!is_dir($moduleConfigLocation)) {
            mkdir($moduleConfigLocation);
        }

        return $success;
    }

}

?>
