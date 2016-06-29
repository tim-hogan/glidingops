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
$pageid=51;
$errtext="";
$sqltext="";
$error=0;
$trantype="Create";
$recid=-1;
$id_f="";
$id_err="";
$eventtime_f=$dateStr;
$eventtime_err="";
$userid_f="";
$userid_err="";
$memberid_f="";
$memberid_err="";
$description_f="";
$description_err="";
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
    $q = "SELECT * FROM audit WHERE id = " . $recid ;
    $r = mysqli_query($con,$q);
    $row = mysqli_fetch_array($r);
    $id_f = $row['id'];
    $eventtime_f = $row['eventtime'];
    $userid_f = $row['userid'];
    $memberid_f = $row['memberid'];
    $description_f = htmlspecialchars($row['description'],ENT_QUOTES);
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
 $eventtime_err = "";
 $userid_err = "";
 $memberid_err = "";
 $description_err = "";
 $eventtime_f = InputChecker($_POST["eventtime_i"]);
 if (empty($eventtime_f) )
 {
  $eventtime_err = "EVENT TIME LOCAL is required";
  $error = 1;
 }
 $userid_f = InputChecker($_POST["userid_i"]);
 if (empty($userid_f) )
 {
  $userid_err = "USERCODE is required";
  $error = 1;
 }
 $memberid_f = InputChecker($_POST["memberid_i"]);
 $description_f = InputChecker($_POST["description_i"]);
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
       $Q="DELETE FROM audit WHERE id = " . $_POST['updateid'] ;}}
     if (isset($_POST["tran"]) ) {if ($_POST["tran"] == "Update"){
      $Q = "UPDATE audit SET ";
      $Q .= "eventtime=";
      $Q .= "'" . $eventtime_f . "'";
      $Q .= ",userid=";
      $Q .= "'" . $userid_f . "'";
      $Q .= ",memberid=";
       if ($memberid_f ==0) $Q .= "null"; else {$Q .= "'";$Q .= "$memberid_f"; $Q .= "'"; }
      $Q .= ",description=";
      $Q .= "'" . mysqli_real_escape_string($con, $description_f)  . "'";
$Q .= " WHERE ";$Q .= "id ";$Q .= "= ";
$Q .= $_POST['updateid'];}
     else
     if ($_POST["tran"] == "Create"){
       $Q = "INSERT INTO audit (";$Q .= "eventtime";$Q .= ", userid";$Q .= ", memberid";$Q .= ", description";$Q .= " ) VALUES (";
       $Q .= "'" . $eventtime_f . "'";
       $Q.= ",";
       $Q .= "'" . $userid_f . "'";
       $Q.= ",";
       if ($memberid_f ==0)$Q .= "null"; else{$Q .= "'";$Q .= $memberid_f;$Q .= "'";}
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $description_f) . "'";
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
echo "<tr><td class='desc'>EVENT TIME LOCAL</td><td>*</td>";
echo "<td><input type='date' name='eventtime_i' Value='" . substr($eventtime_f,0,10) . "'></td>";
echo "<td>";
echo $eventtime_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>USERCODE</td><td>*</td>";
echo "<td><select name='userid_i'>";
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (mysqli_connect_errno())
   {
    $errtext= "Failed to connect to Database: " . mysqli_connect_error();
   }
   else
   {
     $qs= "SELECT * FROM users";if($_SESSION['org'] > 0) {$qs .= " WHERE users.org = " . $_SESSION['org'] . " ";}$qs .= " ORDER BY usercode ASC";
     $r = mysqli_query($con,$qs);
     while($row = mysqli_fetch_array($r))
     {
     if ($userid_f == $row['id'])
     echo "<option value='" . $row['id'] ."' selected>" . $row['usercode'] ."</option>" ; 
    else
     echo "<option value='" . $row['id'] ."'>" . $row['usercode'] ."</option>" ; 
      }
    mysqli_close($con);
   }
echo "</select></td>";echo "<td>";
echo $userid_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>MEMBER</td><td></td>";
echo "<td><select name='memberid_i'>";
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
     if ($memberid_f == $row['id'])
     echo "<option value='" . $row['id'] ."' selected>" . $row['displayname'] ."</option>" ; 
    else
     echo "<option value='" . $row['id'] ."'>" . $row['displayname'] ."</option>" ; 
      }
    mysqli_close($con);
   }
echo "</select></td>";echo "<td>";
echo $memberid_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>DESCRIPTION</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($description_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='description_i' ";echo "size='100' ";echo "Value='";echo $description_f;echo "' ";echo "maxlength='100'";echo " autofocus ";echo ">";echo "</td>";echo "<td>";
echo $description_err; echo "</td></tr>";
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
