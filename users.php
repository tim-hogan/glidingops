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
<link rel="stylesheet" type="text/css" href="styleform1.css">
</head>
<body>
<script>function goBack() {window.history.back()}</script>
<?php
$DEBUG=0;
$pageid=3;
$errtext="";
$sqltext="";
$error=0;
$trantype="Create";
$recid=-1;
$id_f="";
$id_err="";
$name_f="";
$name_err="";
$usercode_f="";
$usercode_err="";
$password_f="";
$password_err="";
$org_f="";
$org_err="";
$expire_f=$dateStr;
$expire_err="";
$securitylevel_f="";
$securitylevel_err="";
$member_f="";
$member_err="";
$force_pw_reset_f="";
$force_pw_reset_err="";
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
    $q = "SELECT * FROM users WHERE id = " . $recid ;
    $r = mysqli_query($con,$q);
    $row = mysqli_fetch_array($r);
    if ($_SESSION['org'] > 0 && $row['org'] != $_SESSION['org'])
       die("Record does not exist");
    $id_f = $row['id'];
    $name_f = htmlspecialchars($row['name'],ENT_QUOTES);
    $usercode_f = htmlspecialchars($row['usercode'],ENT_QUOTES);
    $password_f = htmlspecialchars($row['password'],ENT_QUOTES);
    $org_f = $row['org'];
    $expire_f = $row['expire'];
    $expire_f = substr($expire_f, 0, -9);
    $securitylevel_f = $row['securitylevel'];
    $member_f = $row['member'];
    $force_pw_reset_f = $row['force_pw_reset'];
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
 $usercode_err = "";
 $password_err = "";
 $org_err = "";
 $expire_err = "";
 $securitylevel_err = "";
 $member_err = "";
 $force_pw_reset_err = "";
 $name_f = InputChecker($_POST["name_i"]);
 if (empty($name_f) )
 {
  $name_err = "NAME is required";
  $error = 1;
 }
 $usercode_f = InputChecker($_POST["usercode_i"]);
 if (empty($usercode_f) )
 {
  $usercode_err = "USERCODE is required";
  $error = 1;
 }
 $password_f = InputChecker($_POST["password_i"]);
if (!empty($password_f))
{
 $password_f = md5($password_f);
}
if ($_SESSION['security'] & 128) { $org_f = InputChecker($_POST["org_i"]);
}
 $expire_f = InputChecker($_POST["expire_i"]);
 if (empty($expire_f) )
 {
  $expire_err = "EXPIRES is required";
  $error = 1;
 }
 $securitylevel_f = InputChecker($_POST["securitylevel_i"]);
 if (empty($securitylevel_f) )
 {
  $securitylevel_err = "SECURITY LEVEL is required";
  $error = 1;
 }
 if (!empty($securitylevel_f ) ) {if (!is_numeric($securitylevel_f ) ) {$securitylevel_err = "SECURITY LEVEL is not numeric";$error = 1;}}
 $member_f = InputChecker($_POST["member_i"]);
if(in_array("1",$_POST['force_pw_reset_i']))
 $force_pw_reset_f = 1;
else
 $force_pw_reset_f = 0;
 if (!empty($force_pw_reset_f ) ) {if (!is_numeric($force_pw_reset_f ) ) {$force_pw_reset_err = "FORCE PW RESET is not numeric";$error = 1;}}
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
       $Q="DELETE FROM users WHERE id = " . $_POST['updateid'] ;}}
     if (isset($_POST["tran"]) ) {if ($_POST["tran"] == "Update"){
      $Q = "UPDATE users SET ";
      $Q .= "name=";
      $Q .= "'" . mysqli_real_escape_string($con, $name_f)  . "'";
      $Q .= ",usercode=";
      $Q .= "'" . mysqli_real_escape_string($con, $usercode_f)  . "'";
if (!empty($password_f))
{
      $Q .= ",password=";
      $Q .= "'" . mysqli_real_escape_string($con, $password_f)  . "'";
}
if ($_SESSION['security'] & 128) {      $Q .= ",org=";
       if ($org_f ==0) $Q .= "null"; else {$Q .= "'";$Q .= "$org_f"; $Q .= "'"; }
}
      $Q .= ",expire=";
      $Q .= "'" . $expire_f . "'";
      $Q .= ",securitylevel=";
      $Q .= "'" . $securitylevel_f . "'";
      $Q .= ",member=";
       if ($member_f ==0) $Q .= "null"; else {$Q .= "'";$Q .= "$member_f"; $Q .= "'"; }
      $Q .= ",force_pw_reset=";
      $Q .= "'" . $force_pw_reset_f . "'";
$Q .= " WHERE ";$Q .= "id ";$Q .= "= ";
$Q .= $_POST['updateid'];}
     else
     if ($_POST["tran"] == "Create"){
       $Q = "INSERT INTO users (";$Q .= "name";$Q .= ", usercode";$Q .= ", password";$Q .= ", org";$Q .= ", expire";$Q .= ", securitylevel";$Q .= ", member";$Q .= ", force_pw_reset";$Q .= " ) VALUES (";
       $Q .= "'" . mysqli_real_escape_string($con, $name_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $usercode_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $password_f) . "'";
if ($_SESSION['security'] & 128) {       $Q.= ",";
       if ($org_f ==0)$Q .= "null"; else{$Q .= "'";$Q .= $org_f;$Q .= "'";}
}else{$Q.= ",";$Q.=$_SESSION['org'];}
       $Q.= ",";
       $Q .= "'" . $expire_f . "'";
       $Q.= ",";
       $Q .= "'" . $securitylevel_f . "'";
       $Q.= ",";
       if ($member_f ==0)$Q .= "null"; else{$Q .= "'";$Q .= $member_f;$Q .= "'";}
       $Q.= ",";
       $Q .= "'" . $force_pw_reset_f . "'";
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
$name_f=htmlspecialchars($name_f,ENT_QUOTES);
$usercode_f=htmlspecialchars($usercode_f,ENT_QUOTES);
$password_f=htmlspecialchars($password_f,ENT_QUOTES);
}
?>
<div id='divform'>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<table>
<?php if (true)
{
echo "<tr><td class='desc'>USER NUMBER</td><td></td>";
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
if (strlen($name_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='name_i' ";echo "size='30' ";echo "Value='";echo $name_f;echo "' ";echo "maxlength='40'";echo " autofocus ";echo ">";echo "</td>";echo "<td>";
echo $name_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>USERCODE</td><td>*</td>";
echo "<td>";
echo "<input ";
if (strlen($usercode_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='usercode_i' ";echo "size='20' ";echo "Value='";echo $usercode_f;echo "' ";echo "maxlength='80'";echo ">";echo "</td>";echo "<td>";
echo $usercode_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>PASSWORD</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($password_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='password_i' ";echo "size='20' ";echo "maxlength='32'";echo ">";echo "</td>";echo "<td>";
echo $password_err; echo "</td></tr>";
}
?>
<?php if ($_SESSION['security'] & 128)
{
echo "<tr><td class='desc'>ORGANISATION</td><td></td>";
echo "<td><select name='org_i'>";
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (mysqli_connect_errno())
   {
    $errtext= "Failed to connect to Database: " . mysqli_connect_error();
   }
   else
   {
     echo "<option value='0'></option>";
     $qs= "SELECT * FROM organisations ORDER BY name ASC";
     $r = mysqli_query($con,$qs);
     while($row = mysqli_fetch_array($r))
     {
     if ($org_f == $row['id'])
     echo "<option value='" . $row['id'] ."' selected>" . $row['name'] ."</option>" ; 
    else
     echo "<option value='" . $row['id'] ."'>" . $row['name'] ."</option>" ; 
      }
    mysqli_close($con);
   }
echo "</select></td>";echo "<td>";
echo $org_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>EXPIRES</td><td>*</td>";
echo "<td><input type='date' name='expire_i' Value='" . substr($expire_f,0,10) . "'></td>";
echo "<td>";
echo $expire_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>SECURITY LEVEL</td><td>*</td>";
echo "<td>";
echo "<input ";
if (strlen($securitylevel_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='securitylevel_i' ";echo "size='5' ";echo "Value='";echo $securitylevel_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $securitylevel_err; echo "</td></tr>";
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
echo "<tr><td class='desc'>FORCE PW RESET</td><td></td>";
echo "<td><input type='checkbox' name='force_pw_reset_i[]' Value='1' ";if ($force_pw_reset_f ==1) echo "checked";echo "></td>";echo "<td>";
echo $force_pw_reset_err; echo "</td></tr>";
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
