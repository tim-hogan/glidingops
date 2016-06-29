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
$dateTimeZone = new DateTimeZone($_SESSION['timezone']);
$dateTime = new DateTime('now', $dateTimeZone);
$dateStr = $dateTime->format('Y-m-d');
$timeoffset = $dateTime->getOffset();
$pageid=43;
$errtext="";
$sqltext="";
$error=0;
$trantype="Create";
$recid=-1;
$id_f="";
$id_err="";
$plane_f="";
$plane_err="";
$type_f="";
$type_err="";
$height_f="";
$height_err="";
$club_glider_f="";
$club_glider_err="";
$member_class_f="";
$member_class_err="";
$effective_from_f=$dateStr;
$effective_from_err="";
$cost_f="";
$cost_err="";
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
    $q = "SELECT * FROM towcharges WHERE id = " . $recid ;
    $r = mysqli_query($con,$q);
    $row = mysqli_fetch_array($r);
    if ($_SESSION['org'] > 0 && $row['org'] != $_SESSION['org'])
       die("Record does not exist");
    $id_f = $row['id'];
    $plane_f = $row['plane'];
    $type_f = $row['type'];
    $height_f = $row['height'];
    $club_glider_f = $row['club_glider'];
    $member_class_f = $row['member_class'];
    $effective_from_f = $row['effective_from'];
    $effective_from_f = substr($effective_from_f, 0, -9);
    $cost_f = $row['cost'];
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
 $plane_err = "";
 $type_err = "";
 $height_err = "";
 $club_glider_err = "";
 $member_class_err = "";
 $effective_from_err = "";
 $cost_err = "";
 $plane_f = InputChecker($_POST["plane_i"]);
 $type_f = InputChecker($_POST["type_i"]);
 if (!empty($type_f ) ) {if (!is_numeric($type_f ) ) {$type_err = "TYPE (0 = HEIGHT, 1 = PER MINUTE) is not numeric";$error = 1;}}
 $height_f = InputChecker($_POST["height_i"]);
 if (!empty($height_f ) ) {if (!is_numeric($height_f ) ) {$height_err = "HEIGHT is not numeric";$error = 1;}}
if(in_array("1",$_POST['club_glider_i']))
 $club_glider_f = 1;
else
 $club_glider_f = 0;
 if (!empty($club_glider_f ) ) {if (!is_numeric($club_glider_f ) ) {$club_glider_err = "CLUB GLIDER is not numeric";$error = 1;}}
 $member_class_f = InputChecker($_POST["member_class_i"]);
 $effective_from_f = InputChecker($_POST["effective_from_i"]);
 if (empty($effective_from_f) )
 {
  $effective_from_err = "EFFECTIVE FROM is required";
  $error = 1;
 }
 $cost_f = InputChecker($_POST["cost_i"]);
 if (empty($cost_f) )
 {
  $cost_err = "COST is required";
  $error = 1;
 }
 if (!empty($cost_f ) ) {if (!is_numeric($cost_f ) ) {$cost_err = "COST is not numeric";$error = 1;}}
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
       $Q="DELETE FROM towcharges WHERE id = " . $_POST['updateid'] ;}}
     if (isset($_POST["tran"]) ) {if ($_POST["tran"] == "Update"){
      $Q = "UPDATE towcharges SET ";
      $Q .= "plane=";
       if ($plane_f ==0) $Q .= "null"; else {$Q .= "'";$Q .= "$plane_f"; $Q .= "'"; }
      $Q .= ",type=";
      $Q .= "'" . $type_f . "'";
      $Q .= ",height=";
      $Q .= "'" . $height_f . "'";
      $Q .= ",club_glider=";
      $Q .= "'" . $club_glider_f . "'";
      $Q .= ",member_class=";
       if ($member_class_f ==0) $Q .= "null"; else {$Q .= "'";$Q .= "$member_class_f"; $Q .= "'"; }
      $Q .= ",effective_from=";
      $Q .= "'" . $effective_from_f . "'";
      $Q .= ",cost=";
      $Q .= "'" . $cost_f . "'";
$Q .= " WHERE ";$Q .= "id ";$Q .= "= ";
$Q .= $_POST['updateid'];}
     else
     if ($_POST["tran"] == "Create"){
       $Q = "INSERT INTO towcharges (";$Q .= "org";$Q .= ", plane";$Q .= ", type";$Q .= ", height";$Q .= ", club_glider";$Q .= ", member_class";$Q .= ", effective_from";$Q .= ", cost";$Q .= " ) VALUES (";
$Q.=$_SESSION['org'];       $Q.= ",";
       if ($plane_f ==0)$Q .= "null"; else{$Q .= "'";$Q .= $plane_f;$Q .= "'";}
       $Q.= ",";
       $Q .= "'" . $type_f . "'";
       $Q.= ",";
       $Q .= "'" . $height_f . "'";
       $Q.= ",";
       $Q .= "'" . $club_glider_f . "'";
       $Q.= ",";
       if ($member_class_f ==0)$Q .= "null"; else{$Q .= "'";$Q .= $member_class_f;$Q .= "'";}
       $Q.= ",";
       $Q .= "'" . $effective_from_f . "'";
       $Q.= ",";
       $Q .= "'" . $cost_f . "'";
      $Q.= ")";
    }}
    $sqltext = $Q;
    if(!mysqli_query($con,$Q) )
    {
       $errtext = "Database entry: " . mysqli_error($con) . "<br>" . $Q;
    }
    mysqli_close($con);
    if ($_POST["tran"] == "Delete")
     header('Location: TowCharges');
    if ($_POST["tran"] == "Update")
     header('Location: TowCharges');
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
echo "<tr><td class='desc'>TOW PLANE</td><td></td>";
echo "<td><select name='plane_i'>";
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (mysqli_connect_errno())
   {
    $errtext= "Failed to connect to Database: " . mysqli_connect_error();
   }
   else
   {
     echo "<option value='0'></option>";
     $qs= "SELECT * FROM aircraft";if($_SESSION['org'] > 0) {$qs .= " WHERE aircraft.org = " . $_SESSION['org'] . " ";}$qs .= " ORDER BY rego_short ASC";
     $r = mysqli_query($con,$qs);
     while($row = mysqli_fetch_array($r))
     {
     if ($plane_f == $row['id'])
     echo "<option value='" . $row['id'] ."' selected>" . $row['rego_short'] ."</option>" ; 
    else
     echo "<option value='" . $row['id'] ."'>" . $row['rego_short'] ."</option>" ; 
      }
    mysqli_close($con);
   }
echo "</select></td>";echo "<td>";
echo $plane_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>TYPE (0 = HEIGHT, 1 = PER MINUTE)</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($type_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='type_i' ";echo "Value='";echo $type_f;echo "' ";echo " autofocus ";echo ">";echo "</td>";echo "<td>";
echo $type_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>HEIGHT</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($height_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='height_i' ";echo "Value='";echo $height_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $height_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>CLUB GLIDER</td><td></td>";
echo "<td><input type='checkbox' name='club_glider_i[]' Value='1' ";if ($club_glider_f ==1) echo "checked";echo "></td>";echo "<td>";
echo $club_glider_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>CLASS</td><td></td>";
echo "<td><select name='member_class_i'>";
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
     if ($member_class_f == $row['id'])
     echo "<option value='" . $row['id'] ."' selected>" . $row['class'] ."</option>" ; 
    else
     echo "<option value='" . $row['id'] ."'>" . $row['class'] ."</option>" ; 
      }
    mysqli_close($con);
   }
echo "</select></td>";echo "<td>";
echo $member_class_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>EFFECTIVE FROM</td><td>*</td>";
echo "<td><input type='date' name='effective_from_i' Value='" . substr($effective_from_f,0,10) . "'></td>";
echo "<td>";
echo $effective_from_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>COST</td><td>*</td>";
echo "<td>";
echo "<input ";
if (strlen($cost_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='cost_i' ";echo "Value='";echo $cost_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $cost_err; echo "</td></tr>";
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
