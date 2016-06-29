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
$pageid=43;
$errtext="";
$sqltext="";
$error=0;
$trantype="Create";
$recid=-1;
$id_f="";
$id_err="";
$Char10_f="";
$Char10_err="";
$IReq_f="";
$IReq_err="";
$IntNormal_f="";
$IntNormal_err="";
$IntCheckbox_f="";
$IntCheckbox_err="";
$DecimalVal_f="";
$DecimalVal_err="";
$Email_f="";
$Email_err="";
$Date1_f=$dateStr;
$Date1_err="";
$DateTimeSpecial2_f=$dateStr;
$DateTimeSpecial2_err="";
$DateTimeSpecial2_fa="";
$DateTimeSpecial2_fb="";
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
    $q = "SELECT * FROM testy WHERE id = " . $recid ;
    $r = mysqli_query($con,$q);
    $row = mysqli_fetch_array($r);
    $id_f = $row['id'];
    $Char10_f = htmlspecialchars($row['Char10'],ENT_QUOTES);
    $IReq_f = $row['IReq'];
    $IntNormal_f = $row['IntNormal'];
    $IntCheckbox_f = $row['IntCheckbox'];
    $DecimalVal_f = $row['DecimalVal'];
    $Email_f = htmlspecialchars($row['Email'],ENT_QUOTES);
    $Date1_f = $row['Date1'];
    $Date1_f = substr($Date1_f, 0, -9);
    $DateTimeSpecial2_fdate1=new DateTime($row['DateTimeSpecial2']);
    $DateTimeSpecial2_fdate2=new DateTime();
    $DateTimeSpecial2_fdate2->setTimestamp($DateTimeSpecial2_fdate1->getTimestamp()  +  $timeoffset);
    $DateTimeSpecial2_f=$DateTimeSpecial2_fdate2->format('Y-m-d');
    $DateTimeSpecial2_fa=$DateTimeSpecial2_fdate2->format('H');    $DateTimeSpecial2_fb=$DateTimeSpecial2_fdate2->format('i');    $trantype="Update";
    mysqli_close($con);
   }
  }
 }
}
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
 $error=0;
 $id_err = "";
 $Char10_err = "";
 $IReq_err = "";
 $IntNormal_err = "";
 $IntCheckbox_err = "";
 $DecimalVal_err = "";
 $Email_err = "";
 $Date1_err = "";
 $DateTimeSpecial2_err = "";
 $Char10_f = InputChecker($_POST["Char10_i"]);
if ($_SESSION['security'] & 16) { $IReq_f = InputChecker($_POST["IReq_i"]);
 if (empty($IReq_f) )
 {
  $IReq_err = "INT REQUIRED is required";
  $error = 1;
 }
 if (!empty($IReq_f ) ) {if (!is_numeric($IReq_f ) ) {$IReq_err = "INT REQUIRED is not numeric";$error = 1;}}
}
 $IntNormal_f = InputChecker($_POST["IntNormal_i"]);
 if (!empty($IntNormal_f ) ) {if (!is_numeric($IntNormal_f ) ) {$IntNormal_err = "INT is not numeric";$error = 1;}}
if(in_array("1",$_POST['IntCheckbox_i']))
 $IntCheckbox_f = 1;
else
 $IntCheckbox_f = 0;
 if (!empty($IntCheckbox_f ) ) {if (!is_numeric($IntCheckbox_f ) ) {$IntCheckbox_err = "INT CHECKBOX is not numeric";$error = 1;}}
 $DecimalVal_f = InputChecker($_POST["DecimalVal_i"]);
 if (!empty($DecimalVal_f ) ) {if (!is_numeric($DecimalVal_f ) ) {$DecimalVal_err = "DECIMAL is not numeric";$error = 1;}}
 $Email_f = InputChecker($_POST["Email_i"]);
 $Date1_f = InputChecker($_POST["Date1_i"]);
 if (empty($Date1_f) )
 {
  $Date1_err = "DATE is required";
  $error = 1;
 }
if ($_SESSION['security'] & 16) { $DateTimeSpecial2_f = $_POST["DateTimeSpecial2_i"];
 $DateTimeSpecial2_fa = $_POST["DateTimeSpecial2_ia"];
 $DateTimeSpecial2_fb = $_POST["DateTimeSpecial2_ib"];
 $DateTimeSpecial2_fsum =  $DateTimeSpecial2_f . " " . $DateTimeSpecial2_fa . ":" . $DateTimeSpecial2_fb . ":00";
 $DateTimeSpecial2_fdate1=new DateTime($DateTimeSpecial2_fsum,$dateTimeZoneNZ);
 $DateTimeSpecial2_fdate2=new DateTime("@" . (string)$DateTimeSpecial2_fdate1->getTimestamp());
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
       $Q="DELETE FROM testy WHERE id = " . $_POST['updateid'] ;}}
     if (isset($_POST["tran"]) ) {if ($_POST["tran"] == "Update"){
      $Q = "UPDATE testy SET ";
      $Q .= "Char10=";
      $Q .= "'" . mysqli_real_escape_string($con, $Char10_f)  . "'";
if ($_SESSION['security'] & 16) {      $Q .= ",IReq=";
      $Q .= "'" . $IReq_f . "'";
}
      $Q .= ",IntNormal=";
      $Q .= "'" . $IntNormal_f . "'";
      $Q .= ",IntCheckbox=";
      $Q .= "'" . $IntCheckbox_f . "'";
      $Q .= ",DecimalVal=";
      $Q .= "'" . $DecimalVal_f . "'";
      $Q .= ",Email=";
      $Q .= "'" . mysqli_real_escape_string($con, $Email_f)  . "'";
      $Q .= ",Date1=";
      $Q .= "'" . $Date1_f . "'";
if ($_SESSION['security'] & 16) {      $Q .= ",DateTimeSpecial2=";
      $Q .= "'" . $DateTimeSpecial2_fdate2->format('Y-m-d H:i:s') . "'";
}
$Q .= " WHERE ";$Q .= "id ";$Q .= "= ";
$Q .= $_POST['updateid'];}
     else
     if ($_POST["tran"] == "Create"){
       $Q = "INSERT INTO testy (";$Q .= "Char10";if ($_SESSION['security'] & 16) $Q .= ", IReq";$Q .= ", IntNormal";$Q .= ", IntCheckbox";$Q .= ", DecimalVal";$Q .= ", Email";$Q .= ", Date1";if ($_SESSION['security'] & 16) $Q .= ", DateTimeSpecial2";$Q .= " ) VALUES (";
       $Q .= "'" . mysqli_real_escape_string($con, $Char10_f) . "'";
if ($_SESSION['security'] & 16) {       $Q.= ",";
       $Q .= "'" . $IReq_f . "'";
}
       $Q.= ",";
       $Q .= "'" . $IntNormal_f . "'";
       $Q.= ",";
       $Q .= "'" . $IntCheckbox_f . "'";
       $Q.= ",";
       $Q .= "'" . $DecimalVal_f . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $Email_f) . "'";
       $Q.= ",";
       $Q .= "'" . $Date1_f . "'";
if ($_SESSION['security'] & 16) {       $Q.= ",";
       $Q .= "'" . $DateTimeSpecial2_fdate2->format('Y-m-d H:i:s') . "'";
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
$Char10_f=htmlspecialchars($Char10_f,ENT_QUOTES);
$Email_f=htmlspecialchars($Email_f,ENT_QUOTES);
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
echo "<tr><td class='desc'>VARCHAR(20)</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($Char10_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='Char10_i' ";echo "Value='";echo $Char10_f;echo "' ";echo " autofocus ";echo ">";echo "</td>";echo "<td>";
echo $Char10_err; echo "</td></tr>";
}
?>
<?php if ($_SESSION['security'] & 16)
{
echo "<tr><td class='desc'>INT REQUIRED</td><td>*</td>";
echo "<td>";
echo "<input ";
if (strlen($IReq_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='IReq_i' ";echo "Value='";echo $IReq_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $IReq_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>INT</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($IntNormal_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='IntNormal_i' ";echo "Value='";echo $IntNormal_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $IntNormal_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>INT CHECKBOX</td><td></td>";
echo "<td><input type='checkbox' name='IntCheckbox_i[]' Value='1' ";if ($IntCheckbox_f ==1) echo "checked";echo "></td>";echo "<td>";
echo $IntCheckbox_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>DECIMAL</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($DecimalVal_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='DecimalVal_i' ";echo "Value='";echo $DecimalVal_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $DecimalVal_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>EMAIL</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($Email_err) > 0) echo "class='err' ";echo "type='email' ";echo "name='Email_i' ";echo "Value='";echo $Email_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $Email_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>DATE</td><td>*</td>";
echo "<td><input type='date' name='Date1_i' Value='" . substr($Date1_f,0,10) . "'></td>";
echo "<td>";
echo $Date1_err; echo "</td></tr>";
}
?>
<?php if ($_SESSION['security'] & 16)
{
echo "<tr><td class='desc'>DATESPECIAL</td><td></td>";
echo "<td><input type='date' name='DateTimeSpecial2_i' Value='" . $DateTimeSpecial2_f . "'><select name='DateTimeSpecial2_ia' Value='" . $DateTimeSpecial2_fa . "'>";
echo "<option value='00'";if ($DateTimeSpecial2_fa=="00") echo " selected";echo ">00</option>";
echo "<option value='01'";if ($DateTimeSpecial2_fa=="01") echo " selected";echo ">01</option>";
echo "<option value='02'";if ($DateTimeSpecial2_fa=="02") echo " selected";echo ">02</option>";
echo "<option value='03'";if ($DateTimeSpecial2_fa=="03") echo " selected";echo ">03</option>";
echo "<option value='04'";if ($DateTimeSpecial2_fa=="04") echo " selected";echo ">04</option>";
echo "<option value='05'";if ($DateTimeSpecial2_fa=="05") echo " selected";echo ">05</option>";
echo "<option value='06'";if ($DateTimeSpecial2_fa=="06") echo " selected";echo ">06</option>";
echo "<option value='07'";if ($DateTimeSpecial2_fa=="07") echo " selected";echo ">07</option>";
echo "<option value='08'";if ($DateTimeSpecial2_fa=="08") echo " selected";echo ">08</option>";
echo "<option value='09'";if ($DateTimeSpecial2_fa=="09") echo " selected";echo ">09</option>";
echo "<option value='10'";if ($DateTimeSpecial2_fa=="10") echo " selected";echo ">10</option>";
echo "<option value='11'";if ($DateTimeSpecial2_fa=="11") echo " selected";echo ">11</option>";
echo "<option value='12'";if ($DateTimeSpecial2_fa=="12") echo " selected";echo ">12</option>";
echo "<option value='13'";if ($DateTimeSpecial2_fa=="13") echo " selected";echo ">13</option>";
echo "<option value='14'";if ($DateTimeSpecial2_fa=="14") echo " selected";echo ">14</option>";
echo "<option value='15'";if ($DateTimeSpecial2_fa=="15") echo " selected";echo ">15</option>";
echo "<option value='16'";if ($DateTimeSpecial2_fa=="16") echo " selected";echo ">16</option>";
echo "<option value='17'";if ($DateTimeSpecial2_fa=="17") echo " selected";echo ">17</option>";
echo "<option value='18'";if ($DateTimeSpecial2_fa=="18") echo " selected";echo ">18</option>";
echo "<option value='19'";if ($DateTimeSpecial2_fa=="19") echo " selected";echo ">19</option>";
echo "<option value='20'";if ($DateTimeSpecial2_fa=="20") echo " selected";echo ">20</option>";
echo "<option value='21'";if ($DateTimeSpecial2_fa=="21") echo " selected";echo ">21</option>";
echo "<option value='22'";if ($DateTimeSpecial2_fa=="22") echo " selected";echo ">22</option>";
echo "<option value='23'";if ($DateTimeSpecial2_fa=="23") echo " selected";echo ">23</option>";
echo "</select>:<select name='DateTimeSpecial2_ib' Value='" . $DateTimeSpecial2_fb . "'>";
echo "<option value='00'";if ($DateTimeSpecial2_fb=="00") echo " selected";echo ">00</option>";
echo "<option value='15'";if ($DateTimeSpecial2_fb=="15") echo " selected";echo ">15</option>";
echo "<option value='30'";if ($DateTimeSpecial2_fb=="30") echo " selected";echo ">30</option>";
echo "<option value='45'";if ($DateTimeSpecial2_fb=="45") echo " selected";echo ">45</option>";
echo "</select></td>";echo "<td>";
echo $DateTimeSpecial2_err; echo "</td></tr>";
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
