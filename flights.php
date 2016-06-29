<?php session_start(); ?>
<?php
$org=0;
if(isset($_SESSION['org'])) $org=$_SESSION['org'];
if(isset($_SESSION['security'])){
 if (!($_SESSION['security'] & 72)){die("Secruity level too low for this page");}
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
<link rel="stylesheet" type="text/css" href="styleform1.css">
</head>
<body>
<script>function goBack() {window.history.back()}</script>
<?php
$DEBUG=0;
$dateTimeZone = new DateTimeZone($_SESSION['timezone']);
$dateTime = new DateTime('now', $dateTimeZone);
$dateStr = $dateTime->format('Y-m-d');
$timeoffset = $dateTime->getOffset();
$pageid=33;
$errtext="";
$sqltext="";
$error=0;
$trantype="Create";
$recid=-1;
$id_f="";
$id_err="";
$localdate_f="";
$localdate_err="";
$location_f="";
$location_err="";
$seq_f="";
$seq_err="";
$type_f="";
$type_err="";
$launchtype_f="";
$launchtype_err="";
$towplane_f="";
$towplane_err="";
$glider_f="";
$glider_err="";
$towpilot_f="";
$towpilot_err="";
$pic_f="";
$pic_err="";
$p2_f="";
$p2_err="";
$start_f="";
$start_err="";
$towland_f="";
$towland_err="";
$land_f="";
$land_err="";
$height_f="";
$height_err="";
$billing_option_f="";
$billing_option_err="";
$billing_member1_f="";
$billing_member1_err="";
$billing_member2_f="";
$billing_member2_err="";
$comments_f="";
$comments_err="";
$finalised_f="";
$finalised_err="";
$deleted_f="";
$deleted_err="";
function InputChecker($data)
{
 $data = trim($data);
 $data = stripslashes($data);
 $data = htmlspecialchars($data);
 return $data;
}
if ($_SERVER["REQUEST_METHOD"] == "GET")
{
 if($_GET['id'] != "" && $_GET['id'] != null)
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
    $q = "SELECT * FROM flights WHERE id = " . $recid ;
    $r = mysqli_query($con,$q);
    $row = mysqli_fetch_array($r);
    if ($_SESSION['org'] > 0 && $row['org'] != $_SESSION['org'])
       die("Record does not exist");
    $id_f = $row['id'];
    $localdate_f = $row['localdate'];
    $location_f = htmlspecialchars($row['location'],ENT_QUOTES);
    $seq_f = $row['seq'];
    $type_f = $row['type'];
    $launchtype_f = $row['launchtype'];
    $towplane_f = $row['towplane'];
    $glider_f = htmlspecialchars($row['glider'],ENT_QUOTES);
    $towpilot_f = $row['towpilot'];
    $pic_f = $row['pic'];
    $p2_f = $row['p2'];
    $start_f = $row['start'];
    $towland_f = $row['towland'];
    $land_f = $row['land'];
    $height_f = $row['height'];
    $billing_option_f = $row['billing_option'];
    $billing_member1_f = $row['billing_member1'];
    $billing_member2_f = $row['billing_member2'];
    $comments_f = htmlspecialchars($row['comments'],ENT_QUOTES);
    $finalised_f = $row['finalised'];
    $deleted_f = $row['deleted'];
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
 $org_err = "";
 $date_err = "";
 $localdate_err = "";
 $updseq_err = "";
 $location_err = "";
 $seq_err = "";
 $type_err = "";
 $launchtype_err = "";
 $towplane_err = "";
 $glider_err = "";
 $towpilot_err = "";
 $pic_err = "";
 $p2_err = "";
 $start_err = "";
 $towland_err = "";
 $land_err = "";
 $height_err = "";
 $billing_option_err = "";
 $billing_member1_err = "";
 $billing_member2_err = "";
 $comments_err = "";
 $finalised_err = "";
 $deleted_err = "";
 $localdate_f = InputChecker($_POST["localdate_i"]);
 if (empty($localdate_f) )
 {
  $localdate_err = "LOCAL DATE is required";
  $error = 1;
 }
 if (!empty($localdate_f ) ) {if (!is_numeric($localdate_f ) ) {$localdate_err = "LOCAL DATE is not numeric";$error = 1;}}
 $location_f = InputChecker($_POST["location_i"]);
 if (empty($location_f) )
 {
  $location_err = "LOCATION is required";
  $error = 1;
 }
 $seq_f = InputChecker($_POST["seq_i"]);
 if (empty($seq_f) )
 {
  $seq_err = "SEQ is required";
  $error = 1;
 }
 if (!empty($seq_f ) ) {if (!is_numeric($seq_f ) ) {$seq_err = "SEQ is not numeric";$error = 1;}}
 $type_f = InputChecker($_POST["type_i"]);
 $launchtype_f = InputChecker($_POST["launchtype_i"]);
 $towplane_f = InputChecker($_POST["towplane_i"]);
 $glider_f = InputChecker($_POST["glider_i"]);
 $towpilot_f = InputChecker($_POST["towpilot_i"]);
 $pic_f = InputChecker($_POST["pic_i"]);
 $p2_f = InputChecker($_POST["p2_i"]);
 $start_f = InputChecker($_POST["start_i"]);
 $towland_f = InputChecker($_POST["towland_i"]);
 $land_f = InputChecker($_POST["land_i"]);
 $height_f = InputChecker($_POST["height_i"]);
 if (!empty($height_f ) ) {if (!is_numeric($height_f ) ) {$height_err = "HEIGHT is not numeric";$error = 1;}}
 $billing_option_f = InputChecker($_POST["billing_option_i"]);
 $billing_member1_f = InputChecker($_POST["billing_member1_i"]);
 $billing_member2_f = InputChecker($_POST["billing_member2_i"]);
 $comments_f = InputChecker($_POST["comments_i"]);
if(in_array("1",$_POST['finalised_i']))
 $finalised_f = 1;
else
 $finalised_f = 0;
 if (!empty($finalised_f ) ) {if (!is_numeric($finalised_f ) ) {$finalised_err = "FINALISED is not numeric";$error = 1;}}
if(in_array("1",$_POST['deleted_i']))
 $deleted_f = 1;
else
 $deleted_f = 0;
 if (!empty($deleted_f ) ) {if (!is_numeric($deleted_f ) ) {$deleted_err = "DELETED is not numeric";$error = 1;}}
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
       $Q="DELETE FROM flights WHERE id = " . $_POST['updateid'] ;}}
     if (isset($_POST["tran"]) ) {if ($_POST["tran"] == "Update"){
      $Q = "UPDATE flights SET ";
      $Q .= "localdate=";
      $Q .= "'" . $localdate_f . "'";
      $Q .= ",location=";
      $Q .= "'" . mysqli_real_escape_string($con, $location_f)  . "'";
      $Q .= ",seq=";
      $Q .= "'" . $seq_f . "'";
      $Q .= ",type=";
       if ($type_f ==0) $Q .= "null"; else {$Q .= "'";$Q .= "$type_f"; $Q .= "'"; }
      $Q .= ",launchtype=";
       if ($launchtype_f ==0) $Q .= "null"; else {$Q .= "'";$Q .= "$launchtype_f"; $Q .= "'"; }
      $Q .= ",towplane=";
       if ($towplane_f ==0) $Q .= "null"; else {$Q .= "'";$Q .= "$towplane_f"; $Q .= "'"; }
      $Q .= ",glider=";
      $Q .= "'" . mysqli_real_escape_string($con, $glider_f)  . "'";
      $Q .= ",towpilot=";
       if ($towpilot_f ==0) $Q .= "null"; else {$Q .= "'";$Q .= "$towpilot_f"; $Q .= "'"; }
      $Q .= ",pic=";
       if ($pic_f ==0) $Q .= "null"; else {$Q .= "'";$Q .= "$pic_f"; $Q .= "'"; }
      $Q .= ",p2=";
       if ($p2_f ==0) $Q .= "null"; else {$Q .= "'";$Q .= "$p2_f"; $Q .= "'"; }
      $Q .= ",start=";
      $Q .= "'" . $start_f . "'";
      $Q .= ",towland=";
      $Q .= "'" . $towland_f . "'";
      $Q .= ",land=";
      $Q .= "'" . $land_f . "'";
      $Q .= ",height=";
      $Q .= "'" . $height_f . "'";
      $Q .= ",billing_option=";
       if ($billing_option_f ==0) $Q .= "null"; else {$Q .= "'";$Q .= "$billing_option_f"; $Q .= "'"; }
      $Q .= ",billing_member1=";
       if ($billing_member1_f ==0) $Q .= "null"; else {$Q .= "'";$Q .= "$billing_member1_f"; $Q .= "'"; }
      $Q .= ",billing_member2=";
       if ($billing_member2_f ==0) $Q .= "null"; else {$Q .= "'";$Q .= "$billing_member2_f"; $Q .= "'"; }
      $Q .= ",comments=";
      $Q .= "'" . mysqli_real_escape_string($con, $comments_f)  . "'";
      $Q .= ",finalised=";
      $Q .= "'" . $finalised_f . "'";
      $Q .= ",deleted=";
      $Q .= "'" . $deleted_f . "'";
$Q .= " WHERE ";$Q .= "id ";$Q .= "= ";
$Q .= $_POST['updateid'];}
     else
     if ($_POST["tran"] == "Create"){
       $Q = "INSERT INTO flights (";$Q .= "org";$Q .= ", localdate";$Q .= ", location";$Q .= ", seq";$Q .= ", type";$Q .= ", launchtype";$Q .= ", towplane";$Q .= ", glider";$Q .= ", towpilot";$Q .= ", pic";$Q .= ", p2";$Q .= ", start";$Q .= ", towland";$Q .= ", land";$Q .= ", height";$Q .= ", billing_option";$Q .= ", billing_member1";$Q .= ", billing_member2";$Q .= ", comments";$Q .= ", finalised";$Q .= ", deleted";$Q .= " ) VALUES (";
$Q.=$_SESSION['org'];       $Q.= ",";
       $Q .= "'" . $localdate_f . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $location_f) . "'";
       $Q.= ",";
       $Q .= "'" . $seq_f . "'";
       $Q.= ",";
       if ($type_f ==0)$Q .= "null"; else{$Q .= "'";$Q .= $type_f;$Q .= "'";}
       $Q.= ",";
       if ($launchtype_f ==0)$Q .= "null"; else{$Q .= "'";$Q .= $launchtype_f;$Q .= "'";}
       $Q.= ",";
       if ($towplane_f ==0)$Q .= "null"; else{$Q .= "'";$Q .= $towplane_f;$Q .= "'";}
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $glider_f) . "'";
       $Q.= ",";
       if ($towpilot_f ==0)$Q .= "null"; else{$Q .= "'";$Q .= $towpilot_f;$Q .= "'";}
       $Q.= ",";
       if ($pic_f ==0)$Q .= "null"; else{$Q .= "'";$Q .= $pic_f;$Q .= "'";}
       $Q.= ",";
       if ($p2_f ==0)$Q .= "null"; else{$Q .= "'";$Q .= $p2_f;$Q .= "'";}
       $Q.= ",";
       $Q .= "'" . $start_f . "'";
       $Q.= ",";
       $Q .= "'" . $towland_f . "'";
       $Q.= ",";
       $Q .= "'" . $land_f . "'";
       $Q.= ",";
       $Q .= "'" . $height_f . "'";
       $Q.= ",";
       if ($billing_option_f ==0)$Q .= "null"; else{$Q .= "'";$Q .= $billing_option_f;$Q .= "'";}
       $Q.= ",";
       if ($billing_member1_f ==0)$Q .= "null"; else{$Q .= "'";$Q .= $billing_member1_f;$Q .= "'";}
       $Q.= ",";
       if ($billing_member2_f ==0)$Q .= "null"; else{$Q .= "'";$Q .= $billing_member2_f;$Q .= "'";}
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $comments_f) . "'";
       $Q.= ",";
       $Q .= "'" . $finalised_f . "'";
       $Q.= ",";
       $Q .= "'" . $deleted_f . "'";
      $Q.= ")";
    }}
    $sqltext = $Q;
    if(!mysqli_query($con,$Q) )
    {
       $errtext = "Database entry: " . mysqli_error($con) . "<br>" . $Q;
    }
    mysqli_close($con);
  }
 }
$location_f=htmlspecialchars($location_f,ENT_QUOTES);
$glider_f=htmlspecialchars($glider_f,ENT_QUOTES);
$comments_f=htmlspecialchars($comments_f,ENT_QUOTES);
}
?>
<div id='divform'>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<table>
<?php if (true)
{
echo "<tr><td class='desc'>ID</td><td></td>";
echo "<td>";
echo $id_f; echo "</td>";echo "<td>";
echo $id_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>LOCAL DATE</td><td>*</td>";
echo "<td>";
echo "<input ";
if (strlen($localdate_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='localdate_i' ";echo "Value='";echo $localdate_f;echo "' ";echo " autofocus ";echo ">";echo "</td>";echo "<td>";
echo $localdate_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>LOCATION</td><td>*</td>";
echo "<td>";
echo "<input ";
if (strlen($location_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='location_i' ";echo "size='60' ";echo "Value='";echo $location_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $location_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>SEQ</td><td>*</td>";
echo "<td>";
echo "<input ";
if (strlen($seq_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='seq_i' ";echo "size='10' ";echo "Value='";echo $seq_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $seq_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>TYPE</td><td></td>";
echo "<td><select name='type_i'>";
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (mysqli_connect_errno())
   {
    $errtext= "Failed to connect to Database: " . mysqli_connect_error();
   }
   else
   {
     echo "<option value='0'></option>";
     $qs= "SELECT * FROM flighttypes ORDER BY name ASC";
     $r = mysqli_query($con,$qs);
     while($row = mysqli_fetch_array($r))
     {
     if ($type_f == $row['id'])
     echo "<option value='" . $row['id'] ."' selected>" . $row['name'] ."</option>" ; 
    else
     echo "<option value='" . $row['id'] ."'>" . $row['name'] ."</option>" ; 
      }
    mysqli_close($con);
   }
echo "</select></td>";echo "<td>";
echo $type_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>LAUNCH TYPE</td><td></td>";
echo "<td><select name='launchtype_i'>";
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (mysqli_connect_errno())
   {
    $errtext= "Failed to connect to Database: " . mysqli_connect_error();
   }
   else
   {
     echo "<option value='0'></option>";
     $qs= "SELECT * FROM launchtypes ORDER BY name ASC";
     $r = mysqli_query($con,$qs);
     while($row = mysqli_fetch_array($r))
     {
     if ($launchtype_f == $row['id'])
     echo "<option value='" . $row['id'] ."' selected>" . $row['name'] ."</option>" ; 
    else
     echo "<option value='" . $row['id'] ."'>" . $row['name'] ."</option>" ; 
      }
    mysqli_close($con);
   }
echo "</select></td>";echo "<td>";
echo $launchtype_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>TOW PLANE</td><td></td>";
echo "<td><select name='towplane_i'>";
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (mysqli_connect_errno())
   {
    $errtext= "Failed to connect to Database: " . mysqli_connect_error();
   }
   else
   {
     echo "<option value='0'></option>";
     $qs= "SELECT * FROM aircraft";if($_SESSION['org'] > 0) {$qs .= " WHERE aircraft.org = " . $_SESSION['org'] . " ";}$qs .= " ORDER BY rego_short ASC";
     $r = mysqli_query($con,$qs);
     while($row = mysqli_fetch_array($r))
     {
     if ($towplane_f == $row['id'])
     echo "<option value='" . $row['id'] ."' selected>" . $row['rego_short'] ."</option>" ; 
    else
     echo "<option value='" . $row['id'] ."'>" . $row['rego_short'] ."</option>" ; 
      }
    mysqli_close($con);
   }
echo "</select></td>";echo "<td>";
echo $towplane_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>GLIDER</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($glider_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='glider_i' ";echo "size='10' ";echo "Value='";echo $glider_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $glider_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>TOW PILOT</td><td></td>";
echo "<td><select name='towpilot_i'>";
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (mysqli_connect_errno())
   {
    $errtext= "Failed to connect to Database: " . mysqli_connect_error();
   }
   else
   {
     echo "<option value='0'></option>";
     $qs= "SELECT * FROM members";if($_SESSION['org'] > 0) {$qs .= " WHERE members.org = " . $_SESSION['org'] . " ";}$qs .= " ORDER BY displayname ASC";
     $r = mysqli_query($con,$qs);
     while($row = mysqli_fetch_array($r))
     {
     if ($towpilot_f == $row['id'])
     echo "<option value='" . $row['id'] ."' selected>" . $row['displayname'] ."</option>" ; 
    else
     echo "<option value='" . $row['id'] ."'>" . $row['displayname'] ."</option>" ; 
      }
    mysqli_close($con);
   }
echo "</select></td>";echo "<td>";
echo $towpilot_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>PIC</td><td></td>";
echo "<td><select name='pic_i'>";
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (mysqli_connect_errno())
   {
    $errtext= "Failed to connect to Database: " . mysqli_connect_error();
   }
   else
   {
     echo "<option value='0'></option>";
     $qs= "SELECT * FROM members";if($_SESSION['org'] > 0) {$qs .= " WHERE members.org = " . $_SESSION['org'] . " ";}$qs .= " ORDER BY displayname ASC";
     $r = mysqli_query($con,$qs);
     while($row = mysqli_fetch_array($r))
     {
     if ($pic_f == $row['id'])
     echo "<option value='" . $row['id'] ."' selected>" . $row['displayname'] ."</option>" ; 
    else
     echo "<option value='" . $row['id'] ."'>" . $row['displayname'] ."</option>" ; 
      }
    mysqli_close($con);
   }
echo "</select></td>";echo "<td>";
echo $pic_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>P2</td><td></td>";
echo "<td><select name='p2_i'>";
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (mysqli_connect_errno())
   {
    $errtext= "Failed to connect to Database: " . mysqli_connect_error();
   }
   else
   {
     echo "<option value='0'></option>";
     $qs= "SELECT * FROM members";if($_SESSION['org'] > 0) {$qs .= " WHERE members.org = " . $_SESSION['org'] . " ";}$qs .= " ORDER BY displayname ASC";
     $r = mysqli_query($con,$qs);
     while($row = mysqli_fetch_array($r))
     {
     if ($p2_f == $row['id'])
     echo "<option value='" . $row['id'] ."' selected>" . $row['displayname'] ."</option>" ; 
    else
     echo "<option value='" . $row['id'] ."'>" . $row['displayname'] ."</option>" ; 
      }
    mysqli_close($con);
   }
echo "</select></td>";echo "<td>";
echo $p2_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>TAKEOFF</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($start_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='start_i' ";echo "size='40' ";echo "Value='";echo $start_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $start_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>TOW LAND</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($towland_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='towland_i' ";echo "size='40' ";echo "Value='";echo $towland_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $towland_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>LAND</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($land_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='land_i' ";echo "size='40' ";echo "Value='";echo $land_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $land_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>HEIGHT</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($height_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='height_i' ";echo "size='15' ";echo "Value='";echo $height_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $height_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>BILLING OPTION</td><td></td>";
echo "<td><select name='billing_option_i'>";
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (mysqli_connect_errno())
   {
    $errtext= "Failed to connect to Database: " . mysqli_connect_error();
   }
   else
   {
     echo "<option value='0'></option>";
     $qs= "SELECT * FROM billingoptions ORDER BY name ASC";
     $r = mysqli_query($con,$qs);
     while($row = mysqli_fetch_array($r))
     {
     if ($billing_option_f == $row['id'])
     echo "<option value='" . $row['id'] ."' selected>" . $row['name'] ."</option>" ; 
    else
     echo "<option value='" . $row['id'] ."'>" . $row['name'] ."</option>" ; 
      }
    mysqli_close($con);
   }
echo "</select></td>";echo "<td>";
echo $billing_option_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>BILLING 1</td><td></td>";
echo "<td><select name='billing_member1_i'>";
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (mysqli_connect_errno())
   {
    $errtext= "Failed to connect to Database: " . mysqli_connect_error();
   }
   else
   {
     echo "<option value='0'></option>";
     $qs= "SELECT * FROM members";if($_SESSION['org'] > 0) {$qs .= " WHERE members.org = " . $_SESSION['org'] . " ";}$qs .= " ORDER BY displayname ASC";
     $r = mysqli_query($con,$qs);
     while($row = mysqli_fetch_array($r))
     {
     if ($billing_member1_f == $row['id'])
     echo "<option value='" . $row['id'] ."' selected>" . $row['displayname'] ."</option>" ; 
    else
     echo "<option value='" . $row['id'] ."'>" . $row['displayname'] ."</option>" ; 
      }
    mysqli_close($con);
   }
echo "</select></td>";echo "<td>";
echo $billing_member1_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>BILLING 1</td><td></td>";
echo "<td><select name='billing_member2_i'>";
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (mysqli_connect_errno())
   {
    $errtext= "Failed to connect to Database: " . mysqli_connect_error();
   }
   else
   {
     echo "<option value='0'></option>";
     $qs= "SELECT * FROM members";if($_SESSION['org'] > 0) {$qs .= " WHERE members.org = " . $_SESSION['org'] . " ";}$qs .= " ORDER BY displayname ASC";
     $r = mysqli_query($con,$qs);
     while($row = mysqli_fetch_array($r))
     {
     if ($billing_member2_f == $row['id'])
     echo "<option value='" . $row['id'] ."' selected>" . $row['displayname'] ."</option>" ; 
    else
     echo "<option value='" . $row['id'] ."'>" . $row['displayname'] ."</option>" ; 
      }
    mysqli_close($con);
   }
echo "</select></td>";echo "<td>";
echo $billing_member2_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>COMMENTS</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($comments_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='comments_i' ";echo "size='60' ";echo "Value='";echo $comments_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $comments_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>FINALISED</td><td></td>";
echo "<td><input type='checkbox' name='finalised_i[]' Value='1' ";if ($finalised_f ==1) echo "checked";echo "></td>";echo "<td>";
echo $finalised_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>DELETED</td><td></td>";
echo "<td><input type='checkbox' name='deleted_i[]' Value='1' ";if ($deleted_f ==1) echo "checked";echo "></td>";echo "<td>";
echo $deleted_err; echo "</td></tr>";
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
