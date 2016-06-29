<?php
include 'helpers.php';
include 'timehelpers.php';
header('Content-type: text/xml');
if ($_SERVER["REQUEST_METHOD"] == "GET")
{
 $org=$_GET['org'];
 if ($org < 1)
 {
    echo "<resp><error>No organisation number specified</error></resp>";     
    exit();
 }
 $dateTimeZoneNZ = new DateTimeZone("Pacific/Auckland");
 $dateTimeNZ = new DateTime("now", $dateTimeZoneNZ);
 $dateStr = $dateTimeNZ->format('Ymd');
 $dateStr2 = $dateTimeNZ->format('Y-m-d');
 

 $con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
 if (mysqli_connect_errno())
 {
  echo "<resp><error>Unable to connect to database</error></resp>";
  exit();
 }
 $flightTypeGlider = getGlidingFlightType($con); 
 echo "<resp>";
 echo "<duties>";

 $sql= "SELECT a.name , b.displayname from duty LEFT JOIN dutytypes a ON a.id = duty.type LEFT JOIN members b ON b.id = duty.member where duty.org = ".$org." and duty.localdate = '" .$dateStr2. "'";
 $r = mysqli_query($con,$sql);
 while ($row = mysqli_fetch_array($r) )
 {
   echo "<duty>";
   echo "<t>" . $row[0] . "</t>";
   echo "<n>" . $row[1] . "</n>";
   echo "</duty>";
 }
 echo "</duties>";

 echo "<flights>";
 $q= "SELECT flights.seq,flights.glider, b.displayname,c.displayname, (flights.start/1000) , (flights.land/1000) from flights LEFT JOIN members b ON b.id = flights.pic LEFT JOIN members c ON c.id = flights.p2 where flights.org = ".$org." and flights.localdate=" . $dateStr . " and flights.type = ".$flightTypeGlider." and flights.deleted <> 1 and flights.start > 0 order by flights.seq ASC";
 $r = mysqli_query($con,$q);
 while ($row = mysqli_fetch_array($r) )
 {
   $landed = 0;
   if ($row[5] > 0)
      $landed = 1;
  echo "<flight>";
  echo "<seq>".$row[0]."</seq>";
  echo "<glider>".$row[1]."</glider>";
  echo "<landed>".$landed."</landed>";
  echo "<name1>".$row[2]."</name1>";
  echo "<name2>".$row[3]."</name2>";
  echo "<start>".$row[4]."</start>";
  $duration = $row[5] - $row[4];
  if ($landed == 1)
     echo "<dur>".$duration."</dur>";
  else
     echo "<dur>0</dur>";  
  //Get the last point
  $dtstart = new DateTime();
  $dtstart->setTimestamp($row[4]);
  $strStart = $dtstart->format('Y-m-d H:i:s');

  echo "<points>";  

  if ($landed == 1)
  {
    $dtland = new DateTime();
    $dtland->setTimestamp($row[5]);
    $strland = $dtland->format('Y-m-d H:i:s');
    $q2 = "SELECT tracks.point_time,lattitude,longitude,altitude from tracks where tracks.org = ".$org." and tracks.glider = '".$row[1]."' and tracks.point_time >= '" .$strStart. "'  and tracks.point_time <= '" .$strland. "' ORDER by tracks.point_time";  
  }
  else
    $q2 = "SELECT tracks.point_time,lattitude,longitude,altitude from tracks where tracks.org = ".$org." and tracks.glider = '".$row[1]."' and tracks.point_time >= '" .$strStart. "' ORDER by tracks.point_time";  
  $r2 = mysqli_query($con,$q2); 
  while ($row2 = mysqli_fetch_array($r2) )
  {
       echo "<p>";
       echo "<t>" .timestampSQL($row2[0]). "</t>";
       echo "<lt>" .$row2[1]. "</lt>";
       echo "<ln>" .$row2[2]. "</ln>";
       echo "<al>" .$row2[3]. "</al>";
       echo "</p>";
  } 
  echo "</points>";  
  echo "</flight>";
 }
 echo "</flights>";
 echo "</resp>";
}
?>
