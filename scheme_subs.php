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
$pageid=13;
$errtext="";
$sqltext="";
$error=0;
$trantype="Create";
$recid=-1;
$id_f="";
$id_err="";
$member_f="";
$member_err="";
$start_f=$dateStr;
$start_err="";
$end_f=$dateStr;
$end_err="";
$scheme_f="";
$scheme_err="";
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
    $q = "SELECT * FROM scheme_subs WHERE id = " . $recid ;
    $r = mysqli_query($con,$q);
    $row = mysqli_fetch_array($r);
    if ($_SESSION['org'] > 0 && $row['org'] != $_SESSION['org'])
       die("Record does not exist");
    $id_f = $row['id'];
    $member_f = $row['member'];
    $start_f = $row['start'];
    $start_f = substr($start_f, 0, -9);
    $end_f = $row['end'];
    $end_f = substr($end_f, 0, -9);
    $scheme_f = $row['scheme'];
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
 $member_err = "";
 $start_err = "";
 $end_err = "";
 $scheme_err = "";
 $member_f = InputChecker($_POST["member_i"]);
 $start_f = InputChecker($_POST["start_i"]);
 $end_f = InputChecker($_POST["end_i"]);
 $scheme_f = InputChecker($_POST["scheme_i"]);
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
       $Q="DELETE FROM scheme_subs WHERE id = " . $_POST['updateid'] ;}}
     if (isset($_POST["tran"]) ) {if ($_POST["tran"] == "Update"){
      $Q = "UPDATE scheme_subs SET ";
      $Q .= "member=";
       if ($member_f ==0) $Q .= "null"; else {$Q .= "'";$Q .= "$member_f"; $Q .= "'"; }
      $Q .= ",start=";
      $Q .= "'" . $start_f . "'";
      $Q .= ",end=";
      $Q .= "'" . $end_f . "'";
      $Q .= ",scheme=";
       if ($scheme_f ==0) $Q .= "null"; else {$Q .= "'";$Q .= "$scheme_f"; $Q .= "'"; }
$Q .= " WHERE ";$Q .= "id ";$Q .= "= ";
$Q .= $_POST['updateid'];}
     else
     if ($_POST["tran"] == "Create"){
       $Q = "INSERT INTO scheme_subs (";$Q .= "org";$Q .= ", member";$Q .= ", start";$Q .= ", end";$Q .= ", scheme";$Q .= " ) VALUES (";
$Q.=$_SESSION['org'];       $Q.= ",";
       if ($member_f ==0)$Q .= "null"; else{$Q .= "'";$Q .= $member_f;$Q .= "'";}
       $Q.= ",";
       $Q .= "'" . $start_f . "'";
       $Q.= ",";
       $Q .= "'" . $end_f . "'";
       $Q.= ",";
       if ($scheme_f ==0)$Q .= "null"; else{$Q .= "'";$Q .= $scheme_f;$Q .= "'";}
      $Q.= ")";
    }}
    $sqltext = $Q;
    if(!mysqli_query($con,$Q) )
    {
       $errtext = "Database entry: " . mysqli_error($con) . "<br>" . $Q;
    }
    mysqli_close($con);
    if ($_POST["tran"] == "Delete")
     header('Location: SubsToSchemes');
    if ($_POST["tran"] == "Update")
     header('Location: SubsToSchemes');
  }
 }
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
echo "<tr><td class='desc'>Member</td><td></td>";
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
echo "<tr><td class='desc'>Start Date</td><td></td>";
echo "<td><input type='date' name='start_i' Value='" . substr($start_f,0,10) . "'></td>";
echo "<td>";
echo $start_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>End Date</td><td></td>";
echo "<td><input type='date' name='end_i' Value='" . substr($end_f,0,10) . "'></td>";
echo "<td>";
echo $end_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>Incentive Scheme</td><td></td>";
echo "<td><select name='scheme_i'>";
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (mysqli_connect_errno())
   {
    $errtext= "Failed to connect to Database: " . mysqli_connect_error();
   }
   else
   {
     echo "<option value='0'></option>";
     $qs= "SELECT * FROM incentive_schemes";if($_SESSION['org'] > 0) {$qs .= " WHERE incentive_schemes.org = " . $_SESSION['org'] . " ";}$qs .= " ORDER BY name ASC";
     $r = mysqli_query($con,$qs);
     while($row = mysqli_fetch_array($r))
     {
     if ($scheme_f == $row['id'])
     echo "<option value='" . $row['id'] ."' selected>" . $row['name'] ."</option>" ; 
    else
     echo "<option value='" . $row['id'] ."'>" . $row['name'] ."</option>" ; 
      }
    mysqli_close($con);
   }
echo "</select></td>";echo "<td>";
echo $scheme_err; echo "</td></tr>";
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
