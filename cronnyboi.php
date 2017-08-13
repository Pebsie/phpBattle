<?php
	include 'connect.php';

	$result = mysql_query("SELECT * FROM world");
	while ($row = mysql_fetch_assoc($result)) {

    if ($row['type'] == "Grass" and $row['owner'] == "") { //add and create NPCs

      if (rand(1, 100) > 99) {
        $newUnits = rand(1, 500);
        mysql_query("UPDATE world SET owner='NPC', worth=".$newUnits.", units=".$newUnits.", type='Skeleton' WHERE id=".$row['id'].";");
      } elseif (rand(1, 100) > 95) {
        $newUnits = rand(1, 15);
        mysql_query("UPDATE world SET owner='Food', worth=".$newUnits.", units=".$newUnits.", type='Sheep' WHERE id=".$row['id'].";");
      }

    } elseif ($row['owner'] == "NPC" or $row['owner'] == "Food") {

    	if (rand(1, 2) == 1) {
    		mysql_query("UPDATE world SET owner='', worth=5, units=0, type='Grass' WHERE id=".$row['id'].";");
    	}

    }

		$playerStorage = array();

		$row2 = mysql_fetch_array(mysql_query("SELECT * FROM structures WHERE name='".$row['type']."';"));

		if ($row2['action'] == "units") {
			$pl = mysql_fetch_array(mysql_query("SELECT * FROM users WHERE username='".$row['owner']."';"));
			if ($pl['population'] > $row2['value']) {
				$pl['population'] = $pl['population'] - $row2['value'];
				mysql_query("UPDATE users SET population=".($pl['population']-$row2['value'])." WHERE username='".$row['owner']."';");
				mysql_query("UPDATE world SET units=".($row['units']+$row2['value'])." WHERE id=".$row['id']);
			}

			if ($pl['population'] < 0) {
        $pl['population'] = 0;
        mysql_query("UPDATE users SET population=0 WHERE username='".$row['owner']."';");
			}

		} elseif ($row2['action'] == "wood") {
			$pl = mysql_fetch_array(mysql_query("SELECT * FROM users WHERE username='".$row['owner']."';"));
			mysql_query("UPDATE users SET wood=".($pl['wood']+$row2['value'])." WHERE username='".$row['owner']."';");
		} elseif ($row2['action'] == "cash") {
			$pl = mysql_fetch_array(mysql_query("SELECT * FROM users WHERE username='".$row['owner']."';"));
			mysql_query("UPDATE users SET cash=".($pl['cash']+$row2['value'])." WHERE username='".$row['owner']."';");
		} elseif ($row2['action'] == "population") {
			$pl = mysql_fetch_array(mysql_query("SELECT * FROM users WHERE username='".$row['owner']."';"));
			mysql_query("UPDATE users SET population=".($pl['population']+$row2['value'])." WHERE username='".$row['owner']."';");
		} elseif ($row2['action'] == "food") {
      	$pl = mysql_fetch_array(mysql_query("SELECT * FROM users WHERE username='".$row['owner']."';"));
        mysql_query("UPDATE users SET food=".($pl['food']+$row2['value'])." WHERE username='".$row['owner']."';");
		} elseif ($row2['action'] == "storage") {
			$playerStorage[$row['owner']] = $playerStorage[$row['owner']] + $row2['value'];
		}
	}


	$result = mysql_query("SELECT * FROM users");
	while ($row = mysql_fetch_assoc($result)) {

    if ($row['population'] > $row['food']) {
      $row['population'] = $row['population'] + ($row['food']-$row['population']);
    }

    $row['food'] = $row['food'] - $row['population'];

		if ($row['wood']-50 > $playerStorage[$row['username']]*2) {
			$row['wood'] = $playerStorage[$row['username']]*2;
		}

		if ($row['food']-50 > $playerStorage[$row['username']]) {
			$row['food'] = $playerStorage[$row['username']];
		}


    mysql_query("UPDATE users SET population=".$row['population'].", food=".$row['food'].", wood=".$row['wood']." WHERE username='".$row['username']."';");

	}
