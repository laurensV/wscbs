<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
ini_set('soap.wsdl_cache_enabled', 0);

require_once('calculator.class.php');

$server = new SoapServer("http://localhost/calculator/wsdl.php?wsdl", array('soap_version' => SOAP_1_2));
$server->setClass('Calculator');
$server->handle(); 
?>
