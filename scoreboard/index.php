<!DOCTYPE html>

<html>
<head>
<title>CTF Scoreboard</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
<style>
@import url(http://fonts.googleapis.com/css?family=Happy+Monkey);body{font-family:"Happy Monkey","Helvetica Neue",Helvetica,Arial,sans-serif;font-size:16px;background-color:#fff}h1,h2,h3,h4,p{text-align:center}.points{color:#f0f}.error{color:red}.success{color:green}table{margin-left:auto;margin-right:auto}tr,td,th{border-style:groove}th{font-style:italic}
</style>
</head>

<body>
<h1>CTF Scoreboard</h1>
<?php
    $deduction = -100;
    $actualtampertoken = "skymanatees";
    $submitkeys = array(
        'winterishere'
    );

    $error = false;
    $success = false;
    $tamper = false;

    if (!empty($_POST)) {
        $submittampertoken = $_POST["tampertoken"] ?? '';
        $submitkey = $_POST["submitkey"] ?? '';
        $submitflag = $_POST["submitflag"] ?? '';

        // Check if the submit key is legitimate
        if (in_array($submitkey, $submitkeys)) {
            $team_id = array_search($submitkey, $submitkeys) + 1;

            $myUserName = "root";
            $myPassword = 'Wh@t3ver!Wh@t3ver!';
            $myDatabase = "scoreboard";
            $myHost = "localhost";

            // Connect with mysqli
            $dbh = mysqli_connect($myHost, $myUserName, $myPassword, $myDatabase);

            if (!$dbh) {
                die("Database connection failed: " . mysqli_connect_error());
            }

            // Safely query the flag
            $stmt = mysqli_prepare($dbh, "SELECT id, points FROM ctf_flags WHERE flag = ?");
            mysqli_stmt_bind_param($stmt, "s", $submitflag);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row1 = mysqli_fetch_assoc($result);

            if (empty($row1)) {
                $error = true;
            } else {
                $flagid = (int)$row1["id"];
                $points = (int)$row1["points"];

                // Check for duplicate submission
                $stmt = mysqli_prepare($dbh, "SELECT id FROM ctf_scoreboard WHERE team_number = ? AND flag_id = ?");
                mysqli_stmt_bind_param($stmt, "ii", $team_id, $flagid);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row2 = mysqli_fetch_assoc($result);

                if (!empty($row2)) {
                    $error = true;
                } else {
                    if ($submittampertoken === $actualtampertoken) {
                        // Legitimate submission
                        $stmt = mysqli_prepare($dbh, "INSERT INTO ctf_scoreboard (team_number, flag_id, points, added_on) VALUES (?, ?, ?, NOW())");
                        mysqli_stmt_bind_param($stmt, "iii", $team_id, $flagid, $points);
                        mysqli_stmt_execute($stmt);
                        $success = true;
                    } else {
                        // Tampered submission
                        $zero_flag_id = 0;
                        $stmt = mysqli_prepare($dbh, "INSERT INTO ctf_scoreboard (team_number, flag_id, points, added_on) VALUES (?, ?, ?, NOW())");
                        mysqli_stmt_bind_param($stmt, "iii", $team_id, $zero_flag_id, $deduction);
                        mysqli_stmt_execute($stmt);
                        $tamper = true;
                    }
                }
            }

            mysqli_close($dbh);
        } else {
            $error = true;
        }
    }

    if ($tamper) {
        echo '<h2 class="error">100 points have been deducted as tamper token was tampered with!</h2>';
    } elseif ($error) {
        echo '<h2 class="error">Sorry, your submission was incorrect.</h2>';
    } elseif ($success) {
        echo '<h2 class="success">Nice work!</h2>';
    }
?>

<h2>Notes</h2>
<p>1. Game ends at 11:59 PM tonight</p>
<p>2. Points scored after 6 PM will worth half of it's original value.</p>
<h2>Challenges</h2>
<p>Challenge 0: Freebee <span class="points">1 points</span></p>
<p>Challenge 1: You are staring right at it <span class="points">100 points</span></p>
<p>Challenge 2: All your base64 are belong to us. <span class="points">200 points</span></p>
<p>Challenge 3: Going for a walk in .git <span class="points">150 points</span></p>
<p>Challenge 4: Don't ask me if something looks wrong. Look again, pay careful attention <span class="points">200 points</span></p>
<p>Challenge 5: Don't ask me if something looks wrong. Look again, pay really careful attention <span class="points">300 points</span></p>
<p>Challenge 6: That readme is peculiar... <span class="points">150 points</span></p>
<p>Challenge 7: A whole bunch of CS40 homeworks found... <span class="points">200 points</span></p>
<p>Challenge 8: XSS gone sinister <span class="points">300 points</span></p>
<p>Challenge 9: Where are the robots? <span class="points">200 points</span></p>
<p>Challenge 10: LOL <span class="points">450 points</span></p>
<p>Challenge 11: Spongebob Hunt <span class="points">250 points</span></p>
<p>Challenge 12: Find the Bobo <span class="points">300 points</span></p>

<h2>Scores</h2>
<table>
<tr>
<th>Team Number</th>
<th>Score</th>
</tr>
<?php
    $myUserName = "root";
    $myPassword = 'Wh@t3ver!Wh@t3ver!';
    $myDatabase = "scoreboard";
    $myHost = "localhost";

    // Connect to MySQL
    $dbh = mysqli_connect($myHost, $myUserName, $myPassword);

    if (!$dbh) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Select database
    if (!mysqli_select_db($dbh, $myDatabase)) {
        die("Unable to select database: " . mysqli_error($dbh));
    }

    // Correct query with ORDER BY
    $query = "SELECT team_number, SUM(points) AS total FROM ctf_scoreboard GROUP BY team_number ORDER BY total DESC";

    // Run query
    $myResult = mysqli_query($dbh, $query);

    if (!$myResult) {
        die("Query failed: " . mysqli_error($dbh));
    }

    // Output rows
    while ($row = mysqli_fetch_array($myResult)) {
        echo "<tr><td>" . htmlspecialchars($row["team_number"]) . "</td><td>" . htmlspecialchars($row["total"]) . "</td></tr>\n";
    }
?>

</table>

<h2>Flag Submission</h2>
<form method="post">
<input type="hidden" name="tampertoken" value="skymanatees"/>
<p>Submission Key <input type="text" name="submitkey" /> Flag <input type="text" size="30" name="submitflag" /> <input type="submit" /></p>
</form>

</body>
</html>
