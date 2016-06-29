<!DOCTYPE HTML>
<html>
<head>
<title>Register</title>
<link rel="stylesheet" type="text/css" href="heading4.css">
<style>
body {margin: 0px;font-family: Arial, Helvetica, sans-serif;background-color:#f0f0ff;}
#container {background-color:#f0f0ff;}
#entry {background-color:#f0f0ff;margin-left:20px;}
table {border-collapse: collapse;}
.right {text-align: right;}
.err {color: red;}
p.p1 {font-size:14px;}
p.p2 {font-size:12px;}
</style>
</head>
<body>

<?php
$errtext="";
function generateRandomString($length = 6) {
$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
$randomString = '';
for ($i = 0; $i < $length; $i++) {
    $randomString .= $characters[rand(0, strlen($characters) - 1)];
}
return $randomString;
}
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
 $HaveUserRec=0;
 $alreadyreg=0;
 if (mysqli_connect_errno())
 {
  $errtext= "Failed to connect to Database: " . mysqli_connect_error();
 }
 else
 {
  $email = $_POST['email'];
  $email = trim($email);
  $email = strtolower($email);
  $q="SELECT id, displayname , org from members where email = '" . $email . "'";
  $r = mysqli_query($con,$q);
  if (mysqli_num_rows($r) == 1)
  {
   $row = mysqli_fetch_array($r);
   $memid = $row[0];
   $dispname = $row[1];
   $org = $row[2];
   $q="SELECT id, force_pw_reset from users where member = " .$memid;
   $r = mysqli_query($con,$q);
   if (mysqli_num_rows($r) > 0)
   {
    $row = mysqli_fetch_array($r);
    $HaveUserRec=$row[0];
    if ($row[1] == 0)
    {
     $alreadyreg=1;
      $errtext= 'You have already registered; click <a href=\'Login.php\'>here</a> to login';
    }
   }
   if ($alreadyreg==0)
   {
    $pw = generateRandomString();
    $pw2 = md5($pw);
    if ($HaveUserRec>0)
    {
     $q="UPDATE users SET password = '".$pw2."' where id = ".$HaveUserRec;
    }
    else
    {
     $q="INSERT INTO users(name,org,usercode,password,securitylevel,force_pw_reset,member) VALUE ('" .$dispname. "','" .$org. "','" .$email. "','" .$pw2. "',1,1," . $memid. ")";
    }
    $r = mysqli_query($con,$q);
    $headers = 'From: operations@glidingops.com' . "\r\n" .
     'Reply-To: wgcoperations@gmail.com' . "\r\n" .
     'X-Mailer: PHP/' . phpversion();
    $message = "Login details are Username " . $email . " Temporary Password " . $pw;
    mail($email, "Welcome to Wellington Gliding Club Ops", $message, $headers);
    header('Location: Login.php');
   }
  }
  else
   $errtext = "Sorry, that email address is not recorded as a member";
 mysqli_close($con);
 }
}
?>
<div id='container'>
<div id='entry'>
<p class='p1'>Members can register by entering their email address below:</p>
<p class='p2'>Note, you need to use the email address that the club has in its members list.</p>
<p class='p2'>An email will be sent to you with a temporary password. Remember to <br>check your SPAM folder in case your ISP puts the email there.</p>
<form method='POST' action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<table>
<tr><td>email:</td><td><input type='text' name='email' size='40' autofocus></td><td></td></tr>
<tr><td></td><td class='right'><input type="submit" name"Submit" value="Register"></td><td></td></tr>
</table>
</form>
<p class='err'><?php echo $errtext;?></p>
</div>
</div>
</body>
</html>
