<!DOCTYPE html>
<html>
<head>
	<title>The Happening</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="alternate stylesheet" type="text/css" href="site_burichan.css" title="Burichan" />
	<link rel="alternate stylesheet" type="text/css" href="site_futaba.css" title="Futaba" />
	<link rel="stylesheet" type="text/css" href="site_kusabax.css" title="Kusabax" />
</head>

<body>

	<?php
		$myUserName = "root";
		$myPassword = 'Wh@t3ver!Wh@t3ver!';
		$myDatabase = "board";
		$myHost = "localhost";
		
		// Use mysqli instead of deprecated mysql functions
		$dbh = new mysqli($myHost, $myUserName, $myPassword, $myDatabase);
		if ($dbh->connect_error) {
			die('I cannot connect to the database because: ' . $dbh->connect_error);
		}
		$query = "";		

		$id = $_GET["id"] ?? null;
		if (!empty($_POST)) {
			if (!empty($_POST['title']) && !empty($_POST['post'])) {
				$query = "INSERT INTO posts (title, post, active, updated_at) VALUES ('" . $dbh->real_escape_string($_POST['title']) . "', '" . $dbh->real_escape_string($_POST['post']) . "', 1, NOW())";
			}
			elseif (!empty($_POST['comments']) && !empty($_POST["post_id"])) {
				$query = "INSERT INTO replies (comments, post_id, active, updated_at) VALUES ('" . $dbh->real_escape_string($_POST['comments']) . "', " . intval($_POST["post_id"]) . ", 1, NOW())";
			}
			if (!empty($query)) {
				$dbh->query($query);
			}
		}
		if (empty($id)) {
        	   	echo '<div id="header"><h1><img id="logo" src="cryinggif.gif" /><br/>The Happening</h1></div>';
			echo '<form id="posting" method="post">';
			echo '<h4>New Post</h4>';
			echo '<p>Title: <input type="text" name="title" /></p>';
			echo '<p>Post: <textarea name="post"></textarea></p>';
			echo '<p><input type="submit"></p>';
			echo '</form>';
			$query = "SELECT * FROM posts WHERE active = 1 AND id > 0 ORDER BY id DESC";
			$myResult = $dbh->query($query);
			while ($row = $myResult->fetch_assoc()) {
				echo '<h2><a href="board.php?id=' . $row["id"] . '">' . $row["title"] . "</a></h2>\n";
				echo "<p>" . $row["post"] . "</p>\n";
	                }
		}
		else {
		     echo '<div id="header"><h1 id="logo">The Happening</h1></div>';
			echo '<form id="reply" method="post">';
			echo '<input type="hidden" name="post_id" value="' . $id . '"/>';
			echo '<h4>Reply</h4>';
			echo '<p>Comments: <textarea name="comments"></textarea></p>';
			echo '<p><input type="submit"></p>';
			echo '</form>';
			$query = "SELECT * FROM posts WHERE active = 1 AND id = " . intval($id);
			$myResult = $dbh->query($query);
			while ($row = $myResult->fetch_assoc()) {
				echo '<h2><a href="board.php?id=' . $row["id"] . '">' . $row["title"] . "</a></h2>\n";
				echo "<p>" . $row["post"] . "</p>\n";
	                }
			$query = "SELECT * FROM replies WHERE active = 1 AND post_id = " . intval($id) . " ORDER BY updated_at";
			$myResult = $dbh->query($query);
			while ($row = $myResult->fetch_assoc()) {
				echo "<p>Replied on " . $row["updated_at"] . ": " . $row["comments"] . "</p>\n";
	                }
		}
		$dbh->close();
	?>
	<div id="footer">
		<hr/>
		<h3><a href="board.php">Home</a> | <a href="admin.php">Administration</a></h3>
	</div>
	</body>
</html>
