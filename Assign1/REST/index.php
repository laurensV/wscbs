<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);


/* DEEL LAURENS */
// if ($db = new SQLite3('urls.db')) {
//     $q = $db->query('CREATE TABLE IF NOT EXISTS urls (id INTEGER PRIMARY KEY NOT NULL, url TEXT NOT NULL)');
    
//     switch ($_SERVER['REQUEST_METHOD']) {
//         case "GET":
//             $q = "SELECT * FROM urls";
//             if (isset($_GET['id'])) {
//                 $q.= " WHERE id=\"" . $_GET['id'] . "\"";
//             }
//             $r = $db->query($q);
//             if ($r) {
//                 while ($entry = $r->fetchArray()) {
//                     if (isset($_GET['id'])) {
//                         header("HTTP/1.1 301 Moved");
//                         header("Location: " . $entry['url']);
//                         die();
//                     }
//                     echo $entry['id'] . ",";
//                 }
//             }
//             if (isset($_GET['id'])) {
//                 header("HTTP/1.1 404");
//             }
//             break;

//         case "POST":
//             if (isset($_POST['url']) && filter_var($_POST['url'], FILTER_VALIDATE_URL)) {
//                 $r = $db->query("SELECT id FROM urls WHERE url=\"" . $_POST['url'] . "\"");
//                 $row = $r->fetchArray();
//                 if (!$row) {
//                     if (!$db->query("INSERT INTO urls (url) VALUES(\"" . $_POST['url'] . "\")")) {
//                         die($db->lastErrorMsg());
//                     }
//                     $r = $db->query("SELECT id FROM urls WHERE url=\"" . $_POST['url'] . "\"");
//                     $row = $r->fetchArray();
//                     if (!$row) {
//                         die($db->lastErrorMsg());
//                     }
//                 }
//                 echo $row['id'];
//             } else {
//             	header("HTTP/1.1 400 Provide valid url");
//             }
//             break;
//         /* Not tested yet..*/
//         case "DELETE":
//         	/* PHP only parses $_GET and $_POST automatically, we have to parse $_DELETE manually */
//         	parse_str(file_get_contents('php://input'), $_DELETE);
//             $q = "DELETE FROM urls";
//             if (isset($_GET['id'])) {
//                 $q.= " WHERE id=\"" . $_GET['id'] . "\"";
//             }
//             $r = $db->query($q);
//             if($r || !isset($_GET['id'])){
//             	header("HTTP/1.1 200");
//             } else {
//         		header("HTTP/1.1 404");
//             }
//             break;
//         /* Not tested yet..*/
//         case "PUT":
//             /* PHP only builds $_GET and $_POST automatically, we have to build $_PUT manually */
//             parse_str(file_get_contents('php://input'), $_PUT);
//             if(isset($_GET['id']) && isset($_PUT['url']) && filter_var($_PUT['url'], FILTER_VALIDATE_URL)){
//             	$q = "UPDATE urls SET url=\"" . $_PUT['url'] . "\" WHERE id=\"" . $_GET['id'] . "\"";
// 	            $r = $db->query($q);
// 	            if($r){
// 	            	header("HTTP/1.1 204");
// 	            } else {
// 	        		header("HTTP/1.1 404");
// 	            }
//             } else {
//             	header("HTTP/1.1 400 Provide valid url and id");
//             }
//             break;

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
            /* PHP only builds $_GET and $_POST automatically, we have to build $_PUT manually */
            parse_str(file_get_contents('php://input'), $_PUT);
			$r = $db->query("SELECT id FROM urls WHERE id=" . intval($_GET['id']));
			$row = $r->fetchArray();
			// Record with the given id exists.
			if($row) {
				// PHP is stupid and doesn't parse PUT requests as POST, therefore 
				// we have to parse the data ourselves.
				if(isset($_PUT['url']) && filter_var($_PUT['url'], FILTER_VALIDATE_URL)) {
				
						if(!$db->query("UPDATE urls SET url=\"" . $_PUT['url'] . "\" WHERE id=" . intval($_GET['id']))) {
							header("HTTP/1.1 500");
							die($db->lastErrorMsg());
						}
				} else {
					header("HTTP/1.1 400 Invalid URL");
					echo "Invalid URL";
				}
			} else {
				header("HTTP/1.1 404");
			}

			break;
	}

        default:
    }
} else {
    die($db->lastErrorMsg());
}
?>
