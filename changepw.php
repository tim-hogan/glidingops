<?php session_start(); ?>
<?php
if(isset($_SESSION['security']))
{
 if (!($_SESSION['security'] & 1))
 {
  die("Secruity level too low for this page");
 }
}
else
{
 header('Location: Login.php');
 die("Please logon");
}
?>
<!DOCTYPE HTML>
<html>
<head>
</head>
<body>
<?php
$DEBUG=0;
$errtxt=""; 
$myusername=$_SESSION['who'];
$mypasswordold=$_POST['pcodeold'];
$myusername = stripslashes($myusername);
$mypasswordold = stripslashes($mypasswordold);
$mypasswordold = md5($mypasswordold);



$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
{
 echo "<p>Unable to connect to database</p>";
 exit();
}
$sql="SELECT * FROM users WHERE usercode='$myusername'";
 if($DEBUG>0)
      echo "<p>" . $sql . "</p>";
$r = mysqli_query($con,$sql);
$row = mysqli_fetch_array($r);
if ($row['password'] == $mypasswordold)
{
 
 $newpw1 = $_POST['pcodenew1'];
 $newpw2 = $_POST['pcodenew2'];
 $newpw1 = stripslashes($newpw1);
 $newpw2 = stripslashes($newpw2);
 if ($newpw1 != $newpw2)
 {
    $errtxt =  "New passords not identicle";
    
 }
 else
 {
     $sql="UPDATE users SET password = '" . md5($newpw1) ."' , force_pw_reset = 0 where usercode='$myusername'";
     $r = mysqli_query($con,$sql);
     header('Location: home');
  }
}
else
{
  $errtxt = "ERROR: Wrong Old Password Specified";
}
mysqli_close($con);
echo "<p>". $errtxt . "</p>";
?>


</body>
</html>
