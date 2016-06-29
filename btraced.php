<?php
header('Content-type: application/json');
$strxml = '';
$err=0;
$messsage='';
function retError($num,$trip,$message)
{
    echo "{";
    echo "\"id\":";
    echo $num;
    echo ",";
    if (strlen($trip) > 0)
    	echo "\"tripid\":".$trip.","; 
    echo "\"error\":true,"; 
    if (strlen($message) > 0)
    {
    	echo "\"message\":\"";
    	echo $message;
    	echo "\","; 
    }
    echo "\"valid\":true";
    echo "}"; 
}

function parseMilli($str)
{ 
 strtok($str,".");
 $milipart = strtok(".");
 $milipart = substr($milipart,0,3);
 $frac = 0;
 switch (strlen($milipart))
 {
     case 0:
     $frac = 0;
     break;
     case 1:
     $frac = intval($milipart) * 100;
     break;
     case 2:
     $frac = intval($milipart) * 10;
     break; 
     case 3:
     $frac = intval($milipart);
     break;
 }
 return $frac;
} 

if ($_SERVER["REQUEST_METHOD"] == "GET")
{
  if (isset($_GET["upd"]) )
  	$strxml = $_GET["upd"];
}
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
   if (isset($_POST["upd"]) )
        $strxml = $_POST["upd"];
}
if (strlen($strxml) == 0)
	$strxml = @file_get_contents('php://input');
if (strlen($strxml) > 0)
{
 $strxml = trim($strxml);
 if (strncasecmp ($strxml, "<?xml" , 5 ) != 0)
     $strxml = "<?xml version=\"1.0\"?>" . $strxml;
 //   error_log ( $strxml ); 
 $doc = new DOMDocument();
 if (!$doc->loadXML($strxml))
 {
    echo "<status>ERROR: XML Parse</status></upd>";
    exit();
 }


 $strJSONPoints = "\"points\":[";
 $bDone1 = 0;
 $update = $doc->getElementsByTagName('bwiredtravel')->item(0);
 $username = $update->getElementsByTagName('username')->item(0)->nodeValue;
 $password = $update->getElementsByTagName('password')->item(0)->nodeValue;
 $password = md5($password);
 $timeoff = $update->getElementsByTagName('timeOffset')->item(0)->nodeValue;
 $iTimeOff = intval($timeoff);
 $travel = $update->getElementsByTagName('travel')->item(0);
 $tripid = $travel->getElementsByTagName('id')->item(0)->nodeValue;
 $getTripURL= $travel->getElementsByTagName('getTripUrl')->item(0)->nodeValue;
 $tripdesc= $travel->getElementsByTagName('description')->item(0)->nodeValue;
 
 $con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
 if (mysqli_connect_errno())
 {
    retError('901',$tripid,'Database Open Error');
    exit();
 }


 //Validate the user
 $q = "SELECT org, id from users where usercode = '".$username."' and password = '".$password. "'";
 if(!$r=mysqli_query($con,$q) )
 {
    retError('901',$tripid,'Database Query Error');
    exit();
 }
 if (mysqli_num_rows($r) > 0 )
 {
    $row=mysqli_fetch_array($r);
    $org = $row[0];
    $userid = $row[1];
 }
 else
 {
     retError('1','','');
     exit();
 }
 $list = $travel->getElementsByTagName ('point'); 
 foreach ($list as $point) 
 {
   $id=$point->getElementsByTagName('id')->item(0)->nodeValue;
   $time=$point->getElementsByTagName('date')->item(0)->nodeValue;
   $milli = parseMilli($time);
   $time=strtok($time,".");
   
   $linuxtime = intval($time) - $iTimeOff;
   $dt= new DateTime();
   $dt->setTimestamp($linuxtime);
   $lat=$point->getElementsByTagName('lat')->item(0)->nodeValue;
   $lon=$point->getElementsByTagName('lon')->item(0)->nodeValue;
   $alt=$point->getElementsByTagName('altitude')->item(0)->nodeValue;
   $acu=$point->getElementsByTagName('haccu')->item(0)->nodeValue;
   
   
   $q = "INSERT INTO tracks (org,user,trip_id,glider,point_id,point_time,point_time_milli,lattitude,longitude,altitude,accuracy) VALUES (".$org."," 
      .$userid . ",".$tripid. ",'".$tripdesc."',".$id.",'".$dt->format('Y-m-d H:i:s')."',".$milli.",".$lat.",".$lon.",".$alt.",".$acu.")";   
   
   if(!mysqli_query($con,$q) )
   {
    $err=1;
    $messsage .= mysqli_error($con) . " sql " . $q . " ";
   }

   if ($bDone1 == 1)
      $strJSONPoints .= ",";
   $strJSONPoints .= $id;
   $bDone1 = 1;
 }
 $strJSONPoints .= "],";
 
 $strJSON = "{\"id\":0,\"tripid\":".$tripid.",".$strJSONPoints."\"valid\":true";
 if ($getTripURL == "1")
     $strJSON .= ",\"tripurl\":\"http%3a%2f%2fglidingops%2ecom%2f\"";
 if ($err==1)
     $strJSON .=  ",\"message\":\"".$messsage."\"";
 $strJSON .= "}"; 

 echo $strJSON;
 mysqli_close($con);
}
?>
