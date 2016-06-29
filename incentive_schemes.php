<?php session_start(); ?>
<?php
$org=0;
if(isset($_SESSION['org'])) $org=$_SESSION['org'];
if(isset($_SESSION['security'])){
 if (!($_SESSION['security'] & 8)){die("Secruity level too low for this page");}
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
$pageid=9;
$errtext="";
$sqltext="";
$error=0;
$trantype="Create";
$recid=-1;
$id_f="";
$id_err="";
$name_f="";
$name_err="";
$specific_glider_list_f="";
$specific_glider_list_err="";
$rate_glider_f="";
$rate_glider_err="";
$charge_tow_f="";
$charge_tow_err="";
$charge_airways_f="";
$charge_airways_err="";
$cost_f="";
$cost_err="";
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
    $q = "SELECT * FROM incentive_schemes WHERE id = " . $recid ;
    $r = mysqli_query($con,$q);
    $row = mysqli_fetch_array($r);
    if ($_SESSION['org'] > 0 && $row['org'] != $_SESSION['org'])
       die("Record does not exist");
    $id_f = $row['id'];
    $name_f = htmlspecialchars($row['name'],ENT_QUOTES);
    $specific_glider_list_f = htmlspecialchars($row['specific_glider_list'],ENT_QUOTES);
    $rate_glider_f = $row['rate_glider'];
    $charge_tow_f = $row['charge_tow'];
    $charge_airways_f = $row['charge_airways'];
    $cost_f = $row['cost'];
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
 $name_err = "";
 $specific_glider_list_err = "";
 $rate_glider_err = "";
 $charge_tow_err = "";
 $charge_airways_err = "";
 $cost_err = "";
 $name_f = InputChecker($_POST["name_i"]);
 $specific_glider_list_f = InputChecker($_POST["specific_glider_list_i"]);
 $rate_glider_f = InputChecker($_POST["rate_glider_i"]);
 if (!empty($rate_glider_f ) ) {if (!is_numeric($rate_glider_f ) ) {$rate_glider_err = "RATE FOR GLIDER USE (PER MIN) is not numeric";$error = 1;}}
if(in_array("1",$_POST['charge_tow_i']))
 $charge_tow_f = 1;
else
 $charge_tow_f = 0;
 if (!empty($charge_tow_f ) ) {if (!is_numeric($charge_tow_f ) ) {$charge_tow_err = "PAY FOR TOWS is not numeric";$error = 1;}}
if(in_array("1",$_POST['charge_airways_i']))
 $charge_airways_f = 1;
else
 $charge_airways_f = 0;
 if (!empty($charge_airways_f ) ) {if (!is_numeric($charge_airways_f ) ) {$charge_airways_err = "PAY AIRWAYS CHARGE is not numeric";$error = 1;}}
 $cost_f = InputChecker($_POST["cost_i"]);
 if (!empty($cost_f ) ) {if (!is_numeric($cost_f ) ) {$cost_err = "COST is not numeric";$error = 1;}}
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
       $Q="DELETE FROM incentive_schemes WHERE id = " . $_POST['updateid'] ;}}
     if (isset($_POST["tran"]) ) {if ($_POST["tran"] == "Update"){
      $Q = "UPDATE incentive_schemes SET ";
      $Q .= "name=";
      $Q .= "'" . mysqli_real_escape_string($con, $name_f)  . "'";
      $Q .= ",specific_glider_list=";
      $Q .= "'" . mysqli_real_escape_string($con, $specific_glider_list_f)  . "'";
      $Q .= ",rate_glider=";
      $Q .= "'" . $rate_glider_f . "'";
      $Q .= ",charge_tow=";
      $Q .= "'" . $charge_tow_f . "'";
      $Q .= ",charge_airways=";
      $Q .= "'" . $charge_airways_f . "'";
      $Q .= ",cost=";
      $Q .= "'" . $cost_f . "'";
$Q .= " WHERE ";$Q .= "id ";$Q .= "= ";
$Q .= $_POST['updateid'];}
     else
     if ($_POST["tran"] == "Create"){
       $Q = "INSERT INTO incentive_schemes (";$Q .= "org";$Q .= ", name";$Q .= ", specific_glider_list";$Q .= ", rate_glider";$Q .= ", charge_tow";$Q .= ", charge_airways";$Q .= ", cost";$Q .= " ) VALUES (";
$Q.=$_SESSION['org'];       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $name_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $specific_glider_list_f) . "'";
       $Q.= ",";
       $Q .= "'" . $rate_glider_f . "'";
       $Q.= ",";
       $Q .= "'" . $charge_tow_f . "'";
       $Q.= ",";
       $Q .= "'" . $charge_airways_f . "'";
       $Q.= ",";
       $Q .= "'" . $cost_f . "'";
      $Q.= ")";
    }}
    $sqltext = $Q;
    if(!mysqli_query($con,$Q) )
    {
       $errtext = "Database entry: " . mysqli_error($con) . "<br>" . $Q;
    }
    mysqli_close($con);
    if ($_POST["tran"] == "Delete")
     header('Location: IncentiveSchemes');
    if ($_POST["tran"] == "Update")
     header('Location: IncentiveSchemes');
  }
 }
$name_f=htmlspecialchars($name_f,ENT_QUOTES);
$specific_glider_list_f=htmlspecialchars($specific_glider_list_f,ENT_QUOTES);
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
echo "<tr><td class='desc'>SCHEME NAME</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($name_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='name_i' ";echo "size='40' ";echo "Value='";echo $name_f;echo "' ";echo " autofocus ";echo ">";echo "</td>";echo "<td>";
echo $name_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>ONLY APPILES TO THESE GLIDERS (Separate with Commas)</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($specific_glider_list_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='specific_glider_list_i' ";echo "Value='";echo $specific_glider_list_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $specific_glider_list_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>RATE FOR GLIDER USE (PER MIN)</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($rate_glider_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='rate_glider_i' ";echo "size='8' ";echo "Value='";echo $rate_glider_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $rate_glider_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>PAY FOR TOWS</td><td></td>";
echo "<td><input type='checkbox' name='charge_tow_i[]' Value='1' ";if ($charge_tow_f ==1) echo "checked";echo "></td>";echo "<td>";
echo $charge_tow_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>PAY AIRWAYS CHARGE</td><td></td>";
echo "<td><input type='checkbox' name='charge_airways_i[]' Value='1' ";if ($charge_airways_f ==1) echo "checked";echo "></td>";echo "<td>";
echo $charge_airways_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>COST</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($cost_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='cost_i' ";echo "size='9' ";echo "Value='";echo $cost_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $cost_err; echo "</td></tr>";
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
