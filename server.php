<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
ini_set('soap.wsdl_cache_enabled', 0);

require_once('magic.class.php');

$server = new SoapServer("http://localhost/wsdl.php?wsdl", array('soap_version' => SOAP_1_1));
$server->setClass('Magic');
$server->handle(); 
?>
