<?php
include 'connect.php';

$result = mysql_query("SELECT * FROM world");

while ($row = mysql_fetch_assoc($result)) {

  if ($row['time'] > 0) {
  
    $row['time'] = $row['time'] - 1;
    if ($row['time'] == 0) {
      mysql_query("UPDATE world SET type='".$row['newType']."' WHERE id=".$row['id'].";");
    }
  
    mysql_query("UPDATE world SET time='".$row['time']."' WHERE id=".$row['id'].";");
  }

}