<?php
	include 'connect.php';
	session_start();
	echo "<a href='index.php'>Return</a><br><br>Gray = Neutral<br>Yellow = You<br>Red = Enemy<br>Orange = NPC (enemy)<br><br>";
	
	$user = $_SESSION['userphpb'];
	$result = mysql_query("SELECT * FROM world");
	$thisY = 1;

	while ($row = mysql_fetch_assoc($result)) {
		echo "<span title='".$row['owner']."'>";
		if ($row['owner'] == $user) {
			echo "<img src='img/owned.png'>";
		} elseif ($row['owner'] == "") {
			echo "<img src='img/neutral.png'>";
		} elseif ($row['owner'] == "NPC" or $row['owner'] == "Food") {
			echo "<img src='img/npc.png'>";
		} else {
			echo "<img src='img/enemy.png'>";
		}
		echo "</span>";
		$thisY = $thisY + 1;
		if ($thisY > 100) {
			$thisY = 1;
			echo "<br>";
		}
	}
	echo "<br><h2>Leaderboard</h2>";
	$result = mysql_query("SELECT * FROM users ORDER BY kills DESC");
	$pos = 1;
	while ($row = mysql_fetch_assoc($result)) {
    if ($pos < 6) {
      echo $pos.") ".$row['username']." (".$row['kills']." kills)<br>";
      $pos = $pos + 1;
     }
	}
?>