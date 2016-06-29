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
<link rel="stylesheet" type="text/css" href="styleform1.css">
</head>
<body>
<script>function goBack() {window.history.back()}</script>
<?php
$DEBUG=0;
$pageid=47;
$errtext="";
$sqltext="";
$error=0;
$trantype="Create";
$recid=-1;
$id_f="";
$id_err="";
$create_time_f="";
$create_time_err="";
$data_f="";
$data_err="";
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
    $q = "SELECT * FROM diag WHERE id = " . $recid ;
    $r = mysqli_query($con,$q);
    $row = mysqli_fetch_array($r);
    $id_f = $row['id'];
    $create_time_f = $row['create_time'];
    $data_f = htmlspecialchars($row['data'],ENT_QUOTES);
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
 $create_time_err = "";
 $data_err = "";
 $create_time_f = InputChecker($_POST["create_time_i"]);
 if (empty($create_time_f) )
 {
  $create_time_err = "CREATE TIME is required";
  $error = 1;
 }
 $data_f = InputChecker($_POST["data_i"]);
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
       $Q="DELETE FROM diag WHERE id = " . $_POST['updateid'] ;}}
     if (isset($_POST["tran"]) ) {if ($_POST["tran"] == "Update"){
      $Q = "UPDATE diag SET ";
      $Q .= "create_time=";
      $Q .= "'" . $create_time_f . "'";
      $Q .= ",data=";
      $Q .= "'" . mysqli_real_escape_string($con, $data_f)  . "'";
$Q .= " WHERE ";$Q .= "id ";$Q .= "= ";
$Q .= $_POST['updateid'];}
     else
     if ($_POST["tran"] == "Create"){
       $Q = "INSERT INTO diag (";$Q .= "create_time";$Q .= ", data";$Q .= " ) VALUES (";
       $Q .= "'" . $create_time_f . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $data_f) . "'";
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
$data_f=htmlspecialchars($data_f,ENT_QUOTES);
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
echo "<tr><td class='desc'>CREATE TIME</td><td>*</td>";
echo "<td>";
echo "<input ";
if (strlen($create_time_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='create_time_i' ";echo "Value='";echo $create_time_f;echo "' ";echo " autofocus ";echo ">";echo "</td>";echo "<td>";
echo $create_time_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>DIAG DATA</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($data_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='data_i' ";echo "Value='";echo $data_f;echo "' ";echo "maxlength='2000'";echo ">";echo "</td>";echo "<td>";
echo $data_err; echo "</td></tr>";
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
