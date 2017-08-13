<?php
	include "connect.php";
	session_start();

	$userTry = $_POST['username'];
	$passTry = $_POST['password'];

	$sql = mysql_query("SELECT * FROM users WHERE username='".$userTry."'");
	$row = mysql_fetch_array($sql);
	if ($row['username'] == "") { //register an account
		echo "User ".$userTry." not found! Registering...<br>";
		mysql_query("INSERT INTO users (username, password) VALUES ('".$userTry."', '".$passTry."')");

		$hasBase = false;
		while ($hasBase == false) {
			$posTile = rand(1, (100*100));
			$bTile = mysql_fetch_array(mysql_query("SELECT * FROM world WHERE id=".$posTile.";"));
			if ($bTile['owner'] == "" and $bTile['type'] == "Grass") {
				mysql_query("UPDATE world SET owner='".$userTry."', units=20, type='Castle' WHERE id=".$posTile.";");
				$hasBase = true;
				echo "Your base is on tile ".$posTile."!<br>";
			}
		}

		echo "User created. You may now <a href='index.php'>login!</a>";
	} elseif ($row['password'] == $passTry) {
		$_SESSION['userphpb'] = $userTry;
		echo "Logged in successfully.<br><a href='index.php'>Return to game</a>";
	} else {
		echo "Password incorrect.<br><a href='index.php'>Try again</a>";
	}
?>