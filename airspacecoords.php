<?php session_start(); ?>
<?php
$org=0;
if(isset($_SESSION['org'])) $org=$_SESSION['org'];
if(isset($_SESSION['security'])){
 if (!($_SESSION['security'] & 72)){die("Secruity level too low for this page");}
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
$pageid=71;
$errtext="";
$sqltext="";
$error=0;
$trantype="Create";
$recid=-1;
$id_f="";
$id_err="";
$airspace_f="";
$airspace_err="";
$seq_f="";
$seq_err="";
$type_f="";
$type_err="";
$lattitude_f="";
$lattitude_err="";
$longitude_f="";
$longitude_err="";
$arclat_f="";
$arclat_err="";
$arclon_f="";
$arclon_err="";
$arcdist_f="";
$arcdist_err="";
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
    $q = "SELECT * FROM airspacecoords WHERE id = " . $recid ;
    $r = mysqli_query($con,$q);
    $row = mysqli_fetch_array($r);
    $id_f = $row['id'];
    $airspace_f = $row['airspace'];
    $seq_f = $row['seq'];
    $type_f = htmlspecialchars($row['type'],ENT_QUOTES);
    $lattitude_f = $row['lattitude'];
    $longitude_f = $row['longitude'];
    $arclat_f = $row['arclat'];
    $arclon_f = $row['arclon'];
    $arcdist_f = $row['arcdist'];
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
 $airspace_err = "";
 $seq_err = "";
 $type_err = "";
 $lattitude_err = "";
 $longitude_err = "";
 $arclat_err = "";
 $arclon_err = "";
 $arcdist_err = "";
 $airspace_f = InputChecker($_POST["airspace_i"]);
 $seq_f = InputChecker($_POST["seq_i"]);
 if (!empty($seq_f ) ) {if (!is_numeric($seq_f ) ) {$seq_err = "SEQUENCE is not numeric";$error = 1;}}
 $type_f = InputChecker($_POST["type_i"]);
 $lattitude_f = InputChecker($_POST["lattitude_i"]);
 $longitude_f = InputChecker($_POST["longitude_i"]);
 $arclat_f = InputChecker($_POST["arclat_i"]);
 $arclon_f = InputChecker($_POST["arclon_i"]);
 $arcdist_f = InputChecker($_POST["arcdist_i"]);
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
       $Q="DELETE FROM airspacecoords WHERE id = " . $_POST['updateid'] ;}}
     if (isset($_POST["tran"]) ) {if ($_POST["tran"] == "Update"){
      $Q = "UPDATE airspacecoords SET ";
      $Q .= "airspace=";
       if ($airspace_f ==0) $Q .= "null"; else {$Q .= "'";$Q .= "$airspace_f"; $Q .= "'"; }
      $Q .= ",seq=";
      $Q .= "'" . $seq_f . "'";
      $Q .= ",type=";
      $Q .= "'" . mysqli_real_escape_string($con, $type_f)  . "'";
      $Q .= ",lattitude=";
      $Q .= "'" . $lattitude_f . "'";
      $Q .= ",longitude=";
      $Q .= "'" . $longitude_f . "'";
      $Q .= ",arclat=";
      $Q .= "'" . $arclat_f . "'";
      $Q .= ",arclon=";
      $Q .= "'" . $arclon_f . "'";
      $Q .= ",arcdist=";
      $Q .= "'" . $arcdist_f . "'";
$Q .= " WHERE ";$Q .= "id ";$Q .= "= ";
$Q .= $_POST['updateid'];}
     else
     if ($_POST["tran"] == "Create"){
       $Q = "INSERT INTO airspacecoords (";$Q .= "airspace";$Q .= ", seq";$Q .= ", type";$Q .= ", lattitude";$Q .= ", longitude";$Q .= ", arclat";$Q .= ", arclon";$Q .= ", arcdist";$Q .= " ) VALUES (";
       if ($airspace_f ==0)$Q .= "null"; else{$Q .= "'";$Q .= $airspace_f;$Q .= "'";}
       $Q.= ",";
       $Q .= "'" . $seq_f . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $type_f) . "'";
       $Q.= ",";
       $Q .= "'" . $lattitude_f . "'";
       $Q.= ",";
       $Q .= "'" . $longitude_f . "'";
       $Q.= ",";
       $Q .= "'" . $arclat_f . "'";
       $Q.= ",";
       $Q .= "'" . $arclon_f . "'";
       $Q.= ",";
       $Q .= "'" . $arcdist_f . "'";
      $Q.= ")";
    }}
    $sqltext = $Q;
    if(!mysqli_query($con,$Q) )
    {
       $errtext = "Database entry: " . mysqli_error($con) . "<br>" . $Q;
    }
    mysqli_close($con);
    if ($_POST["tran"] == "Delete")
     header('Location: airspacecoords-list.php');
    if ($_POST["tran"] == "Update")
     header('Location: airspacecoords-list.php');
  }
 }
$type_f=htmlspecialchars($type_f,ENT_QUOTES);
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
echo "<tr><td class='desc'>AIRSPACE NAME</td><td></td>";
echo "<td><select name='airspace_i'>";
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (mysqli_connect_errno())
   {
    $errtext= "Failed to connect to Database: " . mysqli_connect_error();
   }
   else
   {
     echo "<option value='0'></option>";
     $qs= "SELECT * FROM airspace ORDER BY name ASC";
     $r = mysqli_query($con,$qs);
     while($row = mysqli_fetch_array($r))
     {
     if ($airspace_f == $row['id'])
     echo "<option value='" . $row['id'] ."' selected>" . $row['name'] ."</option>" ; 
    else
     echo "<option value='" . $row['id'] ."'>" . $row['name'] ."</option>" ; 
      }
    mysqli_close($con);
   }
echo "</select></td>";echo "<td>";
echo $airspace_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>SEQUENCE</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($seq_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='seq_i' ";echo "Value='";echo $seq_f;echo "' ";echo " autofocus ";echo ">";echo "</td>";echo "<td>";
echo $seq_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>TYPE</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($type_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='type_i' ";echo "Value='";echo $type_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $type_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>LATTITUDE</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($lattitude_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='lattitude_i' ";echo "Value='";echo $lattitude_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $lattitude_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>LONGITUDE</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($longitude_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='longitude_i' ";echo "Value='";echo $longitude_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $longitude_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>ARC LATTITUDE</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($arclat_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='arclat_i' ";echo "Value='";echo $arclat_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $arclat_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>ARC LONGITUDE</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($arclon_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='arclon_i' ";echo "Value='";echo $arclon_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $arclon_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>ARC DISTANCE</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($arcdist_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='arcdist_i' ";echo "Value='";echo $arcdist_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $arcdist_err; echo "</td></tr>";
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
