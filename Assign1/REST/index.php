<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

if($db = new SQLite3('urls.db')) {
	$db->query('CREATE TABLE IF NOT EXISTS urls (id INTEGER PRIMARY KEY NOT NULL, url TEXT NOT NULL)');

	switch($_SERVER['REQUEST_METHOD']) {
		case "GET":
			$q = "SELECT * FROM urls";
			if(isset($_GET['id'])) {
				$q .= " WHERE id=\"" . $_GET['id'] . "\"";
			}
			$r = $db->query($q);
			if($r) {
				$response = "";
				while ($entry = $r->fetchArray()) {
					if(isset($_GET['id'])) {
						header("HTTP/1.1 301 Moved");
						header("Location: " . $entry['url']);
						die();
					}
					$response .= $entry['id'] .  ",";
				}
				if(strlen($response) > 0) {
					echo substr($response, 0, -1);
				}
			}

			if(isset($_GET['id'])) {
				header("HTTP/1.1 404");
			}
			
			break;
		case "POST":
			if(isset($_POST['url']) && filter_var($_POST['url'], FILTER_VALIDATE_URL)) {
				$r = $db->query("SELECT id FROM urls WHERE url=\"" . $_POST['url'] . "\"");
				$row = $r->fetchArray();
				// URL does not exists in the database yet, insert it now to get a fresh id.
				if(!$row) {
					if(!$db->query("INSERT INTO urls (url) VALUES(\"" . $_POST['url'] . "\")")) {
						header("HTTP/1.1 500");
						die($db->lastErrorMsg());
					}
					// Retrieve the id created by AUTO_INCREMENT.
					$r = $db->query("SELECT id FROM urls WHERE url=\"" . $_POST['url'] . "\"");
					$row = $r->fetchArray();
					if(!$row) {
						header("HTTP/1.1 500");
						die($db->lastErrorMsg());
					}
				}
				header("HTTP/1.1 201");
				echo $row['id'];
			} else {
				// URL not set or invalid.
				header("HTTP/1.1 400");
				echo "Invalid URL " . $_POST['url'];
			}

			break;
		case "DELETE":
			if(isset($_GET['id'])) {
				$r = $db->query("SELECT id FROM urls WHERE id=" . intval($_GET['id']));
				$row = $r->fetchArray();
				// Record with the given id exists.
				if($row) {
					if(!$db->query("DELETE FROM urls WHERE id=" . intval($_GET['id']))) {
						header("HTTP/1.1 500");
						die($db->lastErrorMsg());
					}
				} else {
					header("HTTP/1.1 404");
				}
			} else {
				if(!$db->query("DELETE FROM urls")) {
					header("HTTP/1.1 500");
					die($db->lastErrorMsg());
				}
				header("HTTP/1.1 204");
			}
			
			break;
		case "PUT":
			if(!isset($_GET['id'])) {
				header("HTTP/1.1 400");
				die("No ID specified");
			}

			$r = $db->query("SELECT id FROM urls WHERE id=" . intval($_GET['id']));
			$row = $r->fetchArray();
			// Record with the given id exists.
			if($row) {
				// PHP is stupid and doesn't parse PUT requests as POST, therefore 
				// we have to parse the data ourselves.
				if(preg_match("/url=(.+)$/", file_get_contents("php://input"), $match)
						&& count($match) > 1 
						&& filter_var($match[1], FILTER_VALIDATE_URL)) {
				
						if(!$db->query("UPDATE urls SET url=\"" . $match[1] . "\" 
								WHERE id=" . intval($_GET['id']))) {
							header("HTTP/1.1 500");
							die($db->lastErrorMsg());
						}
				} else {
					header("HTTP/1.1 400");
					echo "Invalid URL ";
				}
			} else {
				header("HTTP/1.1 404");
			}

			break;
	}

} else {
	die($db->lastErrorMsg());
}
?>
