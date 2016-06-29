<?php session_start(); ?>
<?php
$org=0;
$updtext='';
if(isset($_SESSION['org'])) $org=$_SESSION['org'];
if(isset($_SESSION['security'])){
 if (!($_SESSION['security'] & 4)){die("Secruity level too low for this page");}
}else{
 header('Location: Login.php');
 die("Please logon");
}
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
  die("Failed to connect to Database: " . mysqli_connect_error());
include 'helpers.php';
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
   $thisorg = $_POST['org'];
   $mem = $_POST['member'];
   $sj = $_POST['subject'];
   $mess =  $_POST['message'];
   $q = "SELECT * from members where org = ".$thisorg." and id =  ".$mem ;
   $r = mysqli_query($con,$q);
   $row = mysqli_fetch_array($r);
   SendMail($row['email'],$sj,$mess);
   $updtext = "Message sent to: " . $row['email'];
}
?>
<!DOCTYPE HTML>
<html>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<head>
<link rel="stylesheet" type="text/css" href="styleform1.css">
<style><?php $inc = "./orgs/" . $org . "/heading2.css"; include $inc; ?></style>
<style>
<?php $inc = "./orgs/" . $org . "/menu1.css"; include $inc; ?></style>
<link rel="stylesheet" type="text/css" href="styleform1.css">
<style>
td.ra {text-align:right;font-size: 12px;vertical-align: top;}
#d1 {margin: 10px;}
#d2 {margin-left: 10px; bacground-color: #eeeebb; padding:5px;}
</style>
</head>
<body>
<?php $inc = "./orgs/" . $org . "/heading2.txt"; include $inc; ?>
<?php $inc = "./orgs/" . $org . "/menu1.txt"; include $inc; ?>
<script>function goBack() {window.history.back()}</script>
<div id='d1'>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<table>
<tr><td class='ra'>SELECT MEMBER</td>
<td><select name='member'> 
<?php
$q = "SELECT * from members where org = ".$org." and email <> '' order by displayname";
$r = mysqli_query($con,$q);
while ($row = mysqli_fetch_array($r))
{
   echo "<option value = '".$row['id']."'>".$row['displayname']."</option>";
}
?>
</select>
</td></tr>
<tr><td class='ra'>SUBJECT</td><td><input type ='text' name='subject' size='75'></td></tr>
<tr><td class='ra'>ENTER MESSAGE</td>
<td><textarea name = 'message' rows='10' cols='50'></textarea></td>
</tr>
<tr><td><input type='submit' name = 'send' value = 'Send Email'></td></tr>
<?php echo "<input type='hidden' name = 'org' value = '".$org."'>"; ?>
</table>
</form>
</div>
<div id='d2'>
<?php
if (strlen($updtext) > 0)
echo "<p>".$updtext."</p>";
?>
</div>
</body>
</html>
