<?php
/**
 * 簡易IO範本
 */

include_once '../vendor/autoload.php';

$op = (! isset($_REQUEST['op'])) ? "index" : $_REQUEST['op'];

switch($op){
    case "index":
        include_once("pages/form.php");
        break;
    case "export":
    default:
        include_once("pages/export.php");
        
        if(function_exists($op)){
            $op();
        }
        
        break;
    case "import":
        include_once("pages/import.php");
        break;
}