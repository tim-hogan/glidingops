<?php session_start(); ?>
<?php
$org=0;
if(isset($_SESSION['org'])) $org=$_SESSION['org'];
if(isset($_SESSION['security'])){
 if (!($_SESSION['security'] & 128)){die("Secruity level too low for this page");}
}else{
 header('Location: Login.php');
 die("Please logon");
}
?>
<!DOCTYPE HTML>
<html>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<head>
<style><?php $inc = "./orgs/" . $org . "/heading3.css"; include $inc; ?></style>
<link rel="stylesheet" type="text/css" href="styleform1.css">
</head>
<body>
<?php $inc = "./orgs/" . $org . "/heading3.txt"; include $inc; ?>
<script>function goBack() {window.history.back()}</script>
<?php
$DEBUG=0;
$dateTimeZone = new DateTimeZone($_SESSION['timezone']);
$dateTime = new DateTime('now', $dateTimeZone);
$dateStr = $dateTime->format('Y-m-d');
$timeoffset = $dateTime->getOffset();
$pageid=1;
$errtext="";
$sqltext="";
$error=0;
$trantype="Create";
$recid=-1;
$id_f="";
$id_err="";
$name_f="";
$name_err="";
$addr1_f="";
$addr1_err="";
$addr2_f="";
$addr2_err="";
$addr3_f="";
$addr3_err="";
$addr4_f="";
$addr4_err="";
$country_f="";
$country_err="";
$contact_name_f="";
$contact_name_err="";
$email_f="";
$email_err="";
$timezone_f="";
$timezone_err="";
$aircraft_prefix_f="";
$aircraft_prefix_err="";
$tow_height_charging_f="";
$tow_height_charging_err="";
$tow_time_based_f="";
$tow_time_based_err="";
$default_location_f="";
$default_location_err="";
$name_othercharges_f="";
$name_othercharges_err="";
$def_launch_lat_f="";
$def_launch_lat_err="";
$def_launch_lon_f="";
$def_launch_lon_err="";
$map_centre_lat_f="";
$map_centre_lat_err="";
$map_centre_lon_f="";
$map_centre_lon_err="";
$twitter_consumerKey_f="";
$twitter_consumerKey_err="";
$twitter_consumerSecret_f="";
$twitter_consumerSecret_err="";
$twitter_accessToken_f="";
$twitter_accessToken_err="";
$twitter_accessTokenSecret_f="";
$twitter_accessTokenSecret_err="";
function InputChecker($data)
{
 $data = trim($data);
 $data = stripslashes($data);
 $data = htmlspecialchars($data);
 return $data;
}
if ($_SERVER["REQUEST_METHOD"] == "GET")
{
 if(isset($_GET['id']))
 {
  $recid = $_GET['id'];
  if ($recid >= 0)
  {
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (mysqli_connect_errno())
   {
    $errtext= "Failed to connect to Database: " . mysqli_connect_error();
   }
   else
   {
    $q = "SELECT * FROM organisations WHERE id = " . $recid ;
    $r = mysqli_query($con,$q);
    $row = mysqli_fetch_array($r);
    $id_f = $row['id'];
    $name_f = htmlspecialchars($row['name'],ENT_QUOTES);
    $addr1_f = htmlspecialchars($row['addr1'],ENT_QUOTES);
    $addr2_f = htmlspecialchars($row['addr2'],ENT_QUOTES);
    $addr3_f = htmlspecialchars($row['addr3'],ENT_QUOTES);
    $addr4_f = htmlspecialchars($row['addr4'],ENT_QUOTES);
    $country_f = htmlspecialchars($row['country'],ENT_QUOTES);
    $contact_name_f = htmlspecialchars($row['contact_name'],ENT_QUOTES);
    $email_f = htmlspecialchars($row['email'],ENT_QUOTES);
    $timezone_f = htmlspecialchars($row['timezone'],ENT_QUOTES);
    $aircraft_prefix_f = htmlspecialchars($row['aircraft_prefix'],ENT_QUOTES);
    $tow_height_charging_f = $row['tow_height_charging'];
    $tow_time_based_f = $row['tow_time_based'];
    $default_location_f = htmlspecialchars($row['default_location'],ENT_QUOTES);
    $name_othercharges_f = htmlspecialchars($row['name_othercharges'],ENT_QUOTES);
    $def_launch_lat_f = $row['def_launch_lat'];
    $def_launch_lon_f = $row['def_launch_lon'];
    $map_centre_lat_f = $row['map_centre_lat'];
    $map_centre_lon_f = $row['map_centre_lon'];
    $twitter_consumerKey_f = htmlspecialchars($row['twitter_consumerKey'],ENT_QUOTES);
    $twitter_consumerSecret_f = htmlspecialchars($row['twitter_consumerSecret'],ENT_QUOTES);
    $twitter_accessToken_f = htmlspecialchars($row['twitter_accessToken'],ENT_QUOTES);
    $twitter_accessTokenSecret_f = htmlspecialchars($row['twitter_accessTokenSecret'],ENT_QUOTES);
    $trantype="Update";
    mysqli_close($con);
   }
  }
 }
}
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
 $error=0;
 $id_err = "";
 $name_err = "";
 $addr1_err = "";
 $addr2_err = "";
 $addr3_err = "";
 $addr4_err = "";
 $country_err = "";
 $contact_name_err = "";
 $email_err = "";
 $timezone_err = "";
 $aircraft_prefix_err = "";
 $tow_height_charging_err = "";
 $tow_time_based_err = "";
 $default_location_err = "";
 $name_othercharges_err = "";
 $def_launch_lat_err = "";
 $def_launch_lon_err = "";
 $map_centre_lat_err = "";
 $map_centre_lon_err = "";
 $twitter_consumerKey_err = "";
 $twitter_consumerSecret_err = "";
 $twitter_accessToken_err = "";
 $twitter_accessTokenSecret_err = "";
 $name_f = InputChecker($_POST["name_i"]);
 if (empty($name_f) )
 {
  $name_err = "NAME is required";
  $error = 1;
 }
 $addr1_f = InputChecker($_POST["addr1_i"]);
 $addr2_f = InputChecker($_POST["addr2_i"]);
 $addr3_f = InputChecker($_POST["addr3_i"]);
 $addr4_f = InputChecker($_POST["addr4_i"]);
 $country_f = InputChecker($_POST["country_i"]);
 $contact_name_f = InputChecker($_POST["contact_name_i"]);
 $email_f = InputChecker($_POST["email_i"]);
 $timezone_f = InputChecker($_POST["timezone_i"]);
 $aircraft_prefix_f = InputChecker($_POST["aircraft_prefix_i"]);
if(in_array("1",$_POST['tow_height_charging_i']))
 $tow_height_charging_f = 1;
else
 $tow_height_charging_f = 0;
 if (!empty($tow_height_charging_f ) ) {if (!is_numeric($tow_height_charging_f ) ) {$tow_height_charging_err = "HEIGHT BASED TOW CHARGING is not numeric";$error = 1;}}
if(in_array("1",$_POST['tow_time_based_i']))
 $tow_time_based_f = 1;
else
 $tow_time_based_f = 0;
 if (!empty($tow_time_based_f ) ) {if (!is_numeric($tow_time_based_f ) ) {$tow_time_based_err = "TIME BASED TOW CHARGING is not numeric";$error = 1;}}
 $default_location_f = InputChecker($_POST["default_location_i"]);
 $name_othercharges_f = InputChecker($_POST["name_othercharges_i"]);
 $def_launch_lat_f = InputChecker($_POST["def_launch_lat_i"]);
 $def_launch_lon_f = InputChecker($_POST["def_launch_lon_i"]);
 $map_centre_lat_f = InputChecker($_POST["map_centre_lat_i"]);
 $map_centre_lon_f = InputChecker($_POST["map_centre_lon_i"]);
 $twitter_consumerKey_f = InputChecker($_POST["twitter_consumerKey_i"]);
 $twitter_consumerSecret_f = InputChecker($_POST["twitter_consumerSecret_i"]);
 $twitter_accessToken_f = InputChecker($_POST["twitter_accessToken_i"]);
 $twitter_accessTokenSecret_f = InputChecker($_POST["twitter_accessTokenSecret_i"]);
 if ($error != 1)
 {
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (mysqli_connect_errno())
   {
    $errtext= "Failed to connect to Database: " . mysqli_connect_error();
   }
   else
   {
     $Q = "";
     if (isset($_POST["del"]) ) {if ($_POST["del"] == "Delete"){
       $Q="DELETE FROM organisations WHERE id = " . $_POST['updateid'] ;}}
     if (isset($_POST["tran"]) ) {if ($_POST["tran"] == "Update"){
      $Q = "UPDATE organisations SET ";
      $Q .= "name=";
      $Q .= "'" . mysqli_real_escape_string($con, $name_f)  . "'";
      $Q .= ",addr1=";
      $Q .= "'" . mysqli_real_escape_string($con, $addr1_f)  . "'";
      $Q .= ",addr2=";
      $Q .= "'" . mysqli_real_escape_string($con, $addr2_f)  . "'";
      $Q .= ",addr3=";
      $Q .= "'" . mysqli_real_escape_string($con, $addr3_f)  . "'";
      $Q .= ",addr4=";
      $Q .= "'" . mysqli_real_escape_string($con, $addr4_f)  . "'";
      $Q .= ",country=";
      $Q .= "'" . mysqli_real_escape_string($con, $country_f)  . "'";
      $Q .= ",contact_name=";
      $Q .= "'" . mysqli_real_escape_string($con, $contact_name_f)  . "'";
      $Q .= ",email=";
      $Q .= "'" . mysqli_real_escape_string($con, $email_f)  . "'";
      $Q .= ",timezone=";
      $Q .= "'" . mysqli_real_escape_string($con, $timezone_f)  . "'";
      $Q .= ",aircraft_prefix=";
      $Q .= "'" . mysqli_real_escape_string($con, $aircraft_prefix_f)  . "'";
      $Q .= ",tow_height_charging=";
      $Q .= "'" . $tow_height_charging_f . "'";
      $Q .= ",tow_time_based=";
      $Q .= "'" . $tow_time_based_f . "'";
      $Q .= ",default_location=";
      $Q .= "'" . mysqli_real_escape_string($con, $default_location_f)  . "'";
      $Q .= ",name_othercharges=";
      $Q .= "'" . mysqli_real_escape_string($con, $name_othercharges_f)  . "'";
      $Q .= ",def_launch_lat=";
      $Q .= "'" . $def_launch_lat_f . "'";
      $Q .= ",def_launch_lon=";
      $Q .= "'" . $def_launch_lon_f . "'";
      $Q .= ",map_centre_lat=";
      $Q .= "'" . $map_centre_lat_f . "'";
      $Q .= ",map_centre_lon=";
      $Q .= "'" . $map_centre_lon_f . "'";
      $Q .= ",twitter_consumerKey=";
      $Q .= "'" . mysqli_real_escape_string($con, $twitter_consumerKey_f)  . "'";
      $Q .= ",twitter_consumerSecret=";
      $Q .= "'" . mysqli_real_escape_string($con, $twitter_consumerSecret_f)  . "'";
      $Q .= ",twitter_accessToken=";
      $Q .= "'" . mysqli_real_escape_string($con, $twitter_accessToken_f)  . "'";
      $Q .= ",twitter_accessTokenSecret=";
      $Q .= "'" . mysqli_real_escape_string($con, $twitter_accessTokenSecret_f)  . "'";
$Q .= " WHERE ";$Q .= "id ";$Q .= "= ";
$Q .= $_POST['updateid'];}
     else
     if ($_POST["tran"] == "Create"){
       $Q = "INSERT INTO organisations (";$Q .= "name";$Q .= ", addr1";$Q .= ", addr2";$Q .= ", addr3";$Q .= ", addr4";$Q .= ", country";$Q .= ", contact_name";$Q .= ", email";$Q .= ", timezone";$Q .= ", aircraft_prefix";$Q .= ", tow_height_charging";$Q .= ", tow_time_based";$Q .= ", default_location";$Q .= ", name_othercharges";$Q .= ", def_launch_lat";$Q .= ", def_launch_lon";$Q .= ", map_centre_lat";$Q .= ", map_centre_lon";$Q .= ", twitter_consumerKey";$Q .= ", twitter_consumerSecret";$Q .= ", twitter_accessToken";$Q .= ", twitter_accessTokenSecret";$Q .= " ) VALUES (";
       $Q .= "'" . mysqli_real_escape_string($con, $name_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $addr1_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $addr2_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $addr3_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $addr4_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $country_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $contact_name_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $email_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $timezone_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $aircraft_prefix_f) . "'";
       $Q.= ",";
       $Q .= "'" . $tow_height_charging_f . "'";
       $Q.= ",";
       $Q .= "'" . $tow_time_based_f . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $default_location_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $name_othercharges_f) . "'";
       $Q.= ",";
       $Q .= "'" . $def_launch_lat_f . "'";
       $Q.= ",";
       $Q .= "'" . $def_launch_lon_f . "'";
       $Q.= ",";
       $Q .= "'" . $map_centre_lat_f . "'";
       $Q.= ",";
       $Q .= "'" . $map_centre_lon_f . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $twitter_consumerKey_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $twitter_consumerSecret_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $twitter_accessToken_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $twitter_accessTokenSecret_f) . "'";
      $Q.= ")";
    }}
    $sqltext = $Q;
    if(!mysqli_query($con,$Q) )
    {
       $errtext = "Database entry: " . mysqli_error($con) . "<br>" . $Q;
    }
    mysqli_close($con);
    if ($_POST["tran"] == "Delete")
     header('Location: Organisations');
    if ($_POST["tran"] == "Update")
     header('Location: Organisations');
  }
 }
$name_f=htmlspecialchars($name_f,ENT_QUOTES);
$addr1_f=htmlspecialchars($addr1_f,ENT_QUOTES);
$addr2_f=htmlspecialchars($addr2_f,ENT_QUOTES);
$addr3_f=htmlspecialchars($addr3_f,ENT_QUOTES);
$addr4_f=htmlspecialchars($addr4_f,ENT_QUOTES);
$country_f=htmlspecialchars($country_f,ENT_QUOTES);
$contact_name_f=htmlspecialchars($contact_name_f,ENT_QUOTES);
$email_f=htmlspecialchars($email_f,ENT_QUOTES);
$timezone_f=htmlspecialchars($timezone_f,ENT_QUOTES);
$aircraft_prefix_f=htmlspecialchars($aircraft_prefix_f,ENT_QUOTES);
$default_location_f=htmlspecialchars($default_location_f,ENT_QUOTES);
$name_othercharges_f=htmlspecialchars($name_othercharges_f,ENT_QUOTES);
$twitter_consumerKey_f=htmlspecialchars($twitter_consumerKey_f,ENT_QUOTES);
$twitter_consumerSecret_f=htmlspecialchars($twitter_consumerSecret_f,ENT_QUOTES);
$twitter_accessToken_f=htmlspecialchars($twitter_accessToken_f,ENT_QUOTES);
$twitter_accessTokenSecret_f=htmlspecialchars($twitter_accessTokenSecret_f,ENT_QUOTES);
}
?>
<div id='divform'>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<table>
<?php if (true)
{
echo "<tr><td class='desc'>ORG NUMBER</td><td></td>";
echo "<td>";
echo $id_f; echo "</td>";echo "<td>";
echo $id_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>NAME</td><td>*</td>";
echo "<td>";
echo "<input ";
if (strlen($name_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='name_i' ";echo "size='40' ";echo "Value='";echo $name_f;echo "' ";echo "maxlength='40'";echo " autofocus ";echo ">";echo "</td>";echo "<td>";
echo $name_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>ADDRESS</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($addr1_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='addr1_i' ";echo "size='60' ";echo "Value='";echo $addr1_f;echo "' ";echo "maxlength='60'";echo ">";echo "</td>";echo "<td>";
echo $addr1_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'></td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($addr2_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='addr2_i' ";echo "size='60' ";echo "Value='";echo $addr2_f;echo "' ";echo "maxlength='60'";echo ">";echo "</td>";echo "<td>";
echo $addr2_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'></td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($addr3_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='addr3_i' ";echo "size='60' ";echo "Value='";echo $addr3_f;echo "' ";echo "maxlength='60'";echo ">";echo "</td>";echo "<td>";
echo $addr3_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'></td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($addr4_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='addr4_i' ";echo "size='60' ";echo "Value='";echo $addr4_f;echo "' ";echo "maxlength='60'";echo ">";echo "</td>";echo "<td>";
echo $addr4_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>COUNTRY</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($country_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='country_i' ";echo "size='60' ";echo "Value='";echo $country_f;echo "' ";echo "maxlength='60'";echo ">";echo "</td>";echo "<td>";
echo $country_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>CONTACT NAME</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($contact_name_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='contact_name_i' ";echo "size='80' ";echo "Value='";echo $contact_name_f;echo "' ";echo "maxlength='80'";echo ">";echo "</td>";echo "<td>";
echo $contact_name_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>EMAIL</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($email_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='email_i' ";echo "size='80' ";echo "Value='";echo $email_f;echo "' ";echo "maxlength='80'";echo ">";echo "</td>";echo "<td>";
echo $email_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>TIMEZONE</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($timezone_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='timezone_i' ";echo "size='20' ";echo "Value='";echo $timezone_f;echo "' ";echo "maxlength='20'";echo ">";echo "</td>";echo "<td>";
echo $timezone_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>AIRCRAFT PREFIX</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($aircraft_prefix_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='aircraft_prefix_i' ";echo "size='5' ";echo "Value='";echo $aircraft_prefix_f;echo "' ";echo "maxlength='5'";echo ">";echo "</td>";echo "<td>";
echo $aircraft_prefix_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>HEIGHT BASED TOW CHARGING</td><td></td>";
echo "<td><input type='checkbox' name='tow_height_charging_i[]' Value='1' ";if ($tow_height_charging_f ==1) echo "checked";echo "></td>";echo "<td>";
echo $tow_height_charging_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>TIME BASED TOW CHARGING</td><td></td>";
echo "<td><input type='checkbox' name='tow_time_based_i[]' Value='1' ";if ($tow_time_based_f ==1) echo "checked";echo "></td>";echo "<td>";
echo $tow_time_based_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>DEFAULT LOCATION</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($default_location_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='default_location_i' ";echo "size='40' ";echo "Value='";echo $default_location_f;echo "' ";echo "maxlength='40'";echo ">";echo "</td>";echo "<td>";
echo $default_location_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>NAME OF OTHER CHARGES</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($name_othercharges_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='name_othercharges_i' ";echo "size='20' ";echo "Value='";echo $name_othercharges_f;echo "' ";echo "maxlength='20'";echo ">";echo "</td>";echo "<td>";
echo $name_othercharges_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>DEFAULT TRACK LATITUDE</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($def_launch_lat_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='def_launch_lat_i' ";echo "size='15' ";echo "Value='";echo $def_launch_lat_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $def_launch_lat_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>DEFAULT TRACK LONGITUDE</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($def_launch_lon_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='def_launch_lon_i' ";echo "size='15' ";echo "Value='";echo $def_launch_lon_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $def_launch_lon_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>MAP CENTRE LATITUDE</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($map_centre_lat_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='map_centre_lat_i' ";echo "size='15' ";echo "Value='";echo $map_centre_lat_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $map_centre_lat_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>MAP CENTRE LONGITUDE</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($map_centre_lon_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='map_centre_lon_i' ";echo "size='15' ";echo "Value='";echo $map_centre_lon_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $map_centre_lon_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>TWITTER CONSUMER KEY</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($twitter_consumerKey_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='twitter_consumerKey_i' ";echo "size='60' ";echo "Value='";echo $twitter_consumerKey_f;echo "' ";echo "maxlength='60'";echo ">";echo "</td>";echo "<td>";
echo $twitter_consumerKey_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>TWITTER CONSUMER SECRET</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($twitter_consumerSecret_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='twitter_consumerSecret_i' ";echo "size='60' ";echo "Value='";echo $twitter_consumerSecret_f;echo "' ";echo "maxlength='60'";echo ">";echo "</td>";echo "<td>";
echo $twitter_consumerSecret_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>TWITTER ACCESS TOKEN</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($twitter_accessToken_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='twitter_accessToken_i' ";echo "size='60' ";echo "Value='";echo $twitter_accessToken_f;echo "' ";echo "maxlength='60'";echo ">";echo "</td>";echo "<td>";
echo $twitter_accessToken_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>TWITTER ACCESS TOKEN SECRET</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($twitter_accessTokenSecret_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='twitter_accessTokenSecret_i' ";echo "size='60' ";echo "Value='";echo $twitter_accessTokenSecret_f;echo "' ";echo "maxlength='60'";echo ">";echo "</td>";echo "<td>";
echo $twitter_accessTokenSecret_err; echo "</td></tr>";
}
?>
</table>
<table>
<tr><td><input type="submit" name = 'tran' value = '<?php echo $trantype; ?>'></td><td><?php if ($trantype == "Update") echo "<input type='submit' name = 'del' value = 'Delete'>";?></td><td></td><td></td></tr>
</table>
<input type="hidden" name = 'updateid' value = '<?php echo $recid; ?>'>
</form>
</div>
<div>
<p><?php echo $errtext; ?></p>
<?php if ($DEBUG>0) echo "<p>".$sqltext."</p>"; ?>
</div>
</body>
</html>
