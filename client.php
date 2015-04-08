<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

$error = array();

if($_SERVER['REQUEST_METHOD'] == "POST") {
	$client = new SoapClient("http://localhost/wsdl.php?wsdl", array('soap_version' => SOAP_1_2));
	if(isset($_POST['a']) && strlen($_POST['a']) != 0) {
		$a = floatval($_POST['a']);
	} else {
		$error[] = "No value a set";
	}

	if(isset($_POST['b']) && strlen($_POST['b']) != 0) {
		$b = floatval($_POST['b']);
	} else {
		$error[] = "No value b set";
	}

	if(!isset($error) || count($error)  == 0) {
		if(isset($_POST['add'])) {
			echo  $client->add($a, $b);
		}elseif(isset($_POST['sub'])) {
			echo  $client->sub($a, $b);
		}elseif(isset($_POST['mul'])) {
			echo  $client->mul($a, $b);
		}elseif(isset($_POST['div'])) {
			echo  $client->div($a, $b);
		}
	}
	
}
	

if(isset($error) && count($error) > 0) {
	foreach($error as $v) {
		echo "<span style=\"color: red;\">" . $v . "</span><br />";
	}
}
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
Value a<input type="number" name="a" step="any" /><br />
value b<input type="number" name="b" step="any" /><br />
<input type="submit" name="add" value="add" />
<input type="submit" name="sub" value="sub" />
<input type="submit" name="mul" value="mul" />
<input type="submit" name="div" value="div" />
</form>
