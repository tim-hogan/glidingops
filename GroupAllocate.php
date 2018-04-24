<?php
  include './helpers/session_helpers.php';
  include 'helpers.php';

  session_start();
  require_security_level(6);

  $org = current_org();
?>

<!DOCTYPE HTML>
<html>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<head>
<style type="text/css">
h2 {font-size: 12px; font-weight: normal;}
p.p1 {margin-top: 0px; font-size: 12px;}
td.rederr {margin-top: 0px; font-size: 12px;color: #ff0000;}
td {font-size: 10px; font-weight: normal;}
#body {margin: 0px;font-family: Arial, Helvetica, sans-serif;}
#container{margin: 0px;
	border: 0px;}
#centrearea{width: 940px;background-color: #e8e8ff;margin:0 auto;}
#titlearea{width: 900px;background-color: #e8e8ff;margin:0 auto;}
#selectarea{width: 900px;background-color: #e8e8ff;margin:0 auto;}
#entrybox{width: 500px;background-color: #d8d8d8;border-radius: 5px;margin: 40px 40px 10px 40px;padding: 10px}
#wholists{width: 800px;background-color: #d8d8d8;border-radius: 5px;margin: 10px 40px 10px 40px;padding: 10px}
#nav_bar{width: 820px; height: 30px;background-color: #f0f0f0;margin: 10px 40px 10px 40px;border-radius: 5px 5px 5px 5px;}

ul {list-style-type: none;padding-left: 10px;padding-right: 10px;}
li {float:left; width:100px; background-color: #f0f0f0; text-align:center;padding-top: 5px;padding-bottom: 5px;}
li.right {float:right; width:100px; background-color: #f0f0f0; text-align:center;padding-top: 5px;padding-bottom: 5px;}
</style>
<script>
var div;
var g_org = 0;
<?php echo "g_org=" . $_SESSION['org']. ";" ?>
if (window.XMLHttpRequest)
  xmlhttp=new XMLHttpRequest();

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

function GetGroups(id)
{
	  var v = "GroupXML.php?org=" + g_org + "&groupid=" + id;
	  console.log (v);
          xmlhttp.open("GET", v, true);
          xmlhttp.send();
}
function StartUp()
{
	  GetGroups(1);
}

function whatGroup(val)
{
   var ca = document.getElementById("centrearea");
   ca.removeChild(div);
   GetGroups(val);
}

xmlhttp.onreadystatechange = function ()
{
    if (xmlhttp.readyState == 4)
    {
        div = document.createElement('div');
	var bd = "<table>";
        var rowcnt = 0;
	var columns = 6;
	console.log ("XML Returned");
        xmlDoc = xmlhttp.responseXML;
        console.log (xml2Str(xmlDoc));
	grplist = xmlDoc.getElementsByTagName("grouplist")[0].childNodes;
        for (i=0; i<grplist.length; i++)
	{
	    console.log (grplist[i].nodeName);
	    if 	(grplist[i].nodeName == "member")
            {
		if ((rowcnt % columns ) == 0)
			bd += "<tr>";
		bd += "<td><input type='checkbox' name=member[] value='";
		bd += grplist[i].getElementsByTagName("id")[0].childNodes[0].nodeValue;
		bd += "'";
		if (grplist[i].getElementsByTagName("incl")[0].childNodes[0].nodeValue == 1)
			bd += " checked ";
		bd += ">";
		bd += grplist[i].getElementsByTagName("name")[0].childNodes[0].nodeValue;
		bd += "</td>";
		rowcnt++;
		if (((rowcnt -columns ) % columns ) == 0)
			bd += "</tr>";

	    }
	}
	if (((rowcnt -columns ) % columns ) != 0)
	    bd += "</tr>";
	bd += "</table><input type = 'submit' value = 'Enter'>";
	div = document.createElement('div');
	div.setAttribute('id', 'wholists');
	div.innerHTML = bd;
	var ca = document.getElementById("centrearea");
	ca.appendChild(div);
    }

};

</script>
</head>
<body id="body" onload="StartUp()">
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$con_params = require('./config/database.php'); $con_params = $con_params['gliding'];
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
        if (mysqli_connect_errno())
	{
	}
	else
	{
	    $q1= "SELECT * FROM members where org = " . $_SESSION['org'];
            $r1 = mysqli_query($con,$q1);
            while ($row = mysqli_fetch_array($r1) )
            {
	        if(in_array((string)$row['id'],$_POST["member"]) )
		{
			//We need to check to see if we already have a record
			$q2 = "SELECT * FROM group_member WHERE gm_group_id = " . $_POST["group"] . " and gm_member_id = " . $row['id'];
			$r2 = mysqli_query($con,$q2);
			if ($r2->num_rows == 0)
			{
				$q3 = "INSERT INTO group_member (gm_group_id,gm_member_id) VALUES ('" . $_POST["group"] . "','" .  $row['id'] . "')";
				$r3 = mysqli_query($con,$q3);
			}
		}
		else
		{
			//We need to check to see if we already have a record in whcih case delete it
			$q2 = "SELECT * FROM group_member WHERE gm_group_id = " . $_POST["group"] . " and gm_member_id = " . $row['id'];
			$r2 = mysqli_query($con,$q2);
			if ($r2->num_rows != 0)
			{
				$q3 = "DELETE FROM group_member WHERE gm_group_id = " . $_POST["group"] . " and gm_member_id = " .  $row['id'];
				$r3 = mysqli_query($con,$q3);
			}
		}
	     }
	}


}
?>
<div id="container">
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<div id="centrearea">
<div id="titlearea">
<h1>Wellington Gliding Club Groups</h1>
</div>
<div id="nav_bar">
<ul>
<li><a href = '/'>Home</a></li>
<li><a href = 'groups.php'>Define</a></li>
<li class='right'>Help</li>
</ul>
</div>
<div id="selectarea">
<select onchange="whatGroup(this.value)" name="group">
<?php
$con_params = require('./config/database.php'); $con_params = $con_params['gliding'];
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (!mysqli_connect_errno())
{
	$q1 = "SELECT * FROM groups where org = " . $_SESSION['org'];
        $r1 = mysqli_query($con,$q1);
        while ($row = mysqli_fetch_array($r1) )
        {
        	echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
	}
}
?>
</select>
</div>
</div>
</form>
</div>
</body>
</html>