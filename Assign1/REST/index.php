<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

if($db = new SQLiteDatabase('filename')) {
	$q = @$db->query('CREATE TABLE IF NOT EXISTS urls (id TEXT, url TEXT, PRIMARY KEY (id))';

	switch($_SERVER['REQUEST_METHOD']) {
		case "GET":
			$q = "SELECT * FROM urls";
			if(isset($_GET['id'])) {
				$q .= " WHERE id=\"" . $_GET['id'] . "\"";
			}
			$r = $db->query($q);
			if($r) {
				while ($entry = sqlite_fetch_array($r, SQLITE_ASSOC)) {
					if(isset($_GET['id'])) {
						header("HTTP/1.1 301 Moved");
						header("Location: " . $entry['url']);
						die();
					}
					var_dump($entry);
				}
			}
			break;
		case "POST":
			if(isset($_POST['url']) && filter_var($_POST['url'], FILTER_VALIDATE_URL)) {
				$id = 9001;
				if(!$db->query("INSERT INTO urls (id, url) VALUES(\"" . $id . "\", \"" . $_POST['url'] . "\")")) {
					die($db->sqlite_last_error());
				}
			}
			break;
		case "DELETE":

			break;
		case "PUT":

			break;
		default:
	}

} else {
	die($db->sqlite_last_error());
}
?>
