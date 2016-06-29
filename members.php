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
$pageid=11;
$errtext="";
$sqltext="";
$error=0;
$trantype="Create";
$recid=-1;
$id_f="";
$id_err="";
$firstname_f="";
$firstname_err="";
$surname_f="";
$surname_err="";
$displayname_f="";
$displayname_err="";
$gnz_number_f="";
$gnz_number_err="";
$qgp_number_f="";
$qgp_number_err="";
$class_f="";
$class_err="";
$phone_mobile_f="";
$phone_mobile_err="";
$email_f="";
$email_err="";
$gone_solo_f="";
$gone_solo_err="";
$enable_text_f="";
$enable_text_err="";
$enable_email_f="";
$enable_email_err="";
$medical_expire_f=$dateStr;
$medical_expire_err="";
$bfr_expire_f=$dateStr;
$bfr_expire_err="";
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
    $q = "SELECT * FROM members WHERE id = " . $recid ;
    $r = mysqli_query($con,$q);
    $row = mysqli_fetch_array($r);
    if ($_SESSION['org'] > 0 && $row['org'] != $_SESSION['org'])
       die("Record does not exist");
    $id_f = $row['id'];
    $firstname_f = htmlspecialchars($row['firstname'],ENT_QUOTES);
    $surname_f = htmlspecialchars($row['surname'],ENT_QUOTES);
    $displayname_f = htmlspecialchars($row['displayname'],ENT_QUOTES);
    $gnz_number_f = $row['gnz_number'];
    $qgp_number_f = $row['qgp_number'];
    $class_f = $row['class'];
    $phone_mobile_f = htmlspecialchars($row['phone_mobile'],ENT_QUOTES);
    $email_f = htmlspecialchars($row['email'],ENT_QUOTES);
    $gone_solo_f = $row['gone_solo'];
    $enable_text_f = $row['enable_text'];
    $enable_email_f = $row['enable_email'];
    $medical_expire_f = $row['medical_expire'];
    $bfr_expire_f = $row['bfr_expire'];
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
 $firstname_err = "";
 $surname_err = "";
 $displayname_err = "";
 $gnz_number_err = "";
 $qgp_number_err = "";
 $class_err = "";
 $phone_mobile_err = "";
 $email_err = "";
 $gone_solo_err = "";
 $enable_text_err = "";
 $enable_email_err = "";
 $medical_expire_err = "";
 $bfr_expire_err = "";
 $firstname_f = InputChecker($_POST["firstname_i"]);
 if (empty($firstname_f) )
 {
  $firstname_err = "FIRSTNAME is required";
  $error = 1;
 }
 $surname_f = InputChecker($_POST["surname_i"]);
 if (empty($surname_f) )
 {
  $surname_err = "SURNAME is required";
  $error = 1;
 }
 $displayname_f = InputChecker($_POST["displayname_i"]);
 if (empty($displayname_f) )
 {
  $displayname_err = "DISPLAY NAME is required";
  $error = 1;
 }
 $gnz_number_f = InputChecker($_POST["gnz_number_i"]);
 if (!empty($gnz_number_f ) ) {if (!is_numeric($gnz_number_f ) ) {$gnz_number_err = "GNZ NUMBER is not numeric";$error = 1;}}
 $qgp_number_f = InputChecker($_POST["qgp_number_i"]);
 if (!empty($qgp_number_f ) ) {if (!is_numeric($qgp_number_f ) ) {$qgp_number_err = "QGP NUMBER is not numeric";$error = 1;}}
 $class_f = InputChecker($_POST["class_i"]);
 $phone_mobile_f = InputChecker($_POST["phone_mobile_i"]);
 $email_f = InputChecker($_POST["email_i"]);
if(in_array("1",$_POST['gone_solo_i']))
 $gone_solo_f = 1;
else
 $gone_solo_f = 0;
 if (!empty($gone_solo_f ) ) {if (!is_numeric($gone_solo_f ) ) {$gone_solo_err = "SOLO is not numeric";$error = 1;}}
if(in_array("1",$_POST['enable_text_i']))
 $enable_text_f = 1;
else
 $enable_text_f = 0;
 if (!empty($enable_text_f ) ) {if (!is_numeric($enable_text_f ) ) {$enable_text_err = "ENABLE TEXTS is not numeric";$error = 1;}}
if(in_array("1",$_POST['enable_email_i']))
 $enable_email_f = 1;
else
 $enable_email_f = 0;
 if (!empty($enable_email_f ) ) {if (!is_numeric($enable_email_f ) ) {$enable_email_err = "ENABLE EMALS is not numeric";$error = 1;}}
if ($_SESSION['security'] & 16) { $medical_expire_f = InputChecker($_POST["medical_expire_i"]);
}
if ($_SESSION['security'] & 16) { $bfr_expire_f = InputChecker($_POST["bfr_expire_i"]);
}
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
       $Q="DELETE FROM members WHERE id = " . $_POST['updateid'] ;}}
     if (isset($_POST["tran"]) ) {if ($_POST["tran"] == "Update"){
      $Q = "UPDATE members SET ";
      $Q .= "firstname=";
      $Q .= "'" . mysqli_real_escape_string($con, $firstname_f)  . "'";
      $Q .= ",surname=";
      $Q .= "'" . mysqli_real_escape_string($con, $surname_f)  . "'";
      $Q .= ",displayname=";
      $Q .= "'" . mysqli_real_escape_string($con, $displayname_f)  . "'";
      $Q .= ",gnz_number=";
      $Q .= "'" . $gnz_number_f . "'";
      $Q .= ",qgp_number=";
      $Q .= "'" . $qgp_number_f . "'";
      $Q .= ",class=";
       if ($class_f ==0) $Q .= "null"; else {$Q .= "'";$Q .= "$class_f"; $Q .= "'"; }
      $Q .= ",phone_mobile=";
      $Q .= "'" . mysqli_real_escape_string($con, $phone_mobile_f)  . "'";
      $Q .= ",email=";
      $Q .= "'" . mysqli_real_escape_string($con, $email_f)  . "'";
      $Q .= ",gone_solo=";
      $Q .= "'" . $gone_solo_f . "'";
      $Q .= ",enable_text=";
      $Q .= "'" . $enable_text_f . "'";
      $Q .= ",enable_email=";
      $Q .= "'" . $enable_email_f . "'";
if ($_SESSION['security'] & 16) {      $Q .= ",medical_expire=";
      $Q .= "'" . $medical_expire_f . "'";
}
if ($_SESSION['security'] & 16) {      $Q .= ",bfr_expire=";
      $Q .= "'" . $bfr_expire_f . "'";
}
$Q .= " WHERE ";$Q .= "id ";$Q .= "= ";
$Q .= $_POST['updateid'];}
     else
     if ($_POST["tran"] == "Create"){
       $Q = "INSERT INTO members (";$Q .= "org";$Q .= ", firstname";$Q .= ", surname";$Q .= ", displayname";$Q .= ", gnz_number";$Q .= ", qgp_number";$Q .= ", class";$Q .= ", phone_mobile";$Q .= ", email";$Q .= ", gone_solo";$Q .= ", enable_text";$Q .= ", enable_email";if ($_SESSION['security'] & 16) $Q .= ", medical_expire";if ($_SESSION['security'] & 16) $Q .= ", bfr_expire";$Q .= " ) VALUES (";
$Q.=$_SESSION['org'];       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $firstname_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $surname_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $displayname_f) . "'";
       $Q.= ",";
       $Q .= "'" . $gnz_number_f . "'";
       $Q.= ",";
       $Q .= "'" . $qgp_number_f . "'";
       $Q.= ",";
       if ($class_f ==0)$Q .= "null"; else{$Q .= "'";$Q .= $class_f;$Q .= "'";}
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $phone_mobile_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $email_f) . "'";
       $Q.= ",";
       $Q .= "'" . $gone_solo_f . "'";
       $Q.= ",";
       $Q .= "'" . $enable_text_f . "'";
       $Q.= ",";
       $Q .= "'" . $enable_email_f . "'";
if ($_SESSION['security'] & 16) {       $Q.= ",";
       $Q .= "'" . $medical_expire_f . "'";
}
if ($_SESSION['security'] & 16) {       $Q.= ",";
       $Q .= "'" . $bfr_expire_f . "'";
}
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
$firstname_f=htmlspecialchars($firstname_f,ENT_QUOTES);
$surname_f=htmlspecialchars($surname_f,ENT_QUOTES);
$displayname_f=htmlspecialchars($displayname_f,ENT_QUOTES);
$phone_mobile_f=htmlspecialchars($phone_mobile_f,ENT_QUOTES);
$email_f=htmlspecialchars($email_f,ENT_QUOTES);
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
echo "<tr><td class='desc'>FIRSTNAME</td><td>*</td>";
echo "<td>";
echo "<input ";
if (strlen($firstname_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='firstname_i' ";echo "size='30' ";echo "Value='";echo $firstname_f;echo "' ";echo "maxlength='40'";echo " autofocus ";echo ">";echo "</td>";echo "<td>";
echo $firstname_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>SURNAME</td><td>*</td>";
echo "<td>";
echo "<input ";
if (strlen($surname_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='surname_i' ";echo "size='30' ";echo "Value='";echo $surname_f;echo "' ";echo "maxlength='40'";echo ">";echo "</td>";echo "<td>";
echo $surname_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>DISPLAY NAME</td><td>*</td>";
echo "<td>";
echo "<input ";
if (strlen($displayname_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='displayname_i' ";echo "size='40' ";echo "Value='";echo $displayname_f;echo "' ";echo "maxlength='80'";echo ">";echo "</td>";echo "<td>";
echo $displayname_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>GNZ NUMBER</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($gnz_number_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='gnz_number_i' ";echo "size='10' ";echo "Value='";echo $gnz_number_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $gnz_number_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>QGP NUMBER</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($qgp_number_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='qgp_number_i' ";echo "size='10' ";echo "Value='";echo $qgp_number_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $qgp_number_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>CLASS</td><td></td>";
echo "<td><select name='class_i'>";
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (mysqli_connect_errno())
   {
    $errtext= "Failed to connect to Database: " . mysqli_connect_error();
   }
   else
   {
     echo "<option value='0'></option>";
     $qs= "SELECT * FROM membership_class";if($_SESSION['org'] > 0) {$qs .= " WHERE membership_class.org = " . $_SESSION['org'] . " ";}$qs .= " ORDER BY class ASC";
     $r = mysqli_query($con,$qs);
     while($row = mysqli_fetch_array($r))
     {
     if ($class_f == $row['id'])
     echo "<option value='" . $row['id'] ."' selected>" . $row['class'] ."</option>" ; 
    else
     echo "<option value='" . $row['id'] ."'>" . $row['class'] ."</option>" ; 
      }
    mysqli_close($con);
   }
echo "</select></td>";echo "<td>";
echo $class_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>MOBILE PHONE</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($phone_mobile_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='phone_mobile_i' ";echo "size='20' ";echo "Value='";echo $phone_mobile_f;echo "' ";echo "maxlength='30'";echo ">";echo "</td>";echo "<td>";
echo $phone_mobile_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>EMAIL</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($email_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='email_i' ";echo "size='50' ";echo "Value='";echo $email_f;echo "' ";echo "maxlength='50'";echo ">";echo "</td>";echo "<td>";
echo $email_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>SOLO</td><td></td>";
echo "<td><input type='checkbox' name='gone_solo_i[]' Value='1' ";if ($gone_solo_f ==1) echo "checked";echo "></td>";echo "<td>";
echo $gone_solo_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>ENABLE TEXTS</td><td></td>";
echo "<td><input type='checkbox' name='enable_text_i[]' Value='1' ";if ($enable_text_f ==1) echo "checked";echo "></td>";echo "<td>";
echo $enable_text_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>ENABLE EMALS</td><td></td>";
echo "<td><input type='checkbox' name='enable_email_i[]' Value='1' ";if ($enable_email_f ==1) echo "checked";echo "></td>";echo "<td>";
echo $enable_email_err; echo "</td></tr>";
}
?>
<?php if ($_SESSION['security'] & 16)
{
echo "<tr><td class='desc'>MEDICAL EXPIRES</td><td></td>";
echo "<td><input type='date' name='medical_expire_i' Value='" . substr($medical_expire_f,0,10) . "'></td>";
echo "<td>";
echo $medical_expire_err; echo "</td></tr>";
}
?>
<?php if ($_SESSION['security'] & 16)
{
echo "<tr><td class='desc'>BFR EXPIRES</td><td></td>";
echo "<td><input type='date' name='bfr_expire_i' Value='" . substr($bfr_expire_f,0,10) . "'></td>";
echo "<td>";
echo $bfr_expire_err; echo "</td></tr>";
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
