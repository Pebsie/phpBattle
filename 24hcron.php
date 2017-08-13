<?php
	include 'connect.php';


	
	
	$result = mysql_query("SELECT * FROM users");
	while ($row = mysql_fetch_assoc($result)) {
	
    
    mysql_query("UPDATE users SET login=1 WHERE username='".$row['username']."';");
	
	}