<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

if ($db = new SQLite3('urls.db')) {
    $q = $db->query('CREATE TABLE IF NOT EXISTS urls (id INTEGER PRIMARY KEY NOT NULL, url TEXT NOT NULL)');
    
    switch ($_SERVER['REQUEST_METHOD']) {
        case "GET":
            $q = "SELECT * FROM urls";
            if (isset($_GET['id'])) {
                $q.= " WHERE id=\"" . $_GET['id'] . "\"";
            }
            $r = $db->query($q);
            if ($r) {
                while ($entry = $r->fetchArray()) {
                    if (isset($_GET['id'])) {
                        header("HTTP/1.1 301 Moved");
                        header("Location: " . $entry['url']);
                        die();
                    }
                    echo $entry['id'] . ",";
                }
            }
            if (isset($_GET['id'])) {
                header("HTTP/1.1 404");
            }
            break;

        case "POST":
            if (isset($_POST['url']) && filter_var($_POST['url'], FILTER_VALIDATE_URL)) {
                $r = $db->query("SELECT id FROM urls WHERE url=\"" . $_POST['url'] . "\"");
                $row = $r->fetchArray();
                if (!$row) {
                    if (!$db->query("INSERT INTO urls (url) VALUES(\"" . $_POST['url'] . "\")")) {
                        die($db->lastErrorMsg());
                    }
                    $r = $db->query("SELECT id FROM urls WHERE url=\"" . $_POST['url'] . "\"");
                    $row = $r->fetchArray();
                    if (!$row) {
                        die($db->lastErrorMsg());
                    }
                }
                echo $row['id'];
            } else {
            	header("HTTP/1.1 400 Provide valid url");
            }
            break;
        /* Not tested yet..*/
        case "DELETE":
        	/* PHP only parses $_GET and $_POST automatically, we have to parse $_DELETE manually */
        	parse_str(file_get_contents('php://input'), $_DELETE);
            $q = "DELETE FROM urls";
            if (isset($_DELETE['id'])) {
                $q.= " WHERE id=\"" . $_DELETE['id'] . "\"";
            }
            $r = $db->query($q);
            if($r || !isset($_DELETE['id'])){
            	header("HTTP/1.1 200");
            } else {
        		header("HTTP/1.1 404");
            }
            break;
        /* Not tested yet..*/
        case "PUT":
            /* PHP only builds $_GET and $_POST automatically, we have to build $_PUT manually */
            parse_str(file_get_contents('php://input'), $_PUT);
            if(isset($_PUT['id']) && isset($_PUT['url']) && filter_var($_PUT['url'], FILTER_VALIDATE_URL)){
            	$q = "UPDATE urls SET url=\"" . $_PUT['url'] . "\" WHERE id=\"" . $_PUT['id'] . "\"";
	            $r = $db->query($q);
	            if($r){
	            	header("HTTP/1.1 204");
	            } else {
	        		header("HTTP/1.1 404");
	            }
            } else {
            	header("HTTP/1.1 400 Provide valid url and id");
            }
            break;

        default:
    }
} else {
    die($db->lastErrorMsg());
}
?>
