<?php session_start(); ?>
<?php
$org=0;
if(isset($_SESSION['org'])) $org=$_SESSION['org'];
if(isset($_SESSION['security'])){
 if (!($_SESSION['security'] & 1)){die("Secruity level too low for this page");}
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
$pageid=53;
$errtext="";
$sqltext="";
$error=0;
$trantype="Create";
$recid=-1;
$id_f="";
$id_err="";
$user_f="";
$user_err="";
$create_time_f=$dateStr;
$create_time_err="";
$trip_id_f="";
$trip_id_err="";
$glider_f="";
$glider_err="";
$point_id_f="";
$point_id_err="";
$point_time_f="";
$point_time_err="";
$point_time_milli_f="";
$point_time_milli_err="";
$lattitude_f="";
$lattitude_err="";
$longitude_f="";
$longitude_err="";
$altitude_f="";
$altitude_err="";
$accuracy_f="";
$accuracy_err="";
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
    $q = "SELECT * FROM tracks WHERE id = " . $recid ;
    $r = mysqli_query($con,$q);
    $row = mysqli_fetch_array($r);
    if ($_SESSION['org'] > 0 && $row['org'] != $_SESSION['org'])
       die("Record does not exist");
    $id_f = $row['id'];
    $user_f = $row['user'];
    $create_time_f = $row['create_time'];
    $trip_id_f = $row['trip_id'];
    $glider_f = htmlspecialchars($row['glider'],ENT_QUOTES);
    $point_id_f = $row['point_id'];
    $point_time_f = $row['point_time'];
    $point_time_milli_f = $row['point_time_milli'];
    $lattitude_f = $row['lattitude'];
    $longitude_f = $row['longitude'];
    $altitude_f = $row['altitude'];
    $accuracy_f = $row['accuracy'];
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
 $user_err = "";
 $create_time_err = "";
 $trip_id_err = "";
 $glider_err = "";
 $point_id_err = "";
 $point_time_err = "";
 $point_time_milli_err = "";
 $lattitude_err = "";
 $longitude_err = "";
 $altitude_err = "";
 $accuracy_err = "";
 $user_f = InputChecker($_POST["user_i"]);
 $create_time_f = InputChecker($_POST["create_time_i"]);
 $trip_id_f = InputChecker($_POST["trip_id_i"]);
 if (!empty($trip_id_f ) ) {if (!is_numeric($trip_id_f ) ) {$trip_id_err = "TRIP ID is not numeric";$error = 1;}}
 $glider_f = InputChecker($_POST["glider_i"]);
 $point_id_f = InputChecker($_POST["point_id_i"]);
 if (!empty($point_id_f ) ) {if (!is_numeric($point_id_f ) ) {$point_id_err = "POINT ID is not numeric";$error = 1;}}
 $point_time_f = InputChecker($_POST["point_time_i"]);
 $point_time_milli_f = InputChecker($_POST["point_time_milli_i"]);
 $lattitude_f = InputChecker($_POST["lattitude_i"]);
 $longitude_f = InputChecker($_POST["longitude_i"]);
 $altitude_f = InputChecker($_POST["altitude_i"]);
 $accuracy_f = InputChecker($_POST["accuracy_i"]);
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
       $Q="DELETE FROM tracks WHERE id = " . $_POST['updateid'] ;}}
     if (isset($_POST["tran"]) ) {if ($_POST["tran"] == "Update"){
      $Q = "UPDATE tracks SET ";
      $Q .= "user=";
       if ($user_f ==0) $Q .= "null"; else {$Q .= "'";$Q .= "$user_f"; $Q .= "'"; }
      $Q .= ",create_time=";
      $Q .= "'" . $create_time_f . "'";
      $Q .= ",trip_id=";
      $Q .= "'" . $trip_id_f . "'";
      $Q .= ",glider=";
      $Q .= "'" . mysqli_real_escape_string($con, $glider_f)  . "'";
      $Q .= ",point_id=";
      $Q .= "'" . $point_id_f . "'";
      $Q .= ",point_time=";
      $Q .= "'" . $point_time_f . "'";
      $Q .= ",point_time_milli=";
      $Q .= "'" . $point_time_milli_f . "'";
      $Q .= ",lattitude=";
      $Q .= "'" . $lattitude_f . "'";
      $Q .= ",longitude=";
      $Q .= "'" . $longitude_f . "'";
      $Q .= ",altitude=";
      $Q .= "'" . $altitude_f . "'";
      $Q .= ",accuracy=";
      $Q .= "'" . $accuracy_f . "'";
$Q .= " WHERE ";$Q .= "id ";$Q .= "= ";
$Q .= $_POST['updateid'];}
     else
     if ($_POST["tran"] == "Create"){
       $Q = "INSERT INTO tracks (";$Q .= "org";$Q .= ", user";$Q .= ", create_time";$Q .= ", trip_id";$Q .= ", glider";$Q .= ", point_id";$Q .= ", point_time";$Q .= ", point_time_milli";$Q .= ", lattitude";$Q .= ", longitude";$Q .= ", altitude";$Q .= ", accuracy";$Q .= " ) VALUES (";
$Q.=$_SESSION['org'];       $Q.= ",";
       if ($user_f ==0)$Q .= "null"; else{$Q .= "'";$Q .= $user_f;$Q .= "'";}
       $Q.= ",";
       $Q .= "'" . $create_time_f . "'";
       $Q.= ",";
       $Q .= "'" . $trip_id_f . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $glider_f) . "'";
       $Q.= ",";
       $Q .= "'" . $point_id_f . "'";
       $Q.= ",";
       $Q .= "'" . $point_time_f . "'";
       $Q.= ",";
       $Q .= "'" . $point_time_milli_f . "'";
       $Q.= ",";
       $Q .= "'" . $lattitude_f . "'";
       $Q.= ",";
       $Q .= "'" . $longitude_f . "'";
       $Q.= ",";
       $Q .= "'" . $altitude_f . "'";
       $Q.= ",";
       $Q .= "'" . $accuracy_f . "'";
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
$glider_f=htmlspecialchars($glider_f,ENT_QUOTES);
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
echo "<tr><td class='desc'>USER</td><td></td>";
echo "<td><select name='user_i'>";
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (mysqli_connect_errno())
   {
    $errtext= "Failed to connect to Database: " . mysqli_connect_error();
   }
   else
   {
     echo "<option value='0'></option>";
     $qs= "SELECT * FROM users";if($_SESSION['org'] > 0) {$qs .= " WHERE users.org = " . $_SESSION['org'] . " ";}$qs .= " ORDER BY name ASC";
     $r = mysqli_query($con,$qs);
     while($row = mysqli_fetch_array($r))
     {
     if ($user_f == $row['id'])
     echo "<option value='" . $row['id'] ."' selected>" . $row['name'] ."</option>" ; 
    else
     echo "<option value='" . $row['id'] ."'>" . $row['name'] ."</option>" ; 
      }
    mysqli_close($con);
   }
echo "</select></td>";echo "<td>";
echo $user_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>CREATE</td><td></td>";
echo "<td><input type='date' name='create_time_i' Value='" . substr($create_time_f,0,10) . "'></td>";
echo "<td>";
echo $create_time_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>TRIP ID</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($trip_id_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='trip_id_i' ";echo "Value='";echo $trip_id_f;echo "' ";echo " autofocus ";echo ">";echo "</td>";echo "<td>";
echo $trip_id_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>GLIDER</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($glider_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='glider_i' ";echo "Value='";echo $glider_f;echo "' ";echo "maxlength='7'";echo ">";echo "</td>";echo "<td>";
echo $glider_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>POINT ID</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($point_id_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='point_id_i' ";echo "Value='";echo $point_id_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $point_id_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>TIME</td><td></td>";
echo "<td><input type='datetime' name='point_time_i' Value='" . $point_time_f . "'></td>";
echo "<td>";
echo $point_time_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>TIME MIILI</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($point_time_milli_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='point_time_milli_i' ";echo "Value='";echo $point_time_milli_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $point_time_milli_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>LATTITUDE</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($lattitude_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='lattitude_i' ";echo "Value='";echo $lattitude_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $lattitude_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>LONGITUDE</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($longitude_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='longitude_i' ";echo "Value='";echo $longitude_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $longitude_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>ALTITUDE</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($altitude_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='altitude_i' ";echo "Value='";echo $altitude_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $altitude_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>ACCURACY</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($accuracy_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='accuracy_i' ";echo "Value='";echo $accuracy_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $accuracy_err; echo "</td></tr>";
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
