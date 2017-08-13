<?php
	
	include 'connect.php';

	mysql_query("TRUNCATE world;");
	mysql_query("TRUNCATE chat;");

	for ($i = 1; $i <= 10000; $i++) {
    	echo $i;

    	$type = rand(1, 1000);
    	$tileType = "Grass";
    	$tileUnits = 0;
    	$tileWorth = 5;
    	$tileOwner = "";

    	if ($type > 800) {
    		$tileType = "Forest";
    		$tileUnits = 0;
    		$tileWorth = 10;
    	} elseif ($type > 795) {
    		$tileType = "Skeleton";
    		$tileUnits = rand(5, 25);
    		$tileWorth = 100;
    		$tileOwner = "NPC";
    	} elseif ($type > 785) {
    		$tileType = "Mine";
    		$tileWorth = 15;
    	}

    	$sql = "INSERT INTO world (owner, worth, units, type) VALUES ('".$tileOwner."', ".$tileWorth.", ".$tileUnits.", '".$tileType."');";
		$result = mysql_query($sql);
			
	}