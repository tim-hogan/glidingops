<?php session_start(); ?>
<?php
$org=0;
if(isset($_SESSION['org'])) $org=$_SESSION['org'];
if(isset($_SESSION['security'])){
 if (!($_SESSION['security'] & 1)){die("Secruity level too low for this page");}
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
$pageid=55;
$errtext="";
$sqltext="";
$error=0;
$trantype="Create";
$recid=-1;
$id_f="";
$id_err="";
$rego_short_f="";
$rego_short_err="";
$spotkey_f="";
$spotkey_err="";
$polltimelast_f="";
$polltimelast_err="";
$polltimeall_f="";
$polltimeall_err="";
$lastreq_f="";
$lastreq_err="";
$lastlistreq_f="";
$lastlistreq_err="";
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
    $q = "SELECT * FROM spots WHERE id = " . $recid ;
    $r = mysqli_query($con,$q);
    $row = mysqli_fetch_array($r);
    if ($_SESSION['org'] > 0 && $row['org'] != $_SESSION['org'])
       die("Record does not exist");
    $id_f = $row['id'];
    $rego_short_f = htmlspecialchars($row['rego_short'],ENT_QUOTES);
    $spotkey_f = htmlspecialchars($row['spotkey'],ENT_QUOTES);
    $polltimelast_f = $row['polltimelast'];
    $polltimeall_f = $row['polltimeall'];
    $lastreq_f = $row['lastreq'];
    $lastlistreq_f = $row['lastlistreq'];
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
 $rego_short_err = "";
 $spotkey_err = "";
 $polltimelast_err = "";
 $polltimeall_err = "";
 $lastreq_err = "";
 $lastlistreq_err = "";
 $rego_short_f = InputChecker($_POST["rego_short_i"]);
 $spotkey_f = InputChecker($_POST["spotkey_i"]);
 $polltimelast_f = InputChecker($_POST["polltimelast_i"]);
 if (!empty($polltimelast_f ) ) {if (!is_numeric($polltimelast_f ) ) {$polltimelast_err = "POLL TIME FOR LAST (seconds) is not numeric";$error = 1;}}
 $polltimeall_f = InputChecker($_POST["polltimeall_i"]);
 if (!empty($polltimeall_f ) ) {if (!is_numeric($polltimeall_f ) ) {$polltimeall_err = "POLL TIME FOR ALL (seconds) is not numeric";$error = 1;}}
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
       $Q="DELETE FROM spots WHERE id = " . $_POST['updateid'] ;}}
     if (isset($_POST["tran"]) ) {if ($_POST["tran"] == "Update"){
      $Q = "UPDATE spots SET ";
      $Q .= "rego_short=";
      $Q .= "'" . mysqli_real_escape_string($con, $rego_short_f)  . "'";
      $Q .= ",spotkey=";
      $Q .= "'" . mysqli_real_escape_string($con, $spotkey_f)  . "'";
      $Q .= ",polltimelast=";
      $Q .= "'" . $polltimelast_f . "'";
      $Q .= ",polltimeall=";
      $Q .= "'" . $polltimeall_f . "'";
$Q .= " WHERE ";$Q .= "id ";$Q .= "= ";
$Q .= $_POST['updateid'];}
     else
     if ($_POST["tran"] == "Create"){
       $Q = "INSERT INTO spots (";$Q .= "org";$Q .= ", rego_short";$Q .= ", spotkey";$Q .= ", polltimelast";$Q .= ", polltimeall";$Q .= " ) VALUES (";
$Q.=$_SESSION['org'];       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $rego_short_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $spotkey_f) . "'";
       $Q.= ",";
       $Q .= "'" . $polltimelast_f . "'";
       $Q.= ",";
       $Q .= "'" . $polltimeall_f . "'";
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
$rego_short_f=htmlspecialchars($rego_short_f,ENT_QUOTES);
$spotkey_f=htmlspecialchars($spotkey_f,ENT_QUOTES);
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
echo "<tr><td class='desc'>REGO</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($rego_short_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='rego_short_i' ";echo "size='10' ";echo "Value='";echo $rego_short_f;echo "' ";echo "maxlength='3'";echo " autofocus ";echo ">";echo "</td>";echo "<td>";
echo $rego_short_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>KEY</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($spotkey_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='spotkey_i' ";echo "size='40' ";echo "Value='";echo $spotkey_f;echo "' ";echo "maxlength='40'";echo ">";echo "</td>";echo "<td>";
echo $spotkey_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>POLL TIME FOR LAST (seconds)</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($polltimelast_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='polltimelast_i' ";echo "size='8' ";echo "Value='";echo $polltimelast_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $polltimelast_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>POLL TIME FOR ALL (seconds)</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($polltimeall_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='polltimeall_i' ";echo "size='8' ";echo "Value='";echo $polltimeall_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $polltimeall_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>LAST REQ</td><td></td>";
echo "<td><input type='datetime' name='lastreq_i' Value='" . $lastreq_f . "'></td>";
echo "<td>";
echo $lastreq_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>LAST FULL LIST REQ</td><td></td>";
echo "<td><input type='datetime' name='lastlistreq_i' Value='" . $lastlistreq_f . "'></td>";
echo "<td>";
echo $lastlistreq_err; echo "</td></tr>";
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
