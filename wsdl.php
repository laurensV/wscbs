<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once('WSDL/class.phpwsdl.php');
require_once('calculator.class.php');

PhpWsdl::RunQuickMode('calculator.class.php');
?>
