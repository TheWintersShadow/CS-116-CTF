<!DOCTYPE html>

<html>
<head>
<title>2025 CTF Scoreboard</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
<style>
@import url(http://fonts.googleapis.com/css?family=Happy+Monkey);body{font-family:"Happy Monkey","Helvetica Neue",Helvetica,Arial,sans-serif;font-size:16px;background-color:#fff}h1,h2,h3,h4,p{text-align:center}.points{color:#f0f}.error{color:red}.success{color:green}table{margin-left:auto;margin-right:auto}tr,td,th{border-style:groove}th{font-style:italic}
</style>
</head>

<body>
<h1>2025 CTF Scoreboard</h1>
<?php
    $deduction = -100;
    $actualtampertoken = "skymanatees";
    // Your team's submission key to the CTF game is: 
    $submitkeys = array('winterishere');
    $error = false;
    $success = false;
    $tamper = false;
    if (!empty($_POST)) {
        $submittampertoken = $_POST["tampertoken"];
        $submitkey = $_POST["submitkey"];
        $submitflag = $_POST["submitflag"];
        
        // Check if the submit key is legitimate
        if (in_array($submitkey, $submitkeys)) {
            $team_id = array_search($submitkey, $submitkeys) + 1;
            $myUserName = "root";
            $myPassword = 'Wh@t3ver!Wh@t3ver!';
            $myDatabase = "scoreboard";
            $myHost = "localhost";
            $db = mysqli_connect($myHost, $myUserName, $myPassword) or die ('I cannot connect to the database because: ' . mysqli_error($db));		
            mysqli_select_db($db, $myDatabase) or die("Unable to select database");
            
            // Check if the flag is legitimate
            $query = "SELECT id, points FROM ctf_flags WHERE flag = '" . mysqli_real_escape_string($db, $submitflag) . "'";
            $myResult = mysqli_query($db, $query);
            $row1 = mysqli_fetch_array($myResult);
            if (empty($row1)) {
                $error = true;
            }
            else {
                $flagid = $row1["id"];
                $points = $row1["points"];
                
                // Check that this is not a duplicate submission
                $query = "SELECT id FROM ctf_scoreboard WHERE team_number = $team_id AND flag_id = $flagid";
                $myResult = mysqli_query($db, $query);
                $row2 = mysqli_fetch_array($myResult);
                if (!empty($row2)) {
                    $error = true;
                }
                else {
                
                    // Check for tampering
                    if ($submittampertoken == $actualtampertoken) {
                        $query = "INSERT INTO ctf_scoreboard (team_number, flag_id, points, added_on) VALUES ($team_id, $flagid, $points, NOW())";
                        $myResult = mysqli_query($db, $query);
                        $success = true;
                    }
                    else {
                        $query = "INSERT INTO ctf_scoreboard (team_number, flag_id, points, added_on) VALUES ($team_id, 0, $deduction, NOW())";
                        $myResult = mysqli_query($db, $query);
                        $tamper = true;
                    }
                }
            }
            mysqli_close($db);
        }
        else {
            $error = true;
        }
    }
    
    if ($tamper) {
        echo '<h2 class="error">100 points have been deducted as tamper token was tampered with!</h2>';
    }
    elseif ($error) {
        echo '<h2 class="error">Sorry, your submission was incorrect.</h2>';
    }
    elseif ($success) {
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
