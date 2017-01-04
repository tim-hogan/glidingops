<!DOCTYPE HTML>
<html>
<head>
<title>Forgotten Password</title>
<link rel="stylesheet" type="text/css" href="heading4.css">
<style>
body {margin: 0px;font-family: Arial, Helvetica, sans-serif;background-color:#f0f0ff;}
#container {background-color:#f0f0ff;}
#entry {background-color:#f0f0ff;margin-left:20px;}
h1 {font-size: 20px;}
table {border-collapse: collapse;}
p.fg {font-size:11px;}
.right {text-align: right;}
.err {color: red;}
a {text-decoration: none;}
a.fg {font-size:11px;}
a:link{color: #0000c0;}
a:visited {color: #0000C0;}
a:hover {color: #0000FF;}
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
 if (mysqli_connect_errno())
 {
  $errtext= "Failed to connect to Database: " . mysqli_connect_error();
 }
 else
 {
  $email = $_POST['email'];
  $email = trim($email);
  $email = strtolower($email);
  $q="SELECT * from users where usercode = '" . $email . "'";
  $r = mysqli_query($con,$q);
  if (mysqli_num_rows($r) == 1)
  {
    $row = mysqli_fetch_array($r);
    $pw = generateRandomString();
    $pw2 = md5($pw);
    $q="UPDATE users SET password = '".$pw2."', force_pw_reset = 1 where id = ".$row['id'];
    $r = mysqli_query($con,$q);
    $headers = 'From: operations@glidingops.com' . "\r\n" .
     'Reply-To: wgcoperations@gmail.com' . "\r\n" .
     'X-Mailer: PHP/' . phpversion();
    $message = "Login details are Username " . $email . " Temporary Password " . $pw;
    mail($email, "Wellington Gliding Club Ops", $message, $headers);
    header('Location: Login.php');
  }
  else
  {
   $errtext='The email address you entered is not registered to this site.<br>Either correct and resubmit or register ';
   $errtext .= "<a href='Register.php'>here</a>";
  }
 }
}
?>
<div id='container'>
<div id='entry'>
<h1>Password Reset</h1>
<p>Please enter your email addess, we will email you a new temporary password.</p>
<form method='POST' action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<table>
<tr><td>email:</td><td><input type='text' name='email' size='40' title='Enter email address' autofocus></td><td></td></tr>
<tr><td></td><td class='right'><input type="submit" name"Submit" value="Reset"></td><td></td></tr>
</table>
</form>
<p class='err'><?php echo $errtext;?></p>
</div>
</div>
</body>
</html>
