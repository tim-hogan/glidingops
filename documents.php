<?php session_start(); ?>
<?php $org=0; if(isset($_SESSION['org'])) $org=$_SESSION['org'];?>
<?php
include 'timehelpers.php';
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
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<head>
<style><?php $inc = "./orgs/" . $org . "/heading2.css"; include $inc; ?></style>
<style type="text/css">
body {margin: 0px;font-family: Arial, Helvetica, sans-serif;}
#container{margin: 5px;border: 0px;}
#menu1{margin: 5px;border: 0px;background-color:#e0e0e0;padding:1px;border-radius: 5px;}
#menu2{margin: 5px;border: 0px;background-color:#e0e0e0;}
#menu2 td {vertical-align: top;}
a {text-decoration: none;border-left: 5px;}
a:link{color: #000000;}
a:visited {color: #000000;}
a:hover {color: #0000FF;}
p.u {font-size: 12px;margin-top: 0px;margin-left: 0px;margin-bottom: 0px;padding-left: 15px;}
h1 {font-size: 16px; color: #0000ff}
h2.u {font-size: 14px;}
h3.u {font-size: 12px; margin-left: 10px;}
table {border-collapse:collapse;}
table.tbl1 {width: 100%;table-layout: fixed;}
.s1 {font-weight: bold;color: #000080}
</style>
</head>
<body>
<?php $inc = "./orgs/" . $org . "/heading2.txt"; include $inc; ?>
<div id='container'>
<div id="menu1">
<div id="menu2">
<?php
if ($org == 1)
{
echo "<h2 class='u'>OPERATIONS</h2>";
echo "<p class='u'><a href = '";echo "./orgs/" . $org . "/docs/MOU20141127.pdf"; echo "'>MEMORANDUM OF UNDERSTANDING</a></p>";
echo "<p class='u'><a href = '";echo "./orgs/" . $org . "/docs/ROLV4.pdf";echo "'>REGISTER OF LIMITATIONS and OPERATIONAL CONDITIONS</a></p>";
echo "<p class='u'><a href = '";echo "./orgs/" . $org . "/docs/WGC_PPQ_Operation_Rules_V3_Mar_2014.pdf";echo "'>OPERATIONAL RULES</a></p>";
echo "<h2 class='u'>FORMS</h2>";
echo "<p class='u'><a href = '";echo "./orgs/" . $org . "/docs/KCAHL_Airside_Vehicle_Permit.pdf";echo "'>AIRSIDE VEHCILE PERMIT</a></p>";
}
?>
</div>
</div>
</div>
</body>
</html>