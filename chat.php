<?php
	session_start();
?>
<html>
<font face='Arial' size=0.5>
<center>
<?
	include 'connect.php';
	
?>
	<form action="chat.php" method="post">
	<input type="text" name="msg" size="10">
	<input type="submit" value="Send">
	</form> 
<?

	$message = $_POST['msg'];
	if ($message == "") {
		//donothing
	} else {
		mysql_query("INSERT INTO chat (sender, message) VALUES ('".$_SESSION['userphpb']."', '".$message."')");
	}

	echo "<a href='chat.php'>Refresh Chat</a>";
	$result = mysql_query("SELECT * FROM chat ORDER BY id DESC;");
	echo "<table border=1><tr><td><font size=0.5><b>Sender</td><td><font size=0.5><b>Message</td></tr>";
	while ($row = mysql_fetch_assoc($result)) {
		echo "<tr><td><font size=0.5>".$row['sender']."</td><td><font size=0.5>".$row['message']."</td></tr>";
	}
	echo "</table>";
?>