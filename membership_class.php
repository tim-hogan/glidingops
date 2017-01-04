<?php session_start(); ?>
<?php
$org=0;
if(isset($_SESSION['org'])) $org=$_SESSION['org'];
if(isset($_SESSION['security'])){
 if (!($_SESSION['security'] & 64)){die("Secruity level too low for this page");}
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
$pageid=7;
$errtext="";
$sqltext="";
$error=0;
$trantype="Create";
$recid=-1;
$id_f="";
$id_err="";
$class_f="";
$class_err="";
$disp_message_broadcast_f="";
$disp_message_broadcast_err="";
$dailysheet_dropdown_f="";
$dailysheet_dropdown_err="";
$email_broadcast_f="";
$email_broadcast_err="";
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
    $q = "SELECT * FROM membership_class WHERE id = " . $recid ;
    $r = mysqli_query($con,$q);
    $row = mysqli_fetch_array($r);
    if ($_SESSION['org'] > 0 && $row['org'] != $_SESSION['org'])
       die("Record does not exist");
    $id_f = $row['id'];
    $class_f = htmlspecialchars($row['class'],ENT_QUOTES);
    $disp_message_broadcast_f = $row['disp_message_broadcast'];
    $dailysheet_dropdown_f = $row['dailysheet_dropdown'];
    $email_broadcast_f = $row['email_broadcast'];
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
 $class_err = "";
 $disp_message_broadcast_err = "";
 $dailysheet_dropdown_err = "";
 $email_broadcast_err = "";
 $class_f = InputChecker($_POST["class_i"]);
if(in_array("1",$_POST['disp_message_broadcast_i']))
 $disp_message_broadcast_f = 1;
else
 $disp_message_broadcast_f = 0;
 if (!empty($disp_message_broadcast_f ) ) {if (!is_numeric($disp_message_broadcast_f ) ) {$disp_message_broadcast_err = "Disiplay on Message Broadcast Screeen is not numeric";$error = 1;}}
if(in_array("1",$_POST['dailysheet_dropdown_i']))
 $dailysheet_dropdown_f = 1;
else
 $dailysheet_dropdown_f = 0;
if(in_array("1",$_POST['email_broadcast_i']))
 $email_broadcast_f = 1;
else
 $email_broadcast_f = 0;
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
       $Q="DELETE FROM membership_class WHERE id = " . $_POST['updateid'] ;}}
     if (isset($_POST["tran"]) ) {if ($_POST["tran"] == "Update"){
      $Q = "UPDATE membership_class SET ";
      $Q .= "class=";
      $Q .= "'" . mysqli_real_escape_string($con, $class_f)  . "'";
      $Q .= ",disp_message_broadcast=";
      $Q .= "'" . $disp_message_broadcast_f . "'";
      $Q .= ",dailysheet_dropdown=";
      $Q .= "'" . $dailysheet_dropdown_f . "'";
      $Q .= ",email_broadcast=";
      $Q .= "'" . $email_broadcast_f . "'";
$Q .= " WHERE ";$Q .= "id ";$Q .= "= ";
$Q .= $_POST['updateid'];}
     else
     if ($_POST["tran"] == "Create"){
       $Q = "INSERT INTO membership_class (";$Q .= "org";$Q .= ", class";$Q .= ", disp_message_broadcast";$Q .= ", dailysheet_dropdown";$Q .= ", email_broadcast";$Q .= " ) VALUES (";
$Q.=$_SESSION['org'];       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $class_f) . "'";
       $Q.= ",";
       $Q .= "'" . $disp_message_broadcast_f . "'";
       $Q.= ",";
       $Q .= "'" . $dailysheet_dropdown_f . "'";
       $Q.= ",";
       $Q .= "'" . $email_broadcast_f . "'";
      $Q.= ")";
    }}
    $sqltext = $Q;
    if(!mysqli_query($con,$Q) )
    {
       $errtext = "Database entry: " . mysqli_error($con) . "<br>" . $Q;
    }
    mysqli_close($con);
    if ($_POST["tran"] == "Delete")
     header('Location: membership_class-list.php');
    if ($_POST["tran"] == "Update")
     header('Location: membership_class-list.php');
  }
 }
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
echo "<tr><td class='desc'>CLASS</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($class_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='class_i' ";echo "Value='";echo $class_f;echo "' ";echo " autofocus ";echo ">";echo "</td>";echo "<td>";
echo $class_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>Disiplay on Message Broadcast Screeen</td><td></td>";
echo "<td><input type='checkbox' name='disp_message_broadcast_i[]' Value='1' ";if ($disp_message_broadcast_f ==1) echo "checked";echo "></td>";echo "<td>";
echo $disp_message_broadcast_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>Show member on daily sheet dropdown</td><td></td>";
echo "<td><input type='checkbox' name='dailysheet_dropdown_i[]' Value='1' ";if ($dailysheet_dropdown_f ==1) echo "checked";echo "></td>";echo "<td>";
echo $dailysheet_dropdown_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>Allow broadcast to member by email</td><td></td>";
echo "<td><input type='checkbox' name='email_broadcast_i[]' Value='1' ";if ($email_broadcast_f ==1) echo "checked";echo "></td>";echo "<td>";
echo $email_broadcast_err; echo "</td></tr>";
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
