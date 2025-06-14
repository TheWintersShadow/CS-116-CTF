<?php
	require_once("includes/dblib.php");
	$login = $_POST['login'] ?? '';
	$password = $_POST['password'] ?? '';
	$access = getLogin($login, $password);
	if ($access != false)
	{
		session_start();
		$_SESSION['login'] = $access;
		header("Location: main.php");
		exit; // Added exit after redirect for PHP 8 best practices
	}
	else {
		header("Location: admin.php?error");
		exit; // Added exit after redirect for PHP 8 best practices
	}
?>