<?php
/*
    This routine looks at all tracks data and deletes the data if a flight time was not during a flight.
    This has the effect of removing location data for Gliders who are stationary on the ground.
    
    From apache as a web page: http://<hostname>/ArchiveTracks.php
    From a CRON Job:  php TracksRemoveRudundant.php <route directory>
      Example: php TracksRemoveRudundant.php /var/www/html
*/
$configDir = '.';
if (null != $argv && count($argv) > 1)
{
    $configDir = rtrim($argv[1],"/");
}

include 'timehelpers.php';
function HaveFlight($db,$dt,$glider)
{
   $bt = $dt->getTimestamp();
   $q1 = "Select id from flights where flights.glider = '".$glider."' and (flights.start/1000) <= ".$bt." and (flights.land/1000) >= " .$bt; 
   $r1 = mysqli_query($db,$q1); 
   if (mysqli_num_rows($r1)  > 0)
     return true;
   return false;
}
$db_params = require($configDir . '/config/database.php');
$con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
{
 echo "<p>Unable to connect to database</p>";
 exit();
}
$cntFlight = 0;
$cntNoFlight = 0;
$cnt = 0;

$q = "SELECT id, point_time , glider from tracks";
$r = mysqli_query($con,$q);  
while ($row = mysqli_fetch_array($r))
{
  $cnt = $cnt + 1;
  $id = $row[0];
  $pdt = new DateTime($row[1]);
  
  if (HaveFlight($con,$pdt,$row[2]) )
  {
     $cntFlight = $cntFlight + 1;
  }
  else
  {
     $cntNoFlight = $cntNoFlight + 1;
     $q2 =  "DELETE from tracks where id = " . $id;
     $r2 = mysqli_query($con,$q2); 
  }
}
if (null == $argv)
{
   echo "Tracks with flights = " .$cntFlight. "<br/>";
   echo "Tracks with no flights = " .$cntNoFlight. "<br/>";
   echo "Total = " .($cntFlight+$cntNoFlight). "<br/>";
}
mysqli_close($con);
?>

