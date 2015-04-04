<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once('WSDL/class.phpwsdl.php');
require_once('magic.class.php');

/*$soap=PhpWsdl::CreateInstance();
$wsdl=$soap->CreateWsdl();
$html=$soap->OutputHtml(false,false);*/
PhpWsdl::RunQuickMode('magic.class.php');

?>
