<?php
include './helpers/session_helpers.php';
session_start();
require_security_level(4);

header('Content-type: text/xml');
include 'timehelpers.php';
echo "<bookings>";
if ($_SERVER["REQUEST_METHOD"] == "GET")
{
   if (!isset($_GET['org']) )
   {
      echo "</bookings>";
      exit;
   }
   $org=$_GET['org'];
   $datestr=$_GET["date"];
   
   echo "<diag>" . $datestr . "</diag>";
  

   $con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   $orgTZ = orgTimezone($con,$org); 
   
   //Create two timestamps converted to GMT
   $dateTimeZone = new DateTimeZone($orgTZ);
   $dateStart1 = new DateTime($datestr . " 00:00:00", $dateTimeZone);
   $dateEnd1 = new DateTime($datestr . " 23:59:59", $dateTimeZone);
   $dateStart= new DateTime();
   $dateEnd= new DateTime();
   $dateStart->setTimestamp($dateStart1->getTimestamp());
   $dateEnd->setTimestamp($dateEnd1->getTimestamp());
   $timeoffset = $dateStart1->getOffset();
   
   //First get the roster for the date
   $q1 = "SELECT a.name,b.displayname,b.phone_mobile from duty LEFT JOIN dutytypes a ON a.id = duty.type LEFT JOIN members b ON b.id = duty.member where duty.org = ".$org." and localdate = '" . $datestr . "'";
  
   $r1 = mysqli_query($con,$q1);
   while ($row = mysqli_fetch_array($r1) )
   {
      echo "<duty><t>" . $row[0] . "</t><n>" . $row[1] . "</n><p>" .$row[2] . "</p></duty>"; 
   }


   $q1 = "SELECT bookings.id,b.registration,bookings.start,bookings.end, bookings.description,a.displayname,c.displayname, d.typename , d.colour FROM bookings LEFT JOIN members a ON a.id = bookings.member LEFT JOIN aircraft b ON b.id = bookings.aircraft LEFT JOIN members c ON c.id = bookings.instructor LEFT JOIN bookingtypes d ON d.id = bookings.type where bookings.org = ".$org." and end > '" . $dateStart->format("Y-m-d H:i:s") . "' and start < '" . $dateEnd->format("Y-m-d H:i:s") ."'";
   
   $r1 = mysqli_query($con,$q1);
   while ($row = mysqli_fetch_array($r1) )
   {
  echo "<booking>";
  echo "<id>" . $row['id'] . "</id>";
  echo "<r>" . $row[1] . "</r>";
  $ds= new DateTime($row[2]);  //UTC
  $de= new DateTime($row[3]);  //UTC
  if ($ds->getTimestamp() < $dateStart->getTimestamp())
      echo "<s>0.00</s>";
  else
  {
      echo "<s>";
      echo timeLocalSQL($row[2],$orgTZ,'H');
      echo ".";
      echo intval((intval(timeLocalSQL($row[2],$orgTZ,'i')) / 60)*100);
      echo "</s>";
  }
  if ($de->getTimestamp() > $dateEnd->getTimestamp())
      echo "<e>23.99</e>";
  else
  {
      $de->setTimestamp($de->getTimestamp()+$timeoffset);
      echo "<e>";
      echo timeLocalSQL($row[3],$orgTZ,'H');
      echo ".";
      echo intval((intval(timeLocalSQL($row[3],$orgTZ,'i')) / 60)*100);
      echo "</e>";
  }
 
   echo "<t>";   
   echo $row[5] . " " . $row[4];
   echo "</t>";
  

   echo "<i>";   
   echo $row[6];
   echo "</i>";

   echo "<ty>";   
   echo $row[7];
   echo "</ty>";

   echo "<tyclr>";   
   echo $row[8];
   echo "</tyclr>";

   echo "</booking>";
   }
}
echo "</bookings>";
mysqli_close($con);
?>
