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
$pageid=69;
$errtext="";
$sqltext="";
$error=0;
$trantype="Create";
$recid=-1;
$id_f="";
$id_err="";
$name_f="";
$name_err="";
$region_f="";
$region_err="";
$type_f="";
$type_err="";
$class_f="";
$class_err="";
$upper_height_f="";
$upper_height_err="";
$Lower_height_f="";
$Lower_height_err="";
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
    $q = "SELECT * FROM airspace WHERE id = " . $recid ;
    $r = mysqli_query($con,$q);
    $row = mysqli_fetch_array($r);
    $id_f = $row['id'];
    $name_f = htmlspecialchars($row['name'],ENT_QUOTES);
    $region_f = htmlspecialchars($row['region'],ENT_QUOTES);
    $type_f = htmlspecialchars($row['type'],ENT_QUOTES);
    $class_f = htmlspecialchars($row['class'],ENT_QUOTES);
    $upper_height_f = $row['upper_height'];
    $Lower_height_f = $row['Lower_height'];
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
 $region_err = "";
 $type_err = "";
 $class_err = "";
 $upper_height_err = "";
 $Lower_height_err = "";
 $name_f = InputChecker($_POST["name_i"]);
 $region_f = InputChecker($_POST["region_i"]);
 $type_f = InputChecker($_POST["type_i"]);
 $class_f = InputChecker($_POST["class_i"]);
 $upper_height_f = InputChecker($_POST["upper_height_i"]);
 $Lower_height_f = InputChecker($_POST["Lower_height_i"]);
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
       $Q="DELETE FROM airspace WHERE id = " . $_POST['updateid'] ;}}
     if (isset($_POST["tran"]) ) {if ($_POST["tran"] == "Update"){
      $Q = "UPDATE airspace SET ";
      $Q .= "name=";
      $Q .= "'" . mysqli_real_escape_string($con, $name_f)  . "'";
      $Q .= ",region=";
      $Q .= "'" . mysqli_real_escape_string($con, $region_f)  . "'";
      $Q .= ",type=";
      $Q .= "'" . mysqli_real_escape_string($con, $type_f)  . "'";
      $Q .= ",class=";
      $Q .= "'" . mysqli_real_escape_string($con, $class_f)  . "'";
      $Q .= ",upper_height=";
      $Q .= "'" . $upper_height_f . "'";
      $Q .= ",Lower_height=";
      $Q .= "'" . $Lower_height_f . "'";
$Q .= " WHERE ";$Q .= "id ";$Q .= "= ";
$Q .= $_POST['updateid'];}
     else
     if ($_POST["tran"] == "Create"){
       $Q = "INSERT INTO airspace (";$Q .= "name";$Q .= ", region";$Q .= ", type";$Q .= ", class";$Q .= ", upper_height";$Q .= ", Lower_height";$Q .= " ) VALUES (";
       $Q .= "'" . mysqli_real_escape_string($con, $name_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $region_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $type_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $class_f) . "'";
       $Q.= ",";
       $Q .= "'" . $upper_height_f . "'";
       $Q.= ",";
       $Q .= "'" . $Lower_height_f . "'";
      $Q.= ")";
    }}
    $sqltext = $Q;
    if(!mysqli_query($con,$Q) )
    {
       $errtext = "Database entry: " . mysqli_error($con) . "<br>" . $Q;
    }
    mysqli_close($con);
    if ($_POST["tran"] == "Delete")
     header('Location: airspace-list.php');
    if ($_POST["tran"] == "Update")
     header('Location: airspace-list.php');
  }
 }
$name_f=htmlspecialchars($name_f,ENT_QUOTES);
$region_f=htmlspecialchars($region_f,ENT_QUOTES);
$type_f=htmlspecialchars($type_f,ENT_QUOTES);
$class_f=htmlspecialchars($class_f,ENT_QUOTES);
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
echo "<tr><td class='desc'>NAME</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($name_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='name_i' ";echo "Value='";echo $name_f;echo "' ";echo " autofocus ";echo ">";echo "</td>";echo "<td>";
echo $name_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>REGION</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($region_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='region_i' ";echo "Value='";echo $region_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $region_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>TYPE</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($type_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='type_i' ";echo "Value='";echo $type_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $type_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>CLASS</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($class_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='class_i' ";echo "Value='";echo $class_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $class_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>UPPER HEIGHT</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($upper_height_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='upper_height_i' ";echo "Value='";echo $upper_height_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $upper_height_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>LOWE HEIGHT</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($Lower_height_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='Lower_height_i' ";echo "Value='";echo $Lower_height_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $Lower_height_err; echo "</td></tr>";
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
