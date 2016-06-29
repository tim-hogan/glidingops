<?php
include 'timehelpers.php';
header('Content-type: text/xml');
function GetAllSpots($key)
{
  $ch = curl_init();
  $url = "https://api.findmespot.com/spot-main-web/consumer/rest-api/2.0/public/feed/" . $key . "/message.xml";
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
  
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
 
  $result = curl_exec($ch);
  curl_close($ch);
  return $result;
}
function GetLastSpot($key)
{
  $ch = curl_init();
  $url = "https://api.findmespot.com/spot-main-web/consumer/rest-api/2.0/public/feed/" . $key . "/latest.xml";
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
  
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
 
  $result = curl_exec($ch);
  curl_close($ch);
  return $result;
}
if ($_SERVER["REQUEST_METHOD"] == "GET")
{
 $org=$_GET['org'];
 if ($org < 1)
 {
    die("<getspotdata><error>No organisation specified</error></getspotdata>");
 }
 
 

 $con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
 if (mysqli_connect_errno())
 {
  echo "<getspotdata><error>Unable to connect to database</error></getspotdata>";
  exit();
 }
 echo "<getspotdata>";
 echo "<diag><org>".$org."</org></diag>";
 
 $dtNow = new DateTime('now');
 $strDate = timeLocalFormat($dtNow,orgTimezone($con,$org),'Ymd');

 //Who is flying today
 $q4="SELECT glider from flights where org = ".$org." and localdate = " . $strDate ;
 echo "<diag><sql>".$q4."</sql></diag>";
 $r4 = mysqli_query($con,$q4);
 
 while ($row4 = mysqli_fetch_array($r4))
 {
  $q="SELECT spotkey, polltimelast , polltimeall , lastreq , lastlistreq, rego_short from spots WHERE org = ".$org . " and rego_short = '" .$row4[0]. "'";
  $r = mysqli_query($con,$q);
  if (mysqli_num_rows($r) > 0)
  {
    $row = mysqli_fetch_array($r);
  
    echo "<diag>Got spot key for glider ".$row[5]."</diag>";
    $rxml = '';
    $doc = new DOMDocument();
    $dNow = new DateTime("now");
    $dLast = new DateTime($row[3]);
    $dLastFull = new DateTime($row[4]);
    if (($dNow->getTimestamp() - $dLastFull->getTimestamp()) >  $row[2])
    {
       //Get full list
       $rxml = GetAllSpots($row[0]);
       $q1 = "UPDATE spots set lastlistreq = '".$dNow->format('Y-m-d H:i:s')."' WHERE org = ".$org." and rego_short = '".$row[5]."'";
       $r1 = mysqli_query($con,$q1);
    }else
    if (($dNow->getTimestamp() - $dLast->getTimestamp()) >  $row[1])
    {
       //Get last
       $rxml = GetLastSpot($row[0]);
       $q1 = "UPDATE spots set lastreq = '".$dNow->format('Y-m-d H:i:s')."' WHERE org = ".$org." and rego_short = '".$row[5]."'";
       $r1 = mysqli_query($con,$q1);
    }
    if (strlen($rxml) > 0)
    {
      if (!$doc->loadXML($rxml))
      {
       echo "<error>XML Parse Error</error></getspotdata>";
       exit();
      }
      $list = $doc->getElementsByTagName('message');
      foreach ($list as $message) 
      {
        $id = $message->getElementsByTagName ('id')->item(0)->nodeValue;
        $type = $message->getElementsByTagName ('messageType')->item(0)->nodeValue;
        $timenum = $message->getElementsByTagName ('unixTime')->item(0)->nodeValue;
        $lat = $message->getElementsByTagName ('latitude')->item(0)->nodeValue;
        $lon = $message->getElementsByTagName ('longitude')->item(0)->nodeValue;
        if ($type == "TRACK" || $type == "OK")
        {
           $dt = new DateTime();
           $dt->setTimestamp($timenum);
           $q2="INSERT INTO tracks (org,glider,point_id,point_time,lattitude,longitude,altitude,accuracy) VALUES (".$org.", '".$row[5]."',".$id.",'".$dt->format('Y-m-d H:i:s')."',".$lat.",".$lon.",-1,-1)";
           $r2 = mysqli_query($con,$q2);        
        }

      } 
    }   
   }
 } 
}
echo "</getspotdata>";
?>