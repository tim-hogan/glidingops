<?php session_start(); ?>
<?php
if(isset($_SESSION['security']))
{
 if (!($_SESSION['security'] & 127))
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
<script>
<?php
$DEBUG=0;
$dateTimeZone = new DateTimeZone($_SESSION['timezone']);
$dateTime = new DateTime('now', $dateTimeZone);

?>
var datestring = "20150418";
function escapeHtml(unsafe) {
    return unsafe
         .replace(/&/g, "&amp;")
         .replace(/</g, "&lt;")
         .replace(/>/g, "&gt;")
         .replace(/"/g, "&quot;")
         .replace(/'/g, "&#039;");
 }

function Start()
{
	var lxml=null;
	if(typeof(Storage) !== "undefined") 
	{
    		
		lxml = localStorage.getItem(datestring);
                document.getElementById("xml").innerHTML = escapeHtml(lxml);
	} else {
    		lxml = null;
	}
}
function ClearStorage(){localStorage.removeItem(datestring);}
</script>
</head>
<body onload='Start()'>
<h1>Current XML Stored in local browser store is</h1>
<p id="xml"></p>
<button onclick='ClearStorage()'>Clear Local Storage</button>
</body>
</html>
