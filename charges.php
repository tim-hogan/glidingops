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
$dateTimeZone = new DateTimeZone($_SESSION['timezone']);
$dateTime = new DateTime('now', $dateTimeZone);
$dateStr = $dateTime->format('Y-m-d');
$timeoffset = $dateTime->getOffset();
$pageid=47;
$errtext="";
$sqltext="";
$error=0;
$trantype="Create";
$recid=-1;
$id_f="";
$id_err="";
$name_f="";
$name_err="";
$location_f="";
$location_err="";
$validfrom_f=$dateStr;
$validfrom_err="";
$amount_f="";
$amount_err="";
$every_flight_f="";
$every_flight_err="";
$max_once_per_day_f="";
$max_once_per_day_err="";
$monthly_f="";
$monthly_err="";
$comments_f="";
$comments_err="";
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
    $q = "SELECT * FROM charges WHERE id = " . $recid ;
    $r = mysqli_query($con,$q);
    $row = mysqli_fetch_array($r);
    if ($_SESSION['org'] > 0 && $row['org'] != $_SESSION['org'])
       die("Record does not exist");
    $id_f = $row['id'];
    $name_f = htmlspecialchars($row['name'],ENT_QUOTES);
    $location_f = htmlspecialchars($row['location'],ENT_QUOTES);
    $validfrom_f = $row['validfrom'];
    $amount_f = $row['amount'];
    $every_flight_f = $row['every_flight'];
    $max_once_per_day_f = $row['max_once_per_day'];
    $monthly_f = $row['monthly'];
    $comments_f = htmlspecialchars($row['comments'],ENT_QUOTES);
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
 $name_err = "";
 $location_err = "";
 $validfrom_err = "";
 $amount_err = "";
 $every_flight_err = "";
 $max_once_per_day_err = "";
 $monthly_err = "";
 $comments_err = "";
 $name_f = InputChecker($_POST["name_i"]);
 if (empty($name_f) )
 {
  $name_err = "CHARGE NAME is required";
  $error = 1;
 }
 $location_f = InputChecker($_POST["location_i"]);
 $validfrom_f = InputChecker($_POST["validfrom_i"]);
 $amount_f = InputChecker($_POST["amount_i"]);
 if (empty($amount_f) )
 {
  $amount_err = "AMMOUNT is required";
  $error = 1;
 }
 if (!empty($amount_f ) ) {if (!is_numeric($amount_f ) ) {$amount_err = "AMMOUNT is not numeric";$error = 1;}}
if(in_array("1",$_POST['every_flight_i']))
 $every_flight_f = 1;
else
 $every_flight_f = 0;
if(in_array("1",$_POST['max_once_per_day_i']))
 $max_once_per_day_f = 1;
else
 $max_once_per_day_f = 0;
if(in_array("1",$_POST['monthly_i']))
 $monthly_f = 1;
else
 $monthly_f = 0;
 $comments_f = InputChecker($_POST["comments_i"]);
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
       $Q="DELETE FROM charges WHERE id = " . $_POST['updateid'] ;}}
     if (isset($_POST["tran"]) ) {if ($_POST["tran"] == "Update"){
      $Q = "UPDATE charges SET ";
      $Q .= "name=";
      $Q .= "'" . mysqli_real_escape_string($con, $name_f)  . "'";
      $Q .= ",location=";
      $Q .= "'" . mysqli_real_escape_string($con, $location_f)  . "'";
      $Q .= ",validfrom=";
      $Q .= "'" . $validfrom_f . "'";
      $Q .= ",amount=";
      $Q .= "'" . $amount_f . "'";
      $Q .= ",every_flight=";
      $Q .= "'" . $every_flight_f . "'";
      $Q .= ",max_once_per_day=";
      $Q .= "'" . $max_once_per_day_f . "'";
      $Q .= ",monthly=";
      $Q .= "'" . $monthly_f . "'";
      $Q .= ",comments=";
      $Q .= "'" . mysqli_real_escape_string($con, $comments_f)  . "'";
$Q .= " WHERE ";$Q .= "id ";$Q .= "= ";
$Q .= $_POST['updateid'];}
     else
     if ($_POST["tran"] == "Create"){
       $Q = "INSERT INTO charges (";$Q .= "org";$Q .= ", name";$Q .= ", location";$Q .= ", validfrom";$Q .= ", amount";$Q .= ", every_flight";$Q .= ", max_once_per_day";$Q .= ", monthly";$Q .= ", comments";$Q .= " ) VALUES (";
$Q.=$_SESSION['org'];       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $name_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $location_f) . "'";
       $Q.= ",";
       $Q .= "'" . $validfrom_f . "'";
       $Q.= ",";
       $Q .= "'" . $amount_f . "'";
       $Q.= ",";
       $Q .= "'" . $every_flight_f . "'";
       $Q.= ",";
       $Q .= "'" . $max_once_per_day_f . "'";
       $Q.= ",";
       $Q .= "'" . $monthly_f . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $comments_f) . "'";
      $Q.= ")";
    }}
    $sqltext = $Q;
    if(!mysqli_query($con,$Q) )
    {
       $errtext = "Database entry: " . mysqli_error($con) . "<br>" . $Q;
    }
    mysqli_close($con);
    if ($_POST["tran"] == "Delete")
     header('Location: OtherCharges');
  }
 }
$name_f=htmlspecialchars($name_f,ENT_QUOTES);
$location_f=htmlspecialchars($location_f,ENT_QUOTES);
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
echo "<tr><td class='desc'>CHARGE NAME</td><td>*</td>";
echo "<td>";
echo "<input ";
if (strlen($name_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='name_i' ";echo "size='30' ";echo "Value='";echo $name_f;echo "' ";echo " autofocus ";echo ">";echo "</td>";echo "<td>";
echo $name_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>LOCATION</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($location_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='location_i' ";echo "size='40' ";echo "Value='";echo $location_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $location_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>VALID FROM</td><td></td>";
echo "<td><input type='date' name='validfrom_i' Value='" . substr($validfrom_f,0,10) . "'></td>";
echo "<td>";
echo $validfrom_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>AMMOUNT</td><td>*</td>";
echo "<td>";
echo "<input ";
if (strlen($amount_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='amount_i' ";echo "size='8' ";echo "Value='";echo $amount_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $amount_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>CHARGE EVERY FLIGHT</td><td></td>";
echo "<td><input type='checkbox' name='every_flight_i[]' Value='1' ";if ($every_flight_f ==1) echo "checked";echo "></td>";echo "<td>";
echo $every_flight_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>MAX ONCE PER DAY</td><td></td>";
echo "<td><input type='checkbox' name='max_once_per_day_i[]' Value='1' ";if ($max_once_per_day_f ==1) echo "checked";echo "></td>";echo "<td>";
echo $max_once_per_day_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>MONTHLY</td><td></td>";
echo "<td><input type='checkbox' name='monthly_i[]' Value='1' ";if ($monthly_f ==1) echo "checked";echo "></td>";echo "<td>";
echo $monthly_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>COMMENTS</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($comments_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='comments_i' ";echo "size='40' ";echo "Value='";echo $comments_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $comments_err; echo "</td></tr>";
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
