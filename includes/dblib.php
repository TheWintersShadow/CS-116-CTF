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

	function getLogin($login, $password)
	{
		global $db;
		// Using prepared statements for better security in PHP 8
		$stmt = mysqli_prepare($db, "SELECT id FROM users WHERE password = SHA1(?) and login = ?");
		mysqli_stmt_bind_param($stmt, "ss", $password, $login);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		
		if (!$result) {
			$message  = 'Invalid query: ' . mysqli_error($db) . "\n";
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