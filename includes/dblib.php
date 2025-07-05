<?php
	$myUserName = 'root';
	$myPassword = 'Wh@t3ver!Wh@t3ver!';
	$myDBName = 'board';
	$myHost = 'localhost';
	$db = mysqli_connect($myHost, $myUserName, $myPassword, $myDBName);
	if (!$db) {
		die('Cannot connect to the database: ' . mysqli_connect_error());
	}

	function getDB()
	{
		global $db;
		return $db;
	}

	function getLogin ($login, $password)
	{
		global $db;
		$query = "SELECT id FROM users WHERE password = SHA1('" . $password . "') and login = '" . $login . "'";
		$result = mysqli_query($db, $query);
		if (!$result) {
			$message  = 'Invalid query: ' . mysqli_error($db) . "\n";
			$message .= 'Whole query: ' . $query;
			die($message);
		}
		else {
			$row = mysqli_fetch_array($result);
			if (!empty($row)) {
				return $row;
			}
		}
		mysqli_free_result($result);
		return false;
	}
?>
