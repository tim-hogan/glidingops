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
$pageid=17;
$errtext="";
$sqltext="";
$error=0;
$trantype="Create";
$recid=-1;
$txt_id_f="";
$txt_id_err="";
$txt_unique_f="";
$txt_unique_err="";
$txt_msg_id_f="";
$txt_msg_id_err="";
$txt_member_id_f="";
$txt_member_id_err="";
$txt_to_f="";
$txt_to_err="";
$txt_status_f="";
$txt_status_err="";
$txt_timestamp_create_f=$dateStr;
$txt_timestamp_create_err="";
$txt_timestamp_sent_f=$dateStr;
$txt_timestamp_sent_err="";
$txt_timestamp_recv_f=$dateStr;
$txt_timestamp_recv_err="";
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
    $q = "SELECT * FROM texts WHERE txt_id = " . $recid ;
    $r = mysqli_query($con,$q);
    $row = mysqli_fetch_array($r);
    $txt_id_f = $row['txt_id'];
    $txt_unique_f = $row['txt_unique'];
    $txt_msg_id_f = $row['txt_msg_id'];
    $txt_member_id_f = $row['txt_member_id'];
    $txt_to_f = htmlspecialchars($row['txt_to'],ENT_QUOTES);
    $txt_status_f = $row['txt_status'];
    $txt_timestamp_create_f = $row['txt_timestamp_create'];
    $txt_timestamp_create_f = substr($txt_timestamp_create_f, 0, -9);
    $txt_timestamp_sent_f = $row['txt_timestamp_sent'];
    $txt_timestamp_sent_f = substr($txt_timestamp_sent_f, 0, -9);
    $txt_timestamp_recv_f = $row['txt_timestamp_recv'];
    $txt_timestamp_recv_f = substr($txt_timestamp_recv_f, 0, -9);
    $trantype="Update";
    mysqli_close($con);
   }
  }
 }
}
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
 $error=0;
 $txt_id_err = "";
 $txt_unique_err = "";
 $txt_msg_id_err = "";
 $txt_member_id_err = "";
 $txt_to_err = "";
 $txt_status_err = "";
 $txt_timestamp_create_err = "";
 $txt_timestamp_sent_err = "";
 $txt_timestamp_recv_err = "";
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
       $Q="DELETE FROM texts WHERE txt_id = " . $_POST['updateid'] ;}}
     if (isset($_POST["tran"]) ) {if ($_POST["tran"] == "Update"){
      $Q = "UPDATE texts SET ";
$Q .= " WHERE ";$Q .= "txt_id ";$Q .= "= ";
$Q .= $_POST['updateid'];}
     else
     if ($_POST["tran"] == "Create"){
       $Q = "INSERT INTO texts (";$Q .= " ) VALUES (";
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
}
?>
<div id='divform'>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<table>
<?php if (true)
{
echo "<tr><td class='desc'>Id</td><td></td>";
echo "<td>";
echo $txt_id_f; echo "</td>";echo "<td>";
echo $txt_id_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>Text Id</td><td></td>";
echo "<td>";
echo $txt_unique_f; echo "</td>";echo "<td>";
echo $txt_unique_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>Message</td><td>*</td>";
echo "<td><select name='txt_msg_id_i'>";
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (mysqli_connect_errno())
   {
    $errtext= "Failed to connect to Database: " . mysqli_connect_error();
   }
   else
   {
     $qs= "SELECT * FROM messages";if($_SESSION['org'] > 0) {$qs .= " WHERE messages.org = " . $_SESSION['org'] . " ";}$qs .= " ORDER BY msg ASC";
     $r = mysqli_query($con,$qs);
     while($row = mysqli_fetch_array($r))
     {
     if ($txt_msg_id_f == $row['id'])
     echo "<option value='" . $row['id'] ."' selected>" . $row['msg'] ."</option>" ; 
    else
     echo "<option value='" . $row['id'] ."'>" . $row['msg'] ."</option>" ; 
      }
    mysqli_close($con);
   }
echo "</select></td>";echo "<td>";
echo $txt_msg_id_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>To</td><td>*</td>";
echo "<td><select name='txt_member_id_i'>";
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (mysqli_connect_errno())
   {
    $errtext= "Failed to connect to Database: " . mysqli_connect_error();
   }
   else
   {
     $qs= "SELECT * FROM members";if($_SESSION['org'] > 0) {$qs .= " WHERE members.org = " . $_SESSION['org'] . " ";}$qs .= " ORDER BY displayname ASC";
     $r = mysqli_query($con,$qs);
     while($row = mysqli_fetch_array($r))
     {
     if ($txt_member_id_f == $row['id'])
     echo "<option value='" . $row['id'] ."' selected>" . $row['displayname'] ."</option>" ; 
    else
     echo "<option value='" . $row['id'] ."'>" . $row['displayname'] ."</option>" ; 
      }
    mysqli_close($con);
   }
echo "</select></td>";echo "<td>";
echo $txt_member_id_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>Number To</td><td></td>";
echo "<td>";
echo $txt_to_f; echo "</td>";echo "<td>";
echo $txt_to_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>Status</td><td>*</td>";
echo "<td>";
echo $txt_status_f; echo "</td>";echo "<td>";
echo $txt_status_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>Creation Time</td><td>*</td>";
echo "<td><input type='date' name='txt_timestamp_create_i' Value='" . substr($txt_timestamp_create_f,0,10) . "'></td>";
echo "<td>";
echo $txt_timestamp_create_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>Sent Time</td><td></td>";
echo "<td><input type='date' name='txt_timestamp_sent_i' Value='" . substr($txt_timestamp_sent_f,0,10) . "'></td>";
echo "<td>";
echo $txt_timestamp_sent_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>Received timr</td><td></td>";
echo "<td><input type='date' name='txt_timestamp_recv_i' Value='" . substr($txt_timestamp_recv_f,0,10) . "'></td>";
echo "<td>";
echo $txt_timestamp_recv_err; echo "</td></tr>";
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
