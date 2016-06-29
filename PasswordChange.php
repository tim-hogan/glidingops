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
<title>Change Password</title>
<link rel="stylesheet" type="text/css" href="heading4.css">
<style>
body {margin: 0px;font-family: Arial, Helvetica, sans-serif;background-color:#f0f0ff;}
#container {background-color:#f0f0ff;}
#entry {background-color:#f0f0ff;margin-left:20px;}
table {border-collapse: collapse;}
.right {text-align: right;}
</style>
</head>
<body>
<div id='container'>
<div id='entry'>
<h2>Change Password</h2>
<form method='POST' action='changepw.php'>
<table>
<tr><td>Username:</td><td><?php echo $_SESSION['who']; ?></td><td></td></tr>
<tr><td>Old Password:</td><td><input type='password' name='pcodeold' autofocus></td><td></td></tr>
<tr><td>New Password:</td><td><input type='password' name='pcodenew1'></td><td></td></tr>
<tr><td>Re-enter New Password:</td><td><input type='password' name='pcodenew2'></td><td></td></tr>
<tr><td></td><td class='right'><input type="submit" name"Submit" value="Change"></td><td></td></tr>
</table>
</form>
</div>
</div>
</body>
</html>
