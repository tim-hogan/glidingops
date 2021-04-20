<?php
require_once 'timehelpers.php';

$con_params = require('./config/database.php');
$con_params = $con_params['gliding'];
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (!mysqli_connect_errno())
{
 $rownum=0;
 $dateTime = new DateTime('now');
 $dateStr = $dateTime->format('Y-m-d');
 $strTZ = orgTimezone($con,$org);

 $q="SELECT create_time, msg from messages where org =".$org." order by create_time DESC";
 $r = mysqli_query($con,$q);
 if ($row = mysqli_fetch_array($r))
 {
   echo "<p class=\"p4\"><span class=\"s1\">" .timeLocalSQL($row[0],$strTZ,'D j M Y H:i'). "</span> " .$row[1]."</p>";
 }
 else
 {
    echo "<p class='p4'>No messages</p>";
 }
 if (isset($_SESSION['memberid']))
 {
  $q="SELECT localdate, a.name from duty LEFT JOIN dutytypes a ON a.id = duty.type where duty.org = ".$org." and member = " .$_SESSION['memberid'] . " and localdate >= '" .$dateStr . "' order by localdate asc";
  $r = mysqli_query($con,$q);
  while ($row = mysqli_fetch_array($r) )
  {
   if($rownum==0)
   {
    echo "<p class='p3'>YOUR NEXT ROSTERED DUTIES:</p>";

   }
   $rownum=$rownum+1;
   $dtstr=$row[0];
   echo "<p class='p4'>" .substr($dtstr,8,2)."/".substr($dtstr,5,2)."/".substr($dtstr,0,4). " " . $row[1] . "</p>";
  }
 }
}
?>