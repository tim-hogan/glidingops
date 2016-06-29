<?php session_start(); ?>
<?php
$org=0;
if(isset($_SESSION['org'])) $org=$_SESSION['org'];
if(isset($_SESSION['security'])){
 if (!($_SESSION['security'] & 120)){die("Secruity level too low for this page");}
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
$pageid=25;
$errtext="";
$sqltext="";
$error=0;
$trantype="Create";
$recid=-1;
$id_f="";
$id_err="";
$registration_f="";
$registration_err="";
$rego_short_f="";
$rego_short_err="";
$type_f="";
$type_err="";
$make_model_f="";
$make_model_err="";
$seats_f="";
$seats_err="";
$serial_f="";
$serial_err="";
$club_glider_f="";
$club_glider_err="";
$bookable_f="";
$bookable_err="";
$charge_per_minute_f="";
$charge_per_minute_err="";
$max_perflight_charge_f="";
$max_perflight_charge_err="";
$next_annual_f=$dateStr;
$next_annual_err="";
$next_supplementary_f=$dateStr;
$next_supplementary_err="";
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
    $q = "SELECT * FROM aircraft WHERE id = " . $recid ;
    $r = mysqli_query($con,$q);
    $row = mysqli_fetch_array($r);
    if ($_SESSION['org'] > 0 && $row['org'] != $_SESSION['org'])
       die("Record does not exist");
    $id_f = $row['id'];
    $registration_f = htmlspecialchars($row['registration'],ENT_QUOTES);
    $rego_short_f = htmlspecialchars($row['rego_short'],ENT_QUOTES);
    $type_f = $row['type'];
    $make_model_f = htmlspecialchars($row['make_model'],ENT_QUOTES);
    $seats_f = $row['seats'];
    $serial_f = htmlspecialchars($row['serial'],ENT_QUOTES);
    $club_glider_f = $row['club_glider'];
    $bookable_f = $row['bookable'];
    $charge_per_minute_f = $row['charge_per_minute'];
    $max_perflight_charge_f = $row['max_perflight_charge'];
    $next_annual_f = $row['next_annual'];
    $next_supplementary_f = $row['next_supplementary'];
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
 $create_time_err = "";
 $registration_err = "";
 $rego_short_err = "";
 $type_err = "";
 $make_model_err = "";
 $seats_err = "";
 $serial_err = "";
 $club_glider_err = "";
 $bookable_err = "";
 $charge_per_minute_err = "";
 $max_perflight_charge_err = "";
 $next_annual_err = "";
 $next_supplementary_err = "";
 $registration_f = InputChecker($_POST["registration_i"]);
 if (empty($registration_f) )
 {
  $registration_err = "REGISTRATION is required";
  $error = 1;
 }
 $rego_short_f = InputChecker($_POST["rego_short_i"]);
 if (empty($rego_short_f) )
 {
  $rego_short_err = "REGISTRATION SHORT FORM (3 Letters) is required";
  $error = 1;
 }
 $type_f = InputChecker($_POST["type_i"]);
 if (empty($type_f) )
 {
  $type_err = "TYPE is required";
  $error = 1;
 }
 $make_model_f = InputChecker($_POST["make_model_i"]);
 $seats_f = InputChecker($_POST["seats_i"]);
 if (empty($seats_f) )
 {
  $seats_err = "NUMBER OF SEATS is required";
  $error = 1;
 }
 if (!empty($seats_f ) ) {if (!is_numeric($seats_f ) ) {$seats_err = "NUMBER OF SEATS is not numeric";$error = 1;}}
 $serial_f = InputChecker($_POST["serial_i"]);
if(in_array("1",$_POST['club_glider_i']))
 $club_glider_f = 1;
else
 $club_glider_f = 0;
 if (!empty($club_glider_f ) ) {if (!is_numeric($club_glider_f ) ) {$club_glider_err = "CLUB GLIDER is not numeric";$error = 1;}}
if(in_array("1",$_POST['bookable_i']))
 $bookable_f = 1;
else
 $bookable_f = 0;
 if (!empty($bookable_f ) ) {if (!is_numeric($bookable_f ) ) {$bookable_err = "BOOKABLE is not numeric";$error = 1;}}
 $charge_per_minute_f = InputChecker($_POST["charge_per_minute_i"]);
 if (!empty($charge_per_minute_f ) ) {if (!is_numeric($charge_per_minute_f ) ) {$charge_per_minute_err = "CHARGE PER MINUTE is not numeric";$error = 1;}}
 $max_perflight_charge_f = InputChecker($_POST["max_perflight_charge_i"]);
 if (!empty($max_perflight_charge_f ) ) {if (!is_numeric($max_perflight_charge_f ) ) {$max_perflight_charge_err = "MAX MINUTES CHARGE is not numeric";$error = 1;}}
 $next_annual_f = InputChecker($_POST["next_annual_i"]);
 $next_supplementary_f = InputChecker($_POST["next_supplementary_i"]);
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
       $Q="DELETE FROM aircraft WHERE id = " . $_POST['updateid'] ;}}
     if (isset($_POST["tran"]) ) {if ($_POST["tran"] == "Update"){
      $Q = "UPDATE aircraft SET ";
      $Q .= "registration=";
      $Q .= "'" . mysqli_real_escape_string($con, $registration_f)  . "'";
      $Q .= ",rego_short=";
      $Q .= "'" . mysqli_real_escape_string($con, $rego_short_f)  . "'";
      $Q .= ",type=";
      $Q .= "'" . $type_f . "'";
      $Q .= ",make_model=";
      $Q .= "'" . mysqli_real_escape_string($con, $make_model_f)  . "'";
      $Q .= ",seats=";
      $Q .= "'" . $seats_f . "'";
      $Q .= ",serial=";
      $Q .= "'" . mysqli_real_escape_string($con, $serial_f)  . "'";
      $Q .= ",club_glider=";
      $Q .= "'" . $club_glider_f . "'";
      $Q .= ",bookable=";
      $Q .= "'" . $bookable_f . "'";
      $Q .= ",charge_per_minute=";
      $Q .= "'" . $charge_per_minute_f . "'";
      $Q .= ",max_perflight_charge=";
      $Q .= "'" . $max_perflight_charge_f . "'";
      $Q .= ",next_annual=";
      $Q .= "'" . $next_annual_f . "'";
      $Q .= ",next_supplementary=";
      $Q .= "'" . $next_supplementary_f . "'";
$Q .= " WHERE ";$Q .= "id ";$Q .= "= ";
$Q .= $_POST['updateid'];}
     else
     if ($_POST["tran"] == "Create"){
       $Q = "INSERT INTO aircraft (";$Q .= "org";$Q .= ", registration";$Q .= ", rego_short";$Q .= ", type";$Q .= ", make_model";$Q .= ", seats";$Q .= ", serial";$Q .= ", club_glider";$Q .= ", bookable";$Q .= ", charge_per_minute";$Q .= ", max_perflight_charge";$Q .= ", next_annual";$Q .= ", next_supplementary";$Q .= " ) VALUES (";
$Q.=$_SESSION['org'];       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $registration_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $rego_short_f) . "'";
       $Q.= ",";
       $Q .= "'" . $type_f . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $make_model_f) . "'";
       $Q.= ",";
       $Q .= "'" . $seats_f . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $serial_f) . "'";
       $Q.= ",";
       $Q .= "'" . $club_glider_f . "'";
       $Q.= ",";
       $Q .= "'" . $bookable_f . "'";
       $Q.= ",";
       $Q .= "'" . $charge_per_minute_f . "'";
       $Q.= ",";
       $Q .= "'" . $max_perflight_charge_f . "'";
       $Q.= ",";
       $Q .= "'" . $next_annual_f . "'";
       $Q.= ",";
       $Q .= "'" . $next_supplementary_f . "'";
      $Q.= ")";
    }}
    $sqltext = $Q;
    if(!mysqli_query($con,$Q) )
    {
       $errtext = "Database entry: " . mysqli_error($con) . "<br>" . $Q;
    }
    mysqli_close($con);
    if ($_POST["tran"] == "Delete")
     header('Location: AllAircraft');
    if ($_POST["tran"] == "Update")
     header('Location: AllAircraft');
  }
 }
$registration_f=htmlspecialchars($registration_f,ENT_QUOTES);
$rego_short_f=htmlspecialchars($rego_short_f,ENT_QUOTES);
$make_model_f=htmlspecialchars($make_model_f,ENT_QUOTES);
$serial_f=htmlspecialchars($serial_f,ENT_QUOTES);
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
echo "<tr><td class='desc'>REGISTRATION</td><td>*</td>";
echo "<td>";
echo "<input ";
if (strlen($registration_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='registration_i' ";echo "Value='";echo $registration_f;echo "' ";echo "maxlength='6'";echo " autofocus ";echo ">";echo "</td>";echo "<td>";
echo $registration_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>REGISTRATION SHORT FORM (3 Letters)</td><td>*</td>";
echo "<td>";
echo "<input ";
if (strlen($rego_short_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='rego_short_i' ";echo "Value='";echo $rego_short_f;echo "' ";echo "maxlength='3'";echo ">";echo "</td>";echo "<td>";
echo $rego_short_err; echo "</td></tr>";
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
     $qs= "SELECT * FROM aircrafttype";if($_SESSION['org'] > 0) {$qs .= " WHERE aircrafttype.org = " . $_SESSION['org'] . " ";}$qs .= " ORDER BY name ASC";
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
echo "<tr><td class='desc'>DESCRIPTION</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($make_model_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='make_model_i' ";echo "Value='";echo $make_model_f;echo "' ";echo "maxlength='30'";echo ">";echo "</td>";echo "<td>";
echo $make_model_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>NUMBER OF SEATS</td><td>*</td>";
echo "<td>";
echo "<input ";
if (strlen($seats_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='seats_i' ";echo "Value='";echo $seats_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $seats_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>SERIAL NUMBER</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($serial_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='serial_i' ";echo "Value='";echo $serial_f;echo "' ";echo "maxlength='30'";echo ">";echo "</td>";echo "<td>";
echo $serial_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>CLUB GLIDER</td><td></td>";
echo "<td><input type='checkbox' name='club_glider_i[]' Value='1' ";if ($club_glider_f ==1) echo "checked";echo "></td>";echo "<td>";
echo $club_glider_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>BOOKABLE</td><td></td>";
echo "<td><input type='checkbox' name='bookable_i[]' Value='1' ";if ($bookable_f ==1) echo "checked";echo "></td>";echo "<td>";
echo $bookable_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>CHARGE PER MINUTE</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($charge_per_minute_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='charge_per_minute_i' ";echo "Value='";echo $charge_per_minute_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $charge_per_minute_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>MAX MINUTES CHARGE</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($max_perflight_charge_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='max_perflight_charge_i' ";echo "Value='";echo $max_perflight_charge_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $max_perflight_charge_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>NEXT ANNUAL</td><td></td>";
echo "<td><input type='date' name='next_annual_i' Value='" . substr($next_annual_f,0,10) . "'></td>";
echo "<td>";
echo $next_annual_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>NEXT SUPPLEMENTARY</td><td></td>";
echo "<td><input type='date' name='next_supplementary_i' Value='" . substr($next_supplementary_f,0,10) . "'></td>";
echo "<td>";
echo $next_supplementary_err; echo "</td></tr>";
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
