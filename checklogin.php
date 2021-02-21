<?php session_start(); ?>
<!DOCTYPE HTML>
<html>
<head>
</head>
<body>
    <?php
$DEBUG=0;
$pagesortdata = array();
for ($i = 0; $i < 65;$i++)
 $pagesortdata[$i] = 0;
$myusername=$_POST['user'];
$mypassword=$_POST['pcode'];
$myusername = stripslashes($myusername);
$mypassword = stripslashes($mypassword);
$mypassword = md5($mypassword);
$con_params = require('./config/database.php');
$con_params = $con_params['gliding'];
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
{
 echo "<p>Unable to connect to database</p>";
 exit();
}
$sql="SELECT * FROM users WHERE usercode='$myusername'";
$r = mysqli_query($con,$sql);
$row = mysqli_fetch_array($r);
if ($row['password'] == $mypassword)
{
  $doForceCheck=1;
  $_SESSION['userid']=$row['id'];
  $_SESSION['who']=$myusername;
  $_SESSION['memberid']=$row['member'];
  $_SESSION['org']=$row['org'];
  $_SESSION['session_key']=base64_encode(openssl_random_pseudo_bytes(32));
  if ($_SESSION['org'] === NULL)
    $_SESSION['org'] = 0;
  $_SESSION['security']=$row['securitylevel'];
  $_SESSION['pagesortdata']=$pagesortdata ;
  $_SESSION['dispname']=$row['name'];
  if ($_SESSION['org'] != 0)
  {
    $q="SELECT timezone from organisations where id = " . $_SESSION['org'];
     $r2 = mysqli_query($con,$q);
     $row2 = mysqli_fetch_array($r2);
     $_SESSION['timezone'] = $row2[0];
  }
  $desc='Login';
  if (!isset($_SESSION['memberid']) || $_SESSION['memberid'] === NULL)
   $q="INSERT INTO audit (userid,description) VALUES (" . $row['id'] .",'" . $desc . "')";
  else
    $q="INSERT INTO audit (userid,memberid,description) VALUES (" . $row['id'] ."," . $row['member'] .",'" . $desc . "')";
  $r = mysqli_query($con,$q);
  if($doForceCheck>0)
  {
    if ($row['force_pw_reset'] > 0)
    {
      header('Location: PasswordChange');
    }
    else
      header('Location: home');
  }
  else
    header('Location: home');
}
else
{
  echo "Wrong Username or Password";
}
mysqli_close($con);
    ?>
</body>
</html>
