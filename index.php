<?php
	include 'connect.php';
	session_start();
	?>
	<head><title>phpBattle</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

<link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>

<style>
	body {
		font-family: 'Roboto', sans-serif;
		font-size: 12px;
	}
</style>

 <script type="text/javascript">
            function refreshPage () {
                var page_y = document.getElementsByTagName("body")[0].scrollTop;
                window.location.href = window.location.href.split('?')[0] + '?page_y=' + page_y;
            }
            window.onload = function () {
                setTimeout(refreshPage, 35000);
                if ( window.location.href.indexOf('page_y') != -1 ) {
                    var match = window.location.href.split('?')[1].split("&")[0].split("=");
                    document.getElementsByTagName("body")[0].scrollTop = match[1];
                }
            }
        </script>


</head>

	<body bgcolor="#FFF3E0">
	<center><META http-equiv='Content-Type' content='text/html; charset=UTF-8'>
	<font color="#E65100"><h1>phpBattle</h1><a href='http://ukip.org'</a><img src='img/advert.png'></a><br><br><a href='help.html'>TUTORIAL (READ THIS!)</a><br><br>
	<div id="spoiler" style="display:none"> 
	<font size=0.5>Your soldiers will now set up camp if enough of them are on a tile.<br>Round #1 begins!<br>Moving your troops is now a bit easier!<br><a href='http://i.imgur.com/WqWEl6T.png'>The south will rise again</a><br><br></font>
	</div> 
	<button title="Click to show/hide content" type="button" onclick="if(document.getElementById('spoiler') .style.display=='none') {document.getElementById('spoiler') .style.display=''}else{document.getElementById('spoiler') .style.display='none'}">Show News</button>
	<hr><br>

<?


	$user = $_SESSION['userphpb'];
	$tile = $_SESSION['tile'];
	$defDir = $_SESSION['dir'];

	if ($defDir == "") {
		$defDir = "North";
		$_SESSION['dir'] = $defDir;
	}

	if ($tile == "") {
		$tile = 100;
		$_SESSION['tile'] = 100;
	}


	if ($user == "") {
		echo "You need to login or register!";
		?>

		<form action="login.php" method="post">
		Username: <input type="text" name="username">
		<br>
		Password: <input type="password" name="password">
		<br>
		<input type="submit" value="Login/Register">
		</form> 
		<font size=0.5>If an account doesn't exist yet, it'll be created with the information you enter here.</font>

		<?
	} else {

		$pl = mysql_fetch_array(mysql_query("SELECT * FROM users WHERE username='".$user."';"));

		if ($pl['round'] == 0) {
			$rI = mysql_fetch_array(mysql_query("SELECT * FROM server WHERE id=1")); //get the round number

			$result = mysql_query("SELECT * FROM users ORDER BY kills DESC"); //find out what position we ended up in (for exp purposes)
			$pos = 1;
			$tPos = 1;
			$xp = 0;
			while ($row = mysql_fetch_assoc($result)) {
		   		if ($row['username'] == $user) {
		   			$pos = $tPos;
		   		}
		   		$tPos = $tPos + 1;
		   	}

			echo "<h2>".$user.", my king!</h2>";
			echo "<b>Battle #".($rI['round']-1)." is over! We must move on to conquer other land!</b><br><br>";
			if ($pos == 1) { echo "You came in 1st place! You won the battle! (<b>+350xp</b>)<br>"; $xp = $xp + 350; }
			if ($pos == 2) { echo "You came in 2nd place! Looks like you have a rival for the next fight... (<b>+250xp</b>)<br>"; $xp = $xp + 250; }
			if ($pos == 3) { echo "You came in 3rd place! (<b>+150xp</b>)<br>"; $xp = $xp + 150; }
			if ($pos > 3) { echo "You came in ".$pos."th place!<br>"; }
			echo "You slayed a total of ".$pl['kills']." enemies! (<b>+".(round($pl['kills']/100))."xp</b>)<br><br>";
			$xp = $xp + round($pl['kills']/100);
			$xp = $xp + $pl['exp'];
			if ($xp > $pl['level']*250) {
				$xp = $xp - $pl['level']*250;
				$pl['level'] = $pl['level'] + 1;
				echo "<h3>Level ".$pl['level']."!</h3>";
				if ($pl['level'] == 2) {
					echo "Unlocked: Orcs (race)<br>";
				} elseif ($pl['level'] == 3) {
					echo "Unlocked: Walls (structure)<br>";
				} elseif ($pl['level'] == 4) {
					echo "Unlocked: Elves (race)<br>";
				}
				
			}
			echo "You have ".$xp."xp/".($pl['level']*250)."xp for level ".($pl['level']+1)."<br>";
			echo "<br><br><h2>Battle #".$rI['round']." begins!</h2>World has been reset.<br>Chat has been reset.<br>Earnings have been reset.<br><br><a href='index.php'>Enter Game</a>";

			mysql_query("UPDATE users SET cash=100, wood=100, population=100, food=150, login=1, kills=0, wins=".($pl['wins']+1).", exp=".$xp.", level=".$pl['level'].", round=1 WHERE username='".$user."';");

		} else {
		
			$a = $_GET['a'];

			if ($pl['login'] == 1) {

				if ($a == "open") {
					echo "<h3>Daily login reward</h3><p>Your reward is...</p>";
					$reward = rand(1, 3);
					if ($reward == 1) {
						echo "<b>5 Gold!</b>";
						mysql_query("UPDATE users SET cash=".($pl['cash']+500)." WHERE username='".$user."';");
					} elseif ($reward == 2) {
						echo "<b>100 wood!</b>";
						mysql_query("UPDATE users SET wood=".($pl['wood']+100)." WHERE username='".$user."';");
					} elseif ($reward == 3) {
						echo "<b>50 food!</b>";
						mysql_query("UPDATE users SET food=".($pl['food']+50)." WHERE username='".$user."';");
					}

					mysql_query("UPDATE users SET login=0 WHERE username='".$user."';");

					echo "<br><br><a href='index.php?a=castle'>Continue to game</a><br><br>";
				} else {
					echo "<h3>Daily login reward</h3><p>Thanks for logging in today! Choose your reward.</br><a href='index.php?a=open&chest=1'><img src='img/chest.png'></a><a href='index.php?a=open&chest=2'><img src='img/chest.png'></a><a href='index.php?a=open&chest=3'><img src='img/chest.png'></a><br><br>";
				}

			} else {

				
				if ($a == "move") { //this is for camera movemement, use the action "travel" for unit movement via the fast travel system
					$dir = $_GET['dir'];
					$_SESSION['dir'] = $dir;
					$defDir = $dir;

					if ($dir == "n") {
						$tile = $tile - 100;
					} elseif ($dir == "s") {
						$tile = $tile + 100;
					} elseif ($dir == "w") {
						$tile = $tile - 1;
					} elseif ($dir == "e") {
						$tile = $tile + 1;
					}
					$_SESSION['tile'] = $tile;
				} elseif ($a == "castle") {
					$result = mysql_query("SELECT * FROM world WHERE owner='".$user."';");
					$foundCastle = false;
					
					while ($row = mysql_fetch_assoc($result)) {
						if ($row['type'] == "Castle") {
							$foundCastle = true;
							$tile = $row['id'];
							$_SESSION['tile'] = $tile;
						}
					}

					if ($foundCastle == false) {
						$hasBase = false;
	        			while ($hasBase == false) {
	            			$posTile = rand(1, (100*100));
				            $bTile = mysql_fetch_array(mysql_query("SELECT * FROM world WHERE id=".$posTile.";"));
				            if ($bTile['owner'] == "" and $bTile['type'] == "Grass") {
				                mysql_query("UPDATE world SET owner='".$user."', units=20, type='Castle' WHERE id=".$posTile.";");
				                $hasBase = true;
				                echo "Set up new Castle on tile ".$posTile."!<br>";
				            }
				        }
					}

					
					echo "Warped to your castle!";
				} elseif ($a == "logout") {
					$_SESSION['userphpb'] = "";
					$user = "";
				} elseif ($a == "view") {
					$a2 = $_GET['v'];
					$_SESSION['view'] = $a2;
				} elseif ($a == "construct") {
	          $build = mysql_fetch_array(mysql_query("SELECT * FROM structures WHERE name='".$_GET['type']."'"));
	          $tInfo = mysql_fetch_array(mysql_query("SELECT * FROM world WHERE id='".$_GET['t']."';"));

	          if ($pl['cash'] > $build['cost'] and $pl['wood'] > $build['wood'] and $tInfo['owner'] == $user) {
	            if ($_GET['type'] == "Barricade") {
	              $tOwner = "NPC";
	            } else {
	              $tOwner = $user;
	            }
	            mysql_query("UPDATE world SET type='Building', owner='".$tOwner."', worth='".($build['cost']*0.75)."', units=".($tInfo['units']+$build['units']).", newType='".$_GET['type']."', time=".$build['time']." WHERE id=".$_GET['t'].";");
	            mysql_query("UPDATE users SET cash=".($pl['cash']-$build['cost']).", wood=".($pl['wood']-$build['wood'])." WHERE username='".$user."';");
	            echo $_GET['type']." built!";
	          } else {
	            echo "You've either not enough cash, wood or you don't own this tile.";
	          }
	        }


				if ($_POST['unitCount'] > 0 or $a == "travel") {
					$direction = $_POST['direction'];
					if ($direction == "") {
						$direction = $_GET['dir'];
					}
					$unitCount = $_POST['unitCount'];
					$thisTile = $_POST['tile'];
					if ($thisTile == "") {
						$thisTile = $_GET['t'];
					}

					$tInfo = mysql_fetch_array(mysql_query("SELECT * FROM world WHERE id='".$thisTile."';"));
					if ($unitCount == "") {
						$unitCount = $tInfo['units']-1;
					}
					echo "Moving ".$unitCount." units ".$direction." from tile#".$thisTile;

					if ($tInfo['owner'] == $user) {
						//echo "<br>We own the tile we're moving from!";
						if ($direction == "north") {
							$headTile = $thisTile - 100;
						} elseif ($direction == "south") {
							$headTile = $thisTile + 100;
						} elseif ($direction == "east") {
							$headTile = $thisTile + 1;
						} elseif ($direction == "west") {
							$headTile = $thisTile - 1;
						}

						$t2Info = mysql_fetch_array(mysql_query("SELECT * FROM world WHERE id='".$headTile."';"));

						if ($tInfo['units'] > $unitCount) {
							if ($t2Info['owner'] == "") {
								mysql_query("UPDATE world SET owner='".$user."', worth='0', units='".($t2Info['units']+$unitCount)."' WHERE id=".$headTile.";");
								mysql_query("UPDATE world SET units='".($tInfo['units']-$unitCount)."' WHERE id=".$thisTile.";");
								mysql_query("UPDATE users SET cash=".($pl['cash']+$t2Info['worth'])." WHERE username='".$user."';");
							} elseif ($t2Info['owner'] == $user) {
								mysql_query("UPDATE world SET units='".($t2Info['units']+$unitCount)."' WHERE id=".$headTile.";");
								mysql_query("UPDATE world SET units='".($tInfo['units']-$unitCount)."' WHERE id=".$thisTile.";");
							} else { //TIME TO FIGHT!!!!!
								//$result = round($t2Info['units'] - (rand($unitCount/4, $unitCount/2)) * (rand(85, 100)/100));
								//$myLoss = round($unitCount - (rand($t2Info['units']/4, $t2Info['units']/2)) * (rand(90,100)/100));

								$myKills = rand(round($unitCount/4), round($unitCount/2));
								$myLoss = rand(round($t2Info['units']/4), round($t2Info['units']));

								echo "<br>You killed ".$myKills." units.<br>You lost ".$myLoss." units.<br>";
		

								mysql_query("UPDATE users SET kills=".($pl['kills']+$myKills)." WHERE username='".$user."';");
								
								$t2Info['units'] = $t2Info['units'] - $myKills;
								$tInfo['units'] = $tInfo['units'] - $myLoss;


							
								if ($t2Info['units'] < 1) {
									if ($t2Info['owner'] == "NPC" or $t2Info['owner'] == "Food") {
										$t2Info['type'] = "Grass";
									} 
									
									if ($t2Info['owner'] == "Food") {
	                 					 mysql_query("UPDATE users SET food=".($pl['food']+$t2Info['worth'])." WHERE username='".$user."';"); 
									}

									$t2Info['owner'] = "";
									$t2Info['units'] = 0;
								}

								if ($tInfo['units'] < 1) {
									$tInfo['owner'] = "";
									$tInfo['units'] = 0;
								}

								mysql_query("UPDATE world SET units='".($t2Info['units'])."', owner='".$t2Info['owner']."', type='".$t2Info['type']."' WHERE id=".$headTile.";");
								mysql_query("UPDATE world SET units='".($tInfo['units'])."', owner='".$tInfo['owner']."' WHERE id=".$thisTile.";");
								


							}
						}

					} else {
						echo "Naughtyyy ;)<br>";
					}


				}

				echo "<table border='1' bgcolor='White'><td>";
				//draw the world
				if (1 == 1) {
					echo "<table border='0' cellpadding='0' cellspacing='0'>";
					$imgRows = 0;
					$thisPos = 5;
					while ($imgRows < 10) {
						if ($imgRows > 5) {

							$min = $tile+(100*$thisPos)-5;
							$max = $tile+(100*$thisPos)+5;
							$thisPos = $thisPos + 1;

						} elseif ($imgRows == 5) {
							
							$min = $tile-5;
							$max = $tile+5;
							$thisPos = $thisPos + 1;
						
						} else {

							$min = $tile-(100*$thisPos)-5;
							$max = $tile-(100*$thisPos)+5;
							$thisPos = $thisPos - 1;

						}
						echo "<tr>";

						$result = mysql_query("SELECT * FROM world WHERE id>".$min." AND id<".$max);
						while ($row = mysql_fetch_assoc($result)) {
							if ($row['type'] == "Grass" && $row['units'] > 10) {
								echo "<td width='32' height='32' background='img/warrior.gif' valign='bottom'>";
							} elseif ($row['type'] == "Building") {

								echo "<td width='32' height='32' background='img/building.gif' valign='bottom'>";
							} else {
								echo "<td width='32' height='32' background='img/".$row['type'].".png' valign='bottom'>";
							}
							if ($a == "sel") {
								$t = $_GET['t'];
								$drawUnits = false;

								if ($row['id'] == $t+100) { echo "<a href='index.php?a=travel&dir=south&u=all&t=".$t."'>"; $drawUnits = true; }
								if ($row['id'] == $t-100) { echo "<a href='index.php?a=travel&dir=north&u=all&t=".$t."'>"; $drawUnits = true; }
								if ($row['id'] == $t+1) { echo "<a href='index.php?a=travel&dir=east&u=all&t=".$t."'>"; $drawUnits = true; }
								if ($row['id'] == $t-1) { echo "<a href='index.php?a=travel&dir=west&u=all&t=".$t."'>"; $drawUnits = true; }
								echo "<font color='White' size=0.5>";

							} else {
								if ($row['owner'] == $user) { 
									echo "<a href='index.php?a=sel&t=".$row['id']."'><font color='Yellow' size=0.5>";
								} elseif ($row['owner'] == "") {
									echo "<font color='White' size=0.5>";
								} else {
									echo "<font color='Red' size=0.5><b>";
								}

								
							}

							$view = $_SESSION['view'];
							if ($view == "") {
								$_SESSION['view'] = "units";
								$view = $_SESSION['view'];
							}

							if ($row[$view] > 1) {
								if ($row['type'] == "Grass") {
									//echo "<img src='img/Warrior.png' width='50%' height='50%'>".$row[$view]."</td></font>";
								}
							}
							if ($a == "sel" and $drawUnits == true) {
									echo "<span title='Property of ".$row['owner']."'>X</td></font>";
							} else {
								if ($row[$view] > 0) {
										echo "<span title='Property of ".$row['owner']."'>".$row[$view]."</td></font>";
								} else {
									echo "</td></font>";
								}
							}

							if ($row['owner'] == $user) {
								echo "</a>";
							}
						}	
						echo "</tr>";
						$imgRows = $imgRows + 1;
					}
					echo "</table>";
				}
				echo "</td><td><center>";
				echo "<iframe src='chat.php' frameborder='0' align='center' width='160' height='300'></iframe>";
				echo "</td></tr><tr><td>";
				//let's grab some spendages!
				$newGold = 0;
				$newWood = 0;
				$newPop = 0;
				$newFood = 0;
				$query = "SELECT * FROM world WHERE owner='".$user."';";
				$result = mysql_query($query);
				while ($row = mysql_fetch_assoc($result)) { //this system NEEDS to be updated! It should pull the tile type and then look it up in the
					if ($row['type'] == "House") { $newPop = $newPop + 5; }
					if ($row['type'] == "Forest") { $newWood = $newWood + 1; }
					if ($row['type'] == "Mine") { $newGold = $newGold + (25/100); }
					if ($row['type'] == "Farm") { $newFood = $newFood + 5; }
					if ($row['type'] == "Lumberjack") { $newWood = $newWood + 5; }
					if ($row['type'] == "Barracks") { $newPop = $newPop - 5; }
					if ($row['type'] == "Outpost") { $newPop = $newPop - 1; }
				}

				$newFood = $newFood - $pl['population'];

				echo "Hi there, <b>".$user."</b>! <br><img src='http://www.goldsilverrate.com/images/gold_mini_icon.png'>".($pl['cash']/100)."<i>(+".$newGold.")</i><img src='img/tree.png'>".$pl['wood']."<i>(+".$newWood.")</i><img src='img/People.png'>".$pl['population']."<i>(+".$newPop.")</i><img src='img/food.png'>".$pl['food']."<i>(+".$newFood.")</i><br><br><a href='map.php'><img src='img/map.png'></a><br>";
				
				if ($a == "sel") {
					$tInfo = mysql_fetch_array(mysql_query("SELECT * FROM world WHERE id='".$_GET['t']."';"));
					if ($tInfo['owner'] == $user) {

						echo "Selected a <b>".$tInfo['type']."</b> tile (<b>".$tInfo['units']."</b> units)<br>";
					
						echo "<a href='index.php?a=build&t=".$tInfo['id']."'>Build</a>";

						echo "<form action='index.php?a=moveUnits' method='post'>Move <input type='number' name='unitCount' min='1' max='".($tInfo['units']-1)."' value='".($tInfo['units']-1)."'> units <select name='direction'><option value='north'>North</option><option value='east'>East</option><option value='south'>South</option><option value='west'>West</option></select><input type='hidden' name='tile' value='".$_GET['t']."'> <input type='submit'></form>";
					} else {
						echo "Naughty ;)<br>";
					}
				} elseif ($a == "build") {
					$tInfo = mysql_fetch_array(mysql_query("SELECT * FROM world WHERE id='".$_GET['t']."';"));
					if ($tInfo['owner'] == $user) {

						

						$result = mysql_query("SELECT * FROM structures");
						while ($row = mysql_fetch_assoc($result)) {
							$hasRequirement = false;
							$result2 = mysql_query("SELECT * FROM world WHERE owner='".$user."';");
							while ($row2 = mysql_fetch_assoc($result2)) {

								if ($row2['type'] == $row['needs']) {
									$hasRequirement = true;
								}

							}
					          if ($row['requirement'] == $tInfo['type'] and $hasRequirement == true) {
					            echo "<a href='index.php?a=construct&type=".$row['name']."&t=".$_GET['t']."'>".$row['name']."</a> (".$row['value']." ".$row['action']."/hour) (<img src='http://www.ahoyworld.co.uk/public/style_images/master/eco_images/money.png'>".($row['cost']/100).", <img src='img/tree.png'>".$row['wood'].", ".$row['time']."<img src='http://www.messinaoggi.it/img/icona-time.png'>)<br>"; //MODIFY THIS LINE AND ADD IN BEFORE IT TO FIND STUFF THAT WAS UPDATED
					          }
							}

					} else {
						echo "Naughtyy ;)<br>";
					}
				
				} elseif ($a == "mu") {
					$tInfo = mysql_fetch_array(mysql_query("SELECT * FROM world WHERE id='".$_GET['t']."';"));
					echo "<form action='index.php?a=moveUnits' method='post'>Move <input type='number' name='unitCount' min='1' max='".($tInfo['units']-1)."'> units <select name='direction' selected='".$defDir."'><option value='north'>North</option><option value='east'>East</option><option value='south'>South</option><option value='west'>West</option></select><input type='hidden' name='tile' value='".$_GET['t']."'> <input type='submit'></form>";
				}

				echo "<b>View: </b><a href='index.php?a=view&v=units'>Units</a> - <a href='index.php?a=view&v=worth'>Worth</a> - <a href='index.php?a=view&v=none'>None</a><br>";
				echo "</td><td>";
				echo "<center><a href='index.php?a=move&dir=n'><img src='img/n.png'></a><br><a href='index.php?a=move&dir=w'><img src='img/w.png'></a><a href='index.php?a=castle'><img src='img/Castle.png'></a><a href='index.php?a=move&dir=e'><img src='img/e.png'></a><br><a href='index.php?a=move&dir=s'><img src='img/s.png'></a><br>";
				

				if ($a == "" or $a == "move" or $a == "moveUnits" or $a == "castle" or $a == "view") {
					
				}

				echo "</td></td></tr></td></table>";
			}
		}
	}
echo "<br><font size=0.5>For optimal gameplay, use Firefox; Chrome does something screwy with the image loading that makes everything feel jolty and odd.</font>";