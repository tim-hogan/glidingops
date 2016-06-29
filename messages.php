<?php session_start(); ?>
<?php
$org=0;
if(isset($_SESSION['org'])) $org=$_SESSION['org'];
if(isset($_SESSION['security'])){
 if (!($_SESSION['security'] & 4)){die("Secruity level too low for this page");}
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
$pageid=15;
$errtext="";
$sqltext="";
$error=0;
$trantype="Create";
$recid=-1;
$id_f="";
$id_err="";
$org_f="";
$org_err="";
$msg_f="";
$msg_err="";
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
    $q = "SELECT * FROM messages WHERE id = " . $recid ;
    $r = mysqli_query($con,$q);
    $row = mysqli_fetch_array($r);
    if ($_SESSION['org'] > 0 && $row['org'] != $_SESSION['org'])
       die("Record does not exist");
    $id_f = $row['id'];
    $org_f = $row['org'];
    $msg_f = htmlspecialchars($row['msg'],ENT_QUOTES);
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
 $msg_err = "";
if ($_SESSION['security'] & 128) { $org_f = InputChecker($_POST["org_i"]);
}
 $msg_f = InputChecker($_POST["msg_i"]);
 if (empty($msg_f) )
 {
  $msg_err = "MESSAGE TEXT is required";
  $error = 1;
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
       $Q="DELETE FROM messages WHERE id = " . $_POST['updateid'] ;}}
     if (isset($_POST["tran"]) ) {if ($_POST["tran"] == "Update"){
      $Q = "UPDATE messages SET ";
if ($_SESSION['security'] & 128) {      $Q .= "org=";
       if ($org_f ==0) $Q .= "null"; else {$Q .= "'";$Q .= "$org_f"; $Q .= "'"; }
}
      $Q .= ",msg=";
      $Q .= "'" . mysqli_real_escape_string($con, $msg_f)  . "'";
$Q .= " WHERE ";$Q .= "id ";$Q .= "= ";
$Q .= $_POST['updateid'];}
     else
     if ($_POST["tran"] == "Create"){
       $Q = "INSERT INTO messages (";$Q .= "org";$Q .= ", msg";$Q .= " ) VALUES (";
if ($_SESSION['security'] & 128) {       if ($org_f ==0)$Q .= "null"; else{$Q .= "'";$Q .= $org_f;$Q .= "'";}
}else{$Q.=$_SESSION['org'];}
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $msg_f) . "'";
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
$msg_f=htmlspecialchars($msg_f,ENT_QUOTES);
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
echo "<tr><td class='desc'>MESSAGE TEXT</td><td>*</td>";
echo "<td>";
echo "<input ";
if (strlen($msg_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='msg_i' ";echo "Value='";echo $msg_f;echo "' ";echo "maxlength='160'";echo " autofocus ";echo ">";echo "</td>";echo "<td>";
echo $msg_err; echo "</td></tr>";
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
