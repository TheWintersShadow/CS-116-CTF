<?php
	session_start();
	if ($_SESSION['login'] == null)
	{
		header("Location: admin.php");
		exit;
	}
	if (!isset($_COOKIE['lg'])) {
	   setcookie('lg', 'false');
	   $_COOKIE['lg'] = 'false';
	   echo '
<html>
<head><title>404 Not Found</title></head>
<body bgcolor="white">
<center><h1>404 Not Found</h1></center>
<hr><center>nginx/1.4.6 (Ubuntu)</center>
</body>
</html>
<!-- Hmmm, the plot thickens... key{b4280c2dcd3f38c5024c813b14bb3cf188cf8a69400578aa00dad9d2f57fd6ab}-->';
     }
     elseif (isset($_COOKIE['lg']) && strcmp($_COOKIE['lg'], 'true') == 0) {
     	    echo "<!DOCTYPE html><html><head><title>Main</title></head><body><p>Congratulations! Here you go: key{9c8cd382edeb7a8b8f865de0a7b1a51a884f5cbe8f2ae3d88e4d550752f8433c}</p></body></html>";
     }
     else {
                echo '
<html>
<head><title>404 Not Found</title></head>
<body bgcolor="white">
<center><h1>404 Not Found</h1></center>
<hr><center>nginx/1.4.6 (Ubuntu)</center>
</body>
</html>
<!-- Hmmm, the plot thickens... key{b4280c2dcd3f38c5024c813b14bb3cf188cf8a69400578aa00dad9d2f57fd6ab}-->';}
?>
