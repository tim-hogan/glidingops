<?php session_start(); ?>
<!DOCTYPE HTML>
<html>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<head>
<?php
$org = $_SESSION['org'];
$dateTimeZone = new DateTimeZone($_SESSION['timezone']);
$dateTime = new DateTime('now', $dateTimeZone);
$dateStr = $dateTime->format('Y-m-d');
?>
<link rel="stylesheet" type="text/css" href="calstylelarge.css">
<style>
body {margin: 0px;font-family: Arial, Helvetica, sans-serif;}
</style>
<script src="cal.js"></script>
<script>
<?php echo "var strToday=\"".$dateStr."\";";?>
function xml2Str(xmlNode) {
   try {
      // Gecko- and Webkit-based browsers (Firefox, Chrome), Opera.
      return (new XMLSerializer()).serializeToString(xmlNode);
  }
  catch (e) {
     try {
        // Internet Explorer.
        return xmlNode.xml;
     }
     catch (e) {  
        //Other browsers without XML Serializer
        alert('Xmlserializer not supported');
     }
   }
   return false;
}


if (window.XMLHttpRequest)
  xmlhttp=new XMLHttpRequest();


xmlhttp.onreadystatechange = function () 
{
    if (xmlhttp.readyState == 4) 
    {
    	
        var xmlReply = xmlhttp.responseXML;
                 	
        //Check that this is a vlaid reply before parsing.        
	if (null != xmlReply)
	{
		console.log(xml2Str(xmlReply));
        	//check to see what type of reply
                var booknode;
		try {
       		    booknode=xmlReply.getElementsByTagName("bookings")[0].childNodes;
                }
		catch(err)
		{booknode=null;}
		if (null != booknode)       	
               		buildbookingtable(xml2Str(xmlReply),"cal",0);
	}	
        
        
    }
}

function getBookings(strDay)
{      
 var v="bookingsForDate.php?date=" + strDay + "&org=<?php echo $org;?>";
 xmlhttp.open("GET", v, true);
 xmlhttp.send();
}

function DateChange(what)
{
 var n = document.getElementById("date");
 if (null != n);
 {
   var date=n.value;
   var jsDate=new Date(date);
   document.getElementById("id1").innerHTML = jsDate.toDateString();
   getBookings(n.value);
 }
}

function Start()
{
var n = document.getElementById("date");
if (null != n);
{
   	n.value=strToday;
	var jsDate=new Date(strToday);
	document.getElementById("id1").innerHTML = jsDate.toDateString();
	getBookings(strToday);
}
}
</script>
</head>
<body onload='Start()'>
<input id='date' type='date' name='date' onchange="DateChange(this)">
<span id='id1' class="id1"></span>
<p></p>
<table id='cal'>
</table>
</body>
</html>