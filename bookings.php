<?php session_start(); ?>
<?php
$org=0;
if(isset($_SESSION['org'])) $org=$_SESSION['org'];
if(isset($_SESSION['security'])){
 if (!($_SESSION['security'] & 6)){die("Secruity level too low for this page");}
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
<style><?php $inc = "./orgs/" . $org . "/heading2.css"; include $inc; ?></style>
<style>
<?php $inc = "./orgs/" . $org . "/menu1.css"; include $inc; ?></style>
<link rel="stylesheet" type="text/css" href="styleform1.css">
</head>
<body>
<?php $inc = "./orgs/" . $org . "/heading2.txt"; include $inc; ?>
<?php $inc = "./orgs/" . $org . "/menu1.txt"; include $inc; ?>
<script>function goBack() {window.history.back()}</script>
<?php
$DEBUG=0;
$dateTimeZone = new DateTimeZone($_SESSION['timezone']);
$dateTime = new DateTime('now', $dateTimeZone);
$dateStr = $dateTime->format('Y-m-d');
$timeoffset = $dateTime->getOffset();
$pageid=37;
$errtext="";
$sqltext="";
$error=0;
$trantype="Create";
$recid=-1;
$id_f="";
$id_err="";
$start_f=$dateStr;
$start_err="";
$start_fa="";
$start_fb="";
$end_f=$dateStr;
$end_err="";
$end_fa="";
$end_fb="";
$type_f="";
$type_err="";
$description_f="";
$description_err="";
$member_f="";
$member_err="";
$aircraft_f="";
$aircraft_err="";
$instructor_f="";
$instructor_err="";
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
    $q = "SELECT * FROM bookings WHERE id = " . $recid ;
    $r = mysqli_query($con,$q);
    $row = mysqli_fetch_array($r);
    if ($_SESSION['org'] > 0 && $row['org'] != $_SESSION['org'])
       die("Record does not exist");
    $id_f = $row['id'];
    $start_fdate1=new DateTime($row['start']);
    $start_fdate2=new DateTime();
    $start_fdate2->setTimestamp($start_fdate1->getTimestamp()  +  $timeoffset);
    $start_f=$start_fdate2->format('Y-m-d');
    $start_fa=$start_fdate2->format('H');    $start_fb=$start_fdate2->format('i');    $end_fdate1=new DateTime($row['end']);
    $end_fdate2=new DateTime();
    $end_fdate2->setTimestamp($end_fdate1->getTimestamp()  +  $timeoffset);
    $end_f=$end_fdate2->format('Y-m-d');
    $end_fa=$end_fdate2->format('H');    $end_fb=$end_fdate2->format('i');    $type_f = $row['type'];
    $description_f = htmlspecialchars($row['description'],ENT_QUOTES);
    $member_f = $row['member'];
    $aircraft_f = $row['aircraft'];
    $instructor_f = $row['instructor'];
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
 $made_err = "";
 $lastmodify_err = "";
 $start_err = "";
 $end_err = "";
 $type_err = "";
 $description_err = "";
 $member_err = "";
 $aircraft_err = "";
 $instructor_err = "";
 $user_err = "";
 $start_f = $_POST["start_i"];
 $start_fa = $_POST["start_ia"];
 $start_fb = $_POST["start_ib"];
 $start_fsum =  $start_f . " " . $start_fa . ":" . $start_fb . ":00";
 $start_fdate1=new DateTime($start_fsum,$dateTimeZone);
 $start_fdate2=new DateTime("@" . (string)$start_fdate1->getTimestamp());
 $end_f = $_POST["end_i"];
 $end_fa = $_POST["end_ia"];
 $end_fb = $_POST["end_ib"];
 $end_fsum =  $end_f . " " . $end_fa . ":" . $end_fb . ":00";
 $end_fdate1=new DateTime($end_fsum,$dateTimeZone);
 $end_fdate2=new DateTime("@" . (string)$end_fdate1->getTimestamp());
 $type_f = InputChecker($_POST["type_i"]);
 if (empty($type_f) )
 {
  $type_err = "TYPE is required";
  $error = 1;
 }
 $description_f = InputChecker($_POST["description_i"]);
 $member_f = InputChecker($_POST["member_i"]);
 $aircraft_f = InputChecker($_POST["aircraft_i"]);
 $instructor_f = InputChecker($_POST["instructor_i"]);
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
       $Q="DELETE FROM bookings WHERE id = " . $_POST['updateid'] ;}}
     if (isset($_POST["tran"]) ) {if ($_POST["tran"] == "Update"){
      $Q = "UPDATE bookings SET ";
      $Q .= "start=";
      $Q .= "'" . $start_fdate2->format('Y-m-d H:i:s') . "'";
      $Q .= ",end=";
      $Q .= "'" . $end_fdate2->format('Y-m-d H:i:s') . "'";
      $Q .= ",type=";
      $Q .= "'" . $type_f . "'";
      $Q .= ",description=";
      $Q .= "'" . mysqli_real_escape_string($con, $description_f)  . "'";
      $Q .= ",member=";
       if ($member_f ==0) $Q .= "null"; else {$Q .= "'";$Q .= "$member_f"; $Q .= "'"; }
      $Q .= ",aircraft=";
       if ($aircraft_f ==0) $Q .= "null"; else {$Q .= "'";$Q .= "$aircraft_f"; $Q .= "'"; }
      $Q .= ",instructor=";
       if ($instructor_f ==0) $Q .= "null"; else {$Q .= "'";$Q .= "$instructor_f"; $Q .= "'"; }
$Q .= " WHERE ";$Q .= "id ";$Q .= "= ";
$Q .= $_POST['updateid'];}
     else
     if ($_POST["tran"] == "Create"){
       $Q = "INSERT INTO bookings (";$Q .= "org";$Q .= ", start";$Q .= ", end";$Q .= ", type";$Q .= ", description";$Q .= ", member";$Q .= ", aircraft";$Q .= ", instructor";$Q .= " ) VALUES (";
$Q.=$_SESSION['org'];       $Q.= ",";
       $Q .= "'" . $start_fdate2->format('Y-m-d H:i:s') . "'";
       $Q.= ",";
       $Q .= "'" . $end_fdate2->format('Y-m-d H:i:s') . "'";
       $Q.= ",";
       $Q .= "'" . $type_f . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $description_f) . "'";
       $Q.= ",";
       if ($member_f ==0)$Q .= "null"; else{$Q .= "'";$Q .= $member_f;$Q .= "'";}
       $Q.= ",";
       if ($aircraft_f ==0)$Q .= "null"; else{$Q .= "'";$Q .= $aircraft_f;$Q .= "'";}
       $Q.= ",";
       if ($instructor_f ==0)$Q .= "null"; else{$Q .= "'";$Q .= $instructor_f;$Q .= "'";}
      $Q.= ")";
    }}
    $sqltext = $Q;
    if(!mysqli_query($con,$Q) )
    {
       $errtext = "Database entry: " . mysqli_error($con) . "<br>" . $Q;
    }
    mysqli_close($con);
    if ($_POST["tran"] == "Delete")
     header('Location: bookings-list.php');
    if ($_POST["tran"] == "Update")
     header('Location: bookings-list.php');
  }
 }
$description_f=htmlspecialchars($description_f,ENT_QUOTES);
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
echo "<tr><td class='desc'>FROM</td><td></td>";
echo "<td><input type='date' name='start_i' Value='" . $start_f . "'><select name='start_ia' Value='" . $start_fa . "'>";
echo "<option value='00'";if ($start_fa=="00") echo " selected";echo ">00</option>";
echo "<option value='01'";if ($start_fa=="01") echo " selected";echo ">01</option>";
echo "<option value='02'";if ($start_fa=="02") echo " selected";echo ">02</option>";
echo "<option value='03'";if ($start_fa=="03") echo " selected";echo ">03</option>";
echo "<option value='04'";if ($start_fa=="04") echo " selected";echo ">04</option>";
echo "<option value='05'";if ($start_fa=="05") echo " selected";echo ">05</option>";
echo "<option value='06'";if ($start_fa=="06") echo " selected";echo ">06</option>";
echo "<option value='07'";if ($start_fa=="07") echo " selected";echo ">07</option>";
echo "<option value='08'";if ($start_fa=="08") echo " selected";echo ">08</option>";
echo "<option value='09'";if ($start_fa=="09") echo " selected";echo ">09</option>";
echo "<option value='10'";if ($start_fa=="10") echo " selected";echo ">10</option>";
echo "<option value='11'";if ($start_fa=="11") echo " selected";echo ">11</option>";
echo "<option value='12'";if ($start_fa=="12") echo " selected";echo ">12</option>";
echo "<option value='13'";if ($start_fa=="13") echo " selected";echo ">13</option>";
echo "<option value='14'";if ($start_fa=="14") echo " selected";echo ">14</option>";
echo "<option value='15'";if ($start_fa=="15") echo " selected";echo ">15</option>";
echo "<option value='16'";if ($start_fa=="16") echo " selected";echo ">16</option>";
echo "<option value='17'";if ($start_fa=="17") echo " selected";echo ">17</option>";
echo "<option value='18'";if ($start_fa=="18") echo " selected";echo ">18</option>";
echo "<option value='19'";if ($start_fa=="19") echo " selected";echo ">19</option>";
echo "<option value='20'";if ($start_fa=="20") echo " selected";echo ">20</option>";
echo "<option value='21'";if ($start_fa=="21") echo " selected";echo ">21</option>";
echo "<option value='22'";if ($start_fa=="22") echo " selected";echo ">22</option>";
echo "<option value='23'";if ($start_fa=="23") echo " selected";echo ">23</option>";
echo "</select>:<select name='start_ib' Value='" . $start_fb . "'>";
echo "<option value='00'";if ($start_fb=="00") echo " selected";echo ">00</option>";
echo "<option value='15'";if ($start_fb=="15") echo " selected";echo ">15</option>";
echo "<option value='30'";if ($start_fb=="30") echo " selected";echo ">30</option>";
echo "<option value='45'";if ($start_fb=="45") echo " selected";echo ">45</option>";
echo "</select></td>";echo "<td>";
echo $start_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>TO</td><td></td>";
echo "<td><input type='date' name='end_i' Value='" . $end_f . "'><select name='end_ia' Value='" . $end_fa . "'>";
echo "<option value='00'";if ($end_fa=="00") echo " selected";echo ">00</option>";
echo "<option value='01'";if ($end_fa=="01") echo " selected";echo ">01</option>";
echo "<option value='02'";if ($end_fa=="02") echo " selected";echo ">02</option>";
echo "<option value='03'";if ($end_fa=="03") echo " selected";echo ">03</option>";
echo "<option value='04'";if ($end_fa=="04") echo " selected";echo ">04</option>";
echo "<option value='05'";if ($end_fa=="05") echo " selected";echo ">05</option>";
echo "<option value='06'";if ($end_fa=="06") echo " selected";echo ">06</option>";
echo "<option value='07'";if ($end_fa=="07") echo " selected";echo ">07</option>";
echo "<option value='08'";if ($end_fa=="08") echo " selected";echo ">08</option>";
echo "<option value='09'";if ($end_fa=="09") echo " selected";echo ">09</option>";
echo "<option value='10'";if ($end_fa=="10") echo " selected";echo ">10</option>";
echo "<option value='11'";if ($end_fa=="11") echo " selected";echo ">11</option>";
echo "<option value='12'";if ($end_fa=="12") echo " selected";echo ">12</option>";
echo "<option value='13'";if ($end_fa=="13") echo " selected";echo ">13</option>";
echo "<option value='14'";if ($end_fa=="14") echo " selected";echo ">14</option>";
echo "<option value='15'";if ($end_fa=="15") echo " selected";echo ">15</option>";
echo "<option value='16'";if ($end_fa=="16") echo " selected";echo ">16</option>";
echo "<option value='17'";if ($end_fa=="17") echo " selected";echo ">17</option>";
echo "<option value='18'";if ($end_fa=="18") echo " selected";echo ">18</option>";
echo "<option value='19'";if ($end_fa=="19") echo " selected";echo ">19</option>";
echo "<option value='20'";if ($end_fa=="20") echo " selected";echo ">20</option>";
echo "<option value='21'";if ($end_fa=="21") echo " selected";echo ">21</option>";
echo "<option value='22'";if ($end_fa=="22") echo " selected";echo ">22</option>";
echo "<option value='23'";if ($end_fa=="23") echo " selected";echo ">23</option>";
echo "</select>:<select name='end_ib' Value='" . $end_fb . "'>";
echo "<option value='00'";if ($end_fb=="00") echo " selected";echo ">00</option>";
echo "<option value='15'";if ($end_fb=="15") echo " selected";echo ">15</option>";
echo "<option value='30'";if ($end_fb=="30") echo " selected";echo ">30</option>";
echo "<option value='45'";if ($end_fb=="45") echo " selected";echo ">45</option>";
echo "</select></td>";echo "<td>";
echo $end_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>TYPE</td><td>*</td>";
echo "<td><select name='type_i'>";
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (mysqli_connect_errno())
   {
    $errtext= "Failed to connect to Database: " . mysqli_connect_error();
   }
   else
   {
     $qs= "SELECT * FROM bookingtypes";if($_SESSION['org'] > 0) {$qs .= " WHERE bookingtypes.org = " . $_SESSION['org'] . " ";}$qs .= " ORDER BY typename ASC";
     $r = mysqli_query($con,$qs);
     while($row = mysqli_fetch_array($r))
     {
     if ($type_f == $row['id'])
     echo "<option value='" . $row['id'] ."' selected>" . $row['typename'] ."</option>" ; 
    else
     echo "<option value='" . $row['id'] ."'>" . $row['typename'] ."</option>" ; 
      }
    mysqli_close($con);
   }
echo "</select></td>";echo "<td>";
echo $type_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>DESCRIPTION</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($description_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='description_i' ";echo "size='60' ";echo "Value='";echo $description_f;echo "' ";echo " autofocus ";echo ">";echo "</td>";echo "<td>";
echo $description_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>MEMBER</td><td></td>";
echo "<td><select name='member_i'>";
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
     if ($member_f == $row['id'])
     echo "<option value='" . $row['id'] ."' selected>" . $row['displayname'] ."</option>" ; 
    else
     echo "<option value='" . $row['id'] ."'>" . $row['displayname'] ."</option>" ; 
      }
    mysqli_close($con);
   }
echo "</select></td>";echo "<td>";
echo $member_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>AIRCRAFT</td><td></td>";
echo "<td><select name='aircraft_i'>";
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (mysqli_connect_errno())
   {
    $errtext= "Failed to connect to Database: " . mysqli_connect_error();
   }
   else
   {
     echo "<option value='0'></option>";
     $qs= "SELECT * FROM aircraft";if($_SESSION['org'] > 0) {$qs .= " WHERE aircraft.org = " . $_SESSION['org'] . " ";}$qs .= " ORDER BY registration ASC";
     $r = mysqli_query($con,$qs);
     while($row = mysqli_fetch_array($r))
     {
     if ($aircraft_f == $row['id'])
     echo "<option value='" . $row['id'] ."' selected>" . $row['registration'] ."</option>" ; 
    else
     echo "<option value='" . $row['id'] ."'>" . $row['registration'] ."</option>" ; 
      }
    mysqli_close($con);
   }
echo "</select></td>";echo "<td>";
echo $aircraft_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>INSTRUCTOR</td><td></td>";
echo "<td><select name='instructor_i'>";
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
     if ($instructor_f == $row['id'])
     echo "<option value='" . $row['id'] ."' selected>" . $row['displayname'] ."</option>" ; 
    else
     echo "<option value='" . $row['id'] ."'>" . $row['displayname'] ."</option>" ; 
      }
    mysqli_close($con);
   }
echo "</select></td>";echo "<td>";
echo $instructor_err; echo "</td></tr>";
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
