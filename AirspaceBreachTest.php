<?php
include 'geohelpers.php';
include 'timehelpers.php';
$org = 1;

function findFlight($db,$glider,$time)
{
  $ret = -1;
  $q = "select id from flights where flights.glider = '".$glider."' and flights.start < ".$time."000 and flights.land > ".$time."000";
  $r = mysqli_query($db,$q);
  if ($row = mysqli_fetch_array($r) )
     $ret = $row[0];
  return $ret;
}
$flightids = array();
$done = false;

$db_params = require('./config/database.php');
$con_params = $db_params['gliding'];
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
{
   echo "<p>Unable to connect to database</p>";
   exit();
}
$con2_params = $db_params['tracks'];
$con2=mysqli_connect($con2_params['hostname'],$con2_params['username'],$con2_params['password'],$con2_params['dbname']);
if (mysqli_connect_errno())
{
  error_log("Cannot open tracksarchive database"); 
  $con2 = null;
}

$tz=orgTimezone($con,$org);
echo "Checking track points for breachs<br>";
//Loop here for each point that is archived
$q1="SELECT lattitude,longitude,altitude, id from tracksarchive where airspacecheck = 0 order by point_time ASC";
$r1 = mysqli_query($con2,$q1);
while ($row1 = mysqli_fetch_array($r1))
{
   $ptFail = false;
   $q5='';
   //Loop here for each area
   $q = "select id, name, Lower_height from airspace order by id ASC";
   $r = mysqli_query($con,$q);
   while ($row = mysqli_fetch_array($r))
   {
    $vertx=array();
    $verty=array();
    
    buildArea($con,$row[0],$vertx,$verty,false);
    if (pointInArea($vertx,$verty,$row1[0],$row1[1]) > 0)
    {
        if (($row1[2] * 3.281) > $row[2])
        {
          $ptFail = true;
        }
    }

   }
   if ($ptFail)
       $q5 = "update tracksarchive set airspacecheck = 1, airspacebreach = 1 where id = " . $row1[3];
   else
       $q5 = "update tracksarchive set airspacecheck = 1, airspacebreach = 0 where id = " . $row1[3];
   $r5 = mysqli_query($con2,$q5);
} 

$dateStart = new DateTime();
$dateEnd = new DateTime();
 
$cnt = 0;
$q1="SELECT lattitude,longitude,altitude, point_time , glider from tracksarchive where airspacebreach = 1 order by point_time ASC";
$r1 = mysqli_query($con2,$q1);
while ($row1 = mysqli_fetch_array($r1))
{
   //we need to find flight id
   $flightid = findFlight($con,$row1[4] ,timestampSQL($row1[3]));
   if ($flightid >=0)
   {
     if (!in_array($flightid, $flightids))
     {
        $flightids[$cnt] = $flightid;
        $cnt++;
      }
    }   
}

echo "<table>";
//Loop here for each flight in array
for ($i=0;$i < count($flightids);$i++)
{
   $flightid = $flightids[$i];
   $q = "select flights.glider , (flights.start/1000), (flights.land/1000) from flights where id = " . $flightid;
   $r = mysqli_query($con,$q);
   $row = mysqli_fetch_array($r);
   $dt1 = new DateTime();
   $dt2 = new DateTime();   
   $dt3 = new DateTime();
   $dt1->setTimestamp(intval($row[1]));
   $dt2->setTimestamp(intval($row[2]));
   $dt3->setTimestamp(intval($row[1]));

   echo "<tr>";
   echo "<td>".timeLocalFormat($dt3,$tz,'d/m/Y H:i')."</td><td>".$row[0]."</td><td>";
   //Loop here for each airspace   
   $q2 = "select id, name, Lower_height from airspace order by id ASC";
   $r2 = mysqli_query($con,$q2);
   while ($row2 = mysqli_fetch_array($r2))
   {
     $vertx=array();
     $verty=array();
     $cnt = 0;
     buildArea($con,$row2[0],$vertx,$verty,false);


     $maxht = 0;
     $q1="SELECT lattitude,longitude,altitude from tracksarchive where airspacebreach = 1 and glider = '".$row[0]."' and point_time > '".$dt1->format('Y-m-d H:i:s')."' and point_time < '".$dt2->format('Y-m-d H:i:s')."' order by point_time ASC";  
     $r1 = mysqli_query($con2,$q1);
     while ($row1 = mysqli_fetch_array($r1) )
     {
        if (pointInArea($vertx,$verty,$row1[0],$row1[1]) > 0)
        {
           
           $above = ($row1[2] * 3.281) - $row2[2];
           if ($above > 0)
           {
              if ($maxht < $above)
                  $maxht = $above;
           }
        }
     }
     //End of area
     if ($maxht > 0)
        echo $row2[1]." - ".$maxht." ";
   }
   echo "</td><td><a href='MyFlightMap3.php?glider=".$row[0]."&from=".$dt1->format('Y-m-d H:i:s')."&to=".$dt2->format('Y-m-d H:i:s')."&flightid=".$flightid."'>MAP</a></td>";
   echo "</tr>";
}
echo "</table>";
?>