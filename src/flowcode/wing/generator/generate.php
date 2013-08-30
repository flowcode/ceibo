<?php

namespace flowcode\wing\generator;

use flowcode\orm\builder\MapperBuilder;

require_once (__DIR__ . '/../mvc/Autoloader.php');

if (count($argv) < 2) {
    fwrite(STDOUT, "Ingrese el nombre de la entidad, ejemplo: noticia \n");
    die();
}

$name = $argv[2];
$module = $argv[1];
fwrite(STDOUT, "Creando estructura de directorios para el modulo: $module \n");
if (FSHelper::buildModuleStructure($module)) {
    fwrite(STDOUT, "Estructura de directorios creada. \n");
} else {
    fwrite(STDOUT, "No fue posible crear los directorios. \n");
    die();
}

fwrite(STDOUT, "Obteniendo mapper para: $name \n");

/* obtengo el mapper */
$mappingFilePath = dirname(__FILE__) . "/../../../../orm-mapping.xml";
$mapping = simplexml_load_file($mappingFilePath);
$mapper = MapperBuilder::buildFromName($mapping, $name);

fwrite(STDOUT, "Mapper obtenido. \n");

fwrite(STDOUT, "Generando archivos... \n");
/* construyo el dao */
$fileDaoLocation = __DIR__ . "/../../$module/dao/" . ucfirst($mapper->getName()) . "Dao.php";
$fileDaoString = StringFileBuilder::getDaoFileString($mapper, $module);
fwrite(STDOUT, "Generado: $fileDaoLocation \n");

FSHelper::writeToFile($fileDaoLocation, $fileDaoString);

/* construyo el service */
$fileServiceLocation = __DIR__ . "/../../$module/service/" . ucfirst($mapper->getName()) . "Service.php";
$serviceFileString = StringFileBuilder::getServiceFileString($mapper, $module);

fwrite(STDOUT, "Generado: $fileServiceLocation \n");
FSHelper::writeToFile($fileServiceLocation, $serviceFileString);

/* construyo el adminController */
$fileAdminControllerLocation = __DIR__ . "/../../$module/controller/Admin" . ucfirst($mapper->getName()) . "Controller.php";
$adminControllerFileString = StringFileBuilder::getAdminControllerFileString($mapper, $module);

fwrite(STDOUT, "Generado: $fileAdminControllerLocation \n");
FSHelper::writeToFile($fileAdminControllerLocation, $adminControllerFileString);

/* construyo la entidad de dominio */
$fileDomainLocation = __DIR__ . "/../../$module/domain/" . ucfirst($mapper->getName()) . ".php";
$domainFileString = StringFileBuilder::getDomainFileString($mapper, $module);

fwrite(STDOUT, "Generado: $fileDomainLocation \n");
FSHelper::writeToFile($fileDomainLocation, $domainFileString);

/* construyo la vista del formulario de la entidad */
$fileFormViewLocation = __DIR__ . "/../../$module/view/admin/" . $mapper->getName() . "Form.view.php";
$viewFormFileString = StringViewFileBuilder::getDomainForm($mapper, $module);

fwrite(STDOUT, "Generado: $fileFormViewLocation \n");
FSHelper::writeToFile($fileFormViewLocation, $viewFormFileString);

/* construyo la vista de lista de la entidad */
$listViewFileLocation = __DIR__ . "/../../$module/view/admin/" . $mapper->getName() . "List.view.php";
$listViewFileString = StringViewFileBuilder::getDomainList($mapper, $module);

fwrite(STDOUT, "Generado: $listViewFileLocation \n");
FSHelper::writeToFile($listViewFileLocation, $listViewFileString);

fwrite(STDOUT, "Proceso terminado con exito :) \n");
?>
