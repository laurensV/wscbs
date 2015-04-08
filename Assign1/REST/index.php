<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

if($db = new SQLite3('urls.db')) {
	$q = $db->query('CREATE TABLE IF NOT EXISTS urls (id INT AUTO_INCREMENT, url VARCHAR(255), PRIMARY KEY (id))');

	switch($_SERVER['REQUEST_METHOD']) {
		case "GET":
			$q = "SELECT * FROM urls";
			if(isset($_GET['id'])) {
				$q .= " WHERE id=\"" . $_GET['id'] . "\"";
			}
			$r = $db->query($q);
			if($r) {
				while ($entry = $r->fetchArray()) {
					if(isset($_GET['id'])) {
						header("HTTP/1.1 301 Moved");
						header("Location: " . $entry['url']);
						die();
					}
					echo $entry['id'] .  "\n";
				}
			}
			if(isset($_GET['id'])) {
				header("HTTP/1.1 404");
			}
			break;
		case "POST":
			if(isset($_POST['url']) && filter_var($_POST['url'], FILTER_VALIDATE_URL)) {
				if(!$db->query("INSERT INTO urls (url) VALUES(\"" . $_POST['url'] . "\")")) {
					die($db->lastErrorMsg());
				}
				$r = $db->query("SELECT id FROM urls WHERE url=\"" . $_POST['url'] . "\"");
				$row = $r->fetchArray();
				if(!$row) {
					die($db->lastErrorMsg());
				}
				echo $row['id'];
			}
			break;
		case "DELETE":

			break;
		case "PUT":

			break;
		default:
	}

} else {
	die($db->lastErrorMsg());
}
?>
