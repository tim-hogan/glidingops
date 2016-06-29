<!DOCTYPE HTML>
<html>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<head>
<link rel="icon" type="image/png" href="favicon.png" />
<link rel="stylesheet" type="text/css" href="calstyle.css">
<script src="cal.js"></script>
<style type="text/css">
#body {margin: 0px;font-family: Arial, Helvetica, sans-serif;}
#container{margin: 0px;
	   border: 0px;}
#final {background-color:#ff9191;height: 40px;font-size:14px;}
#bottomdiv{height:90px;}
#bookings{background-color:#e0e0e0;border-radius:5px;margin: 5px;}
#bookings2{background-color:#e0e0e0;margin: 5px;}
p.err1 {margin: 0px;color:#A00000;font-size: 12px;}
th {font-size:12px;}
p.p1 {margin: 5px;font-weight: bold;}
p.p2 {margin: 5px;}
td.pr {padding-right: 10px;font-size:14px;}
.bhl {width: 22px;font-size:12px;}
.bhm {width: 22px;font-size:12px;}
.bhr {border-right: black;border-right-style: solid;border-right-width: 1px;width: 20px;font-size:12px;}
.bhrs {border-right: black;border-right-style: solid;border-right-width: 1px;width: 20px;}
.in1 {font-size: 20px;}
input.upper {text-transform:uppercase;}
table {border-collapse:collapse;}
.right {text-align: right;}
.red {color:#ff0000;}
.green {color:#00ff00;}
#tempdiv {width: 800px; height: 260px; position: absolute; top: 0px; left: 0px;background: #c0c0c0;padding: 10px;border-style:solid;border-width:1px;box-shadow: 10px 10px 5px #888888;}
</style>
<script>
var nextRow;
var xmlDoc;
var locstore = 0;
nexttempid=99998;
var inSync=0;
function errmsg(msg)
{
document.getElementById("err").innerHTML=msg;
}
if (window.XMLHttpRequest)
  xmlhttp=new XMLHttpRequest();
<?php
include 'timehelpers.php';
include 'helpers.php';
$DEBUG=0;
$org=0;
$location= '';
if ($_SERVER["REQUEST_METHOD"] == "GET")
{
 $org = $_GET['org'];
 if (isset($_GET['location']) )
 {
  $location = $_GET['location'];
 }
 else
  $location = "";
 if (strlen($location) <=0)
 {
   header('Location: StartDay.php?org='.$org);
   exit();
 }
}
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
$dateTimeZone = new DateTimeZone(orgTimezone($con,$org));
$dateTime = new DateTime("now", $dateTimeZone);
$dateStr = $dateTime->format('Ymd');
$dateStr2 = $dateTime->format('Y-m-d');
$flights = "";
$towpilotroleid=0;
$diagtext="";
echo "var strToday=\"" . $dateStr2 ."\";";
echo "var strTodayYear=\""  . $dateTime->format('Y') . "\";";
echo "var strTodayMonth=\""  . $dateTime->format('m') . "\";";
echo "var strTodayDay=\""   . $dateTime->format('d') . "\";"; 

$towpilotroleid=getRoleId($con,'Tow Pilot');
$towplanetype=getTowPlaneType($con,$org);

$launchTypeTow=getTowLaunchType($con);
$launchTypeSelf=getSelfLaunchType($con);
$launchTypeWinch=getWinchLaunchType($con);
$shorttermclass = getShortTermClass($con,$org);
$towChargeType = getTowChargeType($con,$org);
echo "var towChargeType=" . $towChargeType . ";";

 //Find billing option for Other Member 
 $r = mysqli_query($con,"SELECT * FROM billingoptions WHERE bill_other = 1");
 $row = mysqli_fetch_array($r);
 $billing_other_member = $row['id'];



$q2 = "SELECT * FROM flights WHERE flights.org = ".$org." and localdate = " . $dateStr . " ORDER BY seq ASC";
$r2 = mysqli_query($con,$q2);
$row_cnt = $r2->num_rows;
if ($row_cnt > 0)
{
   

   //We have already some info for today
   while ($row = mysqli_fetch_array($r2) )
   {
      $udpver = $row['updseq'];
      $flights .= "<flight>";
      $flights .= "<org>";
      $flights .= $org;
      $flights .= "</org><location>";
      $flights .= $row['location'];
      $flights .= "</location><id>";
      $flights .= $row['seq'];
      $flights .= "</id><del>";
      $flights .= $row['deleted'];
      $flights .= "</del><launchtype>";
      $flights .= $row['launchtype'];
      $flights .= "</launchtype><plane>";
      
      if ($row['launchtype'] == $launchTypeTow)
           $flights .= "t" . $row['towplane'];
      else
           $flights .= "l" . $row['launchtype'];
      
      $flights .= "</plane><glider>";
      $flights .= $row['glider'];
      $flights .= "</glider><towpilot>";
      $flights .= $row['towpilot'];
      $flights .= "</towpilot><p1>";
      $flights .= $row['pic'];
      $flights .= "</p1><p2>";
      $flights .= $row['p2'];
      $flights .= "</p2><start>";
      $flights .= $row['start'];
      $flights .= "</start><towland>";
      $flights .= $row['towland'];
      $flights .= "</towland><land>";
      $flights .= $row['land'];
      $flights .= "</land><height>";
      $flights .= $row['height'];
      $flights .= "</height><charges>";
      if ($row['billing_option'] == $billing_other_member)
      {
           $flights .= "m" . $row['billing_member1'];
      }
      else
      {
           $flights .= "c" . $row['billing_option'];
      }


      
      $flights .= "</charges><comments>";
      $flights .= $row['comments'];
      $flights .= "</comments></flight>";




   }
}
else
    $udpver = 1;

//Create tow pilot list
$pilots="";
$q2 = "SELECT a.id, a.displayname, a.surname , a.firstname from role_member LEFT JOIN members a ON a.id = role_member.member_id where role_member.org = ".$org. " and role_id = " .$towpilotroleid . " order by a.surname,a.firstname";
$r2 = mysqli_query($con,$q2);
while ($row = mysqli_fetch_array($r2) )
{
	$pilots .= "<pilot><id>";
	$pilots .= $row[0];
        $pilots .= "</id><name>";
	$pilots .= $row[1];
	$pilots .= "</name></pilot>";
}

$members="";
$olddate = new DateTime("now");
$olddate->setTimestamp($olddate->getTimestamp() - (3600*24*90));   
$q2 = "SELECT * from members where org = ".$org." and class <> ".$shorttermclass." or (class = ".$shorttermclass." and create_time > '".$olddate->format('Y-m-d')."') order by displayname ASC";
//$q2 = "SELECT * FROM members where org=".$org." ORDER BY displayname ASC";
$r2 = mysqli_query($con,$q2);
while ($row = mysqli_fetch_array($r2) )
{
	$members .= "<member><id>";
	$members .= $row['id'];
        $members .= "</id><name>";
	$members .= $row['displayname'];
	$members .= "</name></member>";
}

//Billing options
$chargeopts="<ChargeOpts>";
$q2 = "SELECT * FROM billingoptions";
$r2 = mysqli_query($con,$q2);
while ($row = mysqli_fetch_array($r2) )
{
 if ($row['bill_other'] == 0)
 {
   $chargeopts .= "<opt><id>";
   $chargeopts .= $row['id'];
   $chargeopts .= "</id><desc>";
   $chargeopts .= $row['name'];
   $chargeopts .= "</desc></opt>";
 }
}
$chargeopts .="</ChargeOpts>";

//Tow Planes
$towplanes="<TowPlanes>";
$q2 = "SELECT * FROM aircraft where aircraft.org = ".$org. " and type = " .$towplanetype;
$r2 = mysqli_query($con,$q2);
while ($row = mysqli_fetch_array($r2) )
{
   $towplanes .= "<plane><id>";
   $towplanes .= $row['id'];
   $towplanes .= "</id><rego>";
   $towplanes .= $row['rego_short'];
   $towplanes .= "</rego></plane>";
}
$towplanes .="</TowPlanes>";

       
mysqli_close($con);	
	
echo "var updseq=" . $udpver . ";";
echo "var server_updseq = updseq;";
?>
var datestring = "<?php echo $dateTime->format('Ymd');?>";
<?php $tnow=time()*1000;$strnow =(string)$tnow;?>
var fxml="<timesheet><newassocs></newassocs><date>" + "<?php echo $dateStr;?>" + "</date><updseq>" + updseq + "</updseq><flights>" + "<?php echo $flights;?>" +"</flights></timesheet>";
var towpilotxml = "<tpilots>" + "<?php echo $pilots;?>" + "</tpilots>";
var allmembers = "<allmembers>" + "<?php echo $members;?>" + "</allmembers>";
var chargeopts = "<?php echo $chargeopts;?>";
var towplanes = "<?php echo $towplanes;?>";
var pollcnt=0;

function ShowCheckErrors(xml)
{
  var bErr=0;
  var k;
  var e;
  var checks=xml.getElementsByTagName("checks")[0].childNodes;
  var divnode = document.getElementById("areachecks");
  
  //remove the bookins area
  var divbookings = document.getElementById("bookings");
  if (null != divbookings)
  	divbookings.parentNode.removeChild(divbookings);

  //remove all the child nodes
  var cn=divnode.childNodes;
  while (cn.length > 0)
  {
    divnode.removeChild(cn[0]);
    cn=divnode.childNodes;
  }


  for (k=0;k<checks.length;k++)
  {
     if (checks[k].nodeName=="err")
     {
       
       if (bErr==0)
       {
          e = document.createElement("H2");
          e.innerHTML="Validation Error"; 
          divnode.appendChild(e);
          bErr=1;
       }
       e = document.createElement("P");
       e.setAttribute("class","err1");
       e.innerHTML = checks[k].childNodes[0].nodeValue;
       divnode.appendChild(e);
     }
     
  }

  if (bErr==0)
  	window.location.href = "CompletedSheet.php?org=<?php echo $org;?>";
}

function xmlReplyType(xml)
{
  if (null != xml)
  {
    console.log(xml2Str(xml));
    var node;
    try {node=xml.getElementsByTagName("status")[0].childNodes;}catch(err){node=null;}
    if (null != node) return "status";
    try {node=xml.getElementsByTagName("bookings")[0].childNodes;}catch(err){node=null;}
    if (null != node) return "bookings";
    try {node=xml.getElementsByTagName("checks")[0].childNodes;}catch(err){node=null;}
    if (null != node) return "checks";
    try {node=xml.getElementsByTagName("allmembers")[0].childNodes;}catch(err){node=null;}
    if (null != node) return "allmembers";
  }
  return "";    	
}

xmlhttp.onreadystatechange = function () 
{
    if (xmlhttp.readyState == 4) 
    {
    	console.log("Reply from server");
        var xmlReply = xmlhttp.responseXML;
        var replyType=xmlReplyType(xmlReply);      	
                
        if (replyType=="allmembers")
        {
            console.log("Reply type: allmembers"); 
            allmembers = xml2Str(xmlReply);
            var r = 0;
            for (r=0;r < 100;r++)
            {
 	    	var iRow = r +1;
                
	        
                var n = document.getElementById("e"+ iRow);
                if (null != n)
                {
                   var p1 = document.getElementById("e" + iRow).value;
                   //remove all list
		   while (n.firstChild) 
  			n.removeChild(n.firstChild);
                   AddEntriesToDropDown(n,allmembers,"allmembers",p1,"new");
                }
                 
 	    	n = document.getElementById("f"+iRow);
                if (null != n)
                {
                   var p2 = document.getElementById("f" + iRow).value;
                   //remove all list
		   while (n.firstChild) 
  			n.removeChild(n.firstChild);
                   AddEntriesToDropDown(n,allmembers,"allmembers",p2,"trial");
                }
                 
            } 


        }


        if (replyType=="bookings")
        {
           console.log("Reply type: bookings"); 
           buildbookingtable(xml2Str(xmlReply),"btable",1);
        }
        if (replyType=="checks")
        {
           console.log("Reply type: checks"); 
           ShowCheckErrors(xmlReply);
        }
        if (replyType=="status")
        {
           console.log("Reply type: status"); 


		var status = xmlReply.getElementsByTagName("status")[0].childNodes[0].nodeValue;
                
                //See if we have any diag messages.
                var diagmsg="";
		if (null !=  xmlReply.getElementsByTagName("diag")) 
		{
                    try
                    {
                     diagmsg = xmlReply.getElementsByTagName("diag")[0].childNodes[0].nodeValue;
                     document.getElementById("diag").innerHTML = diagmsg;
                    }
		    catch(err)
                    {
                    }
		}
                //check for member updates.
		var nodes=xmlReply.getElementsByTagName("upd")[0].childNodes;
                
                for (i=0;i<nodes.length;i++)
                {
                    
                    if (nodes[i].nodeName == "member")
                    {
	                var tid = nodes[i].getElementsByTagName("tempid")[0].childNodes[0].nodeValue;
	                var cell = nodes[i].getElementsByTagName("cell")[0].childNodes[0].nodeValue;
	                var colid = nodes[i].getElementsByTagName("colid")[0].childNodes[0].nodeValue;
                        var id = nodes[i].getElementsByTagName("id")[0].childNodes[0].nodeValue;
	                var disp = nodes[i].getElementsByTagName("displayname")[0].childNodes[0].nodeValue;
                        
			//Find the cell and update the real ID
			var eNodes = document.getElementById(cell).childNodes;
                        for(j=0;j<eNodes.length;j++)
                        {
			   if(eNodes[j].value==tid)
				eNodes[j].value=id;
			}

                        
			//Add this member to the new lists
                        if (colid=="towpilot")
			{
				parser=new DOMParser();
  				towDoc=parser.parseFromString(towpilotxml,"text/xml");
				var pilots = towDoc.getElementsByTagName("tpilots")[0].childNodes;	
            			var pilot = towDoc.createElement("pilot");
				towDoc.getElementsByTagName("tpilots")[0].appendChild(pilot);
				var nid = towDoc.createElement("id");				
				nid.appendChild(towDoc.createTextNode(id));
				pilot.appendChild(nid);

				var nnam = towDoc.createElement("name");				
				nnam.appendChild(towDoc.createTextNode(disp));
				pilot.appendChild(nnam);
				towpilotxml=xml2Str(towDoc);
				console.log("Update towpilot list " + towpilotxml);
					
			}
			//Now add to members list
			parser=new DOMParser();
  			memDoc=parser.parseFromString(allmembers,"text/xml");
			var members = memDoc.getElementsByTagName("allmembers")[0].childNodes;	
       			var member = memDoc.createElement("member");
			memDoc.getElementsByTagName("allmembers")[0].appendChild(member);
			var nid = memDoc.createElement("id");				
			nid.appendChild(memDoc.createTextNode(id));
			member.appendChild(nid);

			var nnam = memDoc.createElement("name");				
			nnam.appendChild(memDoc.createTextNode(disp));
			member.appendChild(nnam);
			allmembers=xml2Str(memDoc);
			console.log("Update towpilot list " + allmembers);


			//Delete this node form the update required
			var newassocnode = xmlDoc.getElementsByTagName("newassocs")[0].childNodes;
			for(k=0;k<newassocnode.length;k++)
			{
			    if(newassocnode[k].nodeName=="member")
		            {
			    	if (newassocnode[k].getElementsByTagName("tempid")[0].childNodes[0].nodeValue == tid)
			    		xmlDoc.getElementsByTagName("newassocs")[0].removeChild(newassocnode[k]);
			    }
			}
		    }
		}
                
		
                server_updseq = xmlReply.getElementsByTagName("updseq")[0].childNodes[0].nodeValue;
		if (parseInt(server_updseq) == parseInt(updseq))
		{
			inSync = 1;
                        var st = document.getElementById("sync");
			st.innerHTML = "Sync";
			st.setAttribute("class","green");
		}	

		console.log("Reply from server status: " + status);
		
	}
    }
}


function sendXMLtoServer()
{
	//Update the field that show sync
        inSync=0;
	var st = document.getElementById("sync");
	st.innerHTML = "Syncing";
	st.setAttribute("class","red");
        var v="updflights.php";
        var params="org=<?php echo $org; ?>&upd=" + xml2Str(xmlDoc);
           
        console.log(v + params);
	xmlhttp.open("POST", v, true);
        //Send the proper header information along with the request
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        //xmlhttp.setRequestHeader("Content-length", params.length);
        //xmlhttp.setRequestHeader("Connection", "close");
        xmlhttp.send(params);
}

function getBookings()
{
        console.log("StrToday = " + strToday);
        strToday = strToday+"";
        var v="bookingsForDate.php?date=" + strToday + "&org=<?php echo $org; ?>";
 	xmlhttp.open("GET", v, true);
        xmlhttp.send();
}

function getMembers()
{
        console.log("StrToday = " + strToday);
        strToday = strToday+"";
        var v="memberlistfortimesheet.php?org=<?php echo $org; ?>";
 	xmlhttp.open("GET", v, true);
        xmlhttp.send();
}

function finalise()
{
   if (inSync==0)
   {   
	sendXMLtoServer();
	alert("Synchronising data with server first, try again after server is in Sync");
   }
   else
   {
   	var r = confirm("Please confirm that the day is complete");  
   	if (r == true) 
   	{
     	   if (anyDeleted(xmlDoc) )
           {
	    r = confirm("There are delete items, please confirm these are to be deleted.");
           }
           if (r == true)
           {
            var org=<?php echo $org; ?>;
            var v="DaycheckAndFinal.php?date=" + datestring + "&org=" + org;
     	    xmlhttp.open("GET", v, true);
     	    xmlhttp.send();
           }
   	} 
   }
        
}

function pad(num, size) {
    var s = num+"";
    while (s.length < size) s = "0" + s;
    return s;
}
function firstUpper(str)
{
   var l=str.length;
   if (l>0)
   {
       var s = str.substr(0,1);
       return s.toUpperCase() + str.substr(1,(l-1));
   }
   return str;
}
function strNodeValue(node)
{
	if (null != node)
	   return node.nodeValue;
        else
           return "";
}

function updatenode(doc,node,val)
{
  if (null != node.childNodes[0])
    node.childNodes[0].nodeValue = val;
  else
  {
     node.appendChild(doc.createTextNode(val));
  }
}

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

function UnSelect(field)
{
   var selelement = document.getElementById(field);
   var a;
   if (null != selelement)
   {
   	var opts = selelement.childNodes;
        for (a=0;a< opts.length;a++)
        {
           opts[a].selected = false;
	}
        opts[0].selected = true;
   }

}

function newassociatecancel(field)
{
   UnSelect(field);
   document.body.removeChild(document.getElementById("tempdiv"));
   document.getElementById("container").style.display="block";
}
function newassociate(field)
{
 console.log("New associate field = " + field);
 var strTempId = nexttempid.toString();
 var colid = "";
 var org=<?php echo $org; ?>;
 
 var sur = document.getElementById("naa").value;
 var fir = document.getElementById("nab").value;
 var mob = document.getElementById("nac").value;
 var em = document.getElementById("nad").value;
 
 //We need to check that fields have been filled out
 if (sur.length == 0 || fir.length == 0)
 {  
    alert("You must enter values for Surname and Firstname");
    return;
 } 

 sur = firstUpper(sur);
 fir = firstUpper(fir);

 var f=document.getElementById(field);
 colid= f.getAttribute("colname");
 

 var s = document.createElement("option");
 var displayname = fir + " " + sur;
 s.value= strTempId;
 
 s.innerHTML = displayname;
 f.appendChild(s);
 s.selected=true;

 var newassocnode = xmlDoc.getElementsByTagName("newassocs")[0];
 if (null != newassocnode)
 {
    console.log("Create assosiate creating XML");
    var t;
    var s = xmlDoc.createElement("member"); 
    newassocnode.appendChild(s);
    
    t = xmlDoc.createElement("tempid");
    t.appendChild(xmlDoc.createTextNode(strTempId));
    s.appendChild(t);
     
    t = xmlDoc.createElement("cell");
    t.appendChild(xmlDoc.createTextNode(field));
    s.appendChild(t);

    t = xmlDoc.createElement("colid");
    t.appendChild(xmlDoc.createTextNode(colid));
    s.appendChild(t);

    t = xmlDoc.createElement("org");
    t.appendChild(xmlDoc.createTextNode(org));
    s.appendChild(t);  


    t = xmlDoc.createElement("surname");
    t.appendChild(xmlDoc.createTextNode(sur));
    s.appendChild(t);

    t = xmlDoc.createElement("firstname");
    t.appendChild(xmlDoc.createTextNode(fir));
    s.appendChild(t);

    t = xmlDoc.createElement("displayname");
    t.appendChild(xmlDoc.createTextNode(displayname));
    s.appendChild(t);
    
    t = xmlDoc.createElement("mobile");
    t.appendChild(xmlDoc.createTextNode(mob));
    s.appendChild(t);

    t = xmlDoc.createElement("email");
    t.appendChild(xmlDoc.createTextNode(em));
    s.appendChild(t);
 }
 else
   console.log("ERROR creating XML");
 nexttempid++;
 fieldchange(f);
 document.body.removeChild(document.getElementById("tempdiv"));
 document.getElementById("container").style.display="block";
}

function createAssociateMember(field)
{
  //We need to create a new window
  console.log("In createAssco field = " + field);
  //Need to hide the container
  document.getElementById("container").style.display="none";



  var win=document.createElement("div");
  win.id = "tempdiv";
  document.body.appendChild(win);
  var codeHTML = 
"<table>" +
"<tr><td>FIRSTNAME * </td><td><input class='in1' type = 'text' name='Name' id='nab' size='30' autofocus required></td></tr>" +
"<tr><td>SURNAME * </td><td><input class='in1' type = 'text' name='Name' id='naa' size='30' required></td></tr>" +
"<tr><td>MOBILE</td><td><input class='in1' type = 'text' name='mobile' id='nac'size='20'></td></tr>" +
"<tr><td>EMAIL</td><td><input class='in1' type = 'text' name='email' id='nad' size='40'></td></tr>" +
"<tr><td><button class='in1' onclick='newassociate(\"" + field + "\")'>Enter</button></td><td><button class='in1' onclick='newassociatecancel(\"" + field + "\")'>Cancel</button></td></tr>" +
"</table>";
win.innerHTML = codeHTML;
}

function anyDeleted(doc)
{
  var list = doc.getElementsByTagName("flights")[0].childNodes;
  for (i=0; i<list.length; i++) 
  {
    if (list[i].nodeName == "flight")
    {
       var d = list[i].getElementsByTagName("del")[0].childNodes[0].nodeValue;
       if (d == "1")
          return true;
    }	
  } 
  return false;
}

function findxmlflightseq(list,id)
{
	for (i=0; i<list.length; i++)
	{
		if (list[i].nodeName == "flight")
            	{
			var vid = list[i].getElementsByTagName("id")[0].childNodes[0].nodeValue;
			if (vid == id)
				return list[i];
		}	
	}
	return null;	 
}


function updatexmlflight(doc,seq,launchtype,plane,glider,towpilot,p1,p2,start,towland,land,height,charges,comments,del)
{
   var list = doc.getElementsByTagName("flights")[0].childNodes;
   var flight =  findxmlflightseq(list,seq);  
   //Look to see if it exists
   if (null != flight )
   { 
	
	node = flight.childNodes;
	var i;
	
	var node2 = flight.getElementsByTagName("towpilot");
	
	updseq++;   	
        updatenode(doc,doc.getElementsByTagName("updseq")[0],updseq);
	updatenode(doc,flight.getElementsByTagName("launchtype")[0],launchtype);
        updatenode(doc,flight.getElementsByTagName("plane")[0],plane);
	updatenode(doc,flight.getElementsByTagName("glider")[0],glider);
	updatenode(doc,flight.getElementsByTagName("towpilot")[0],towpilot);
	updatenode(doc,flight.getElementsByTagName("p1")[0],p1);
	updatenode(doc,flight.getElementsByTagName("p2")[0],p2);
	updatenode(doc,flight.getElementsByTagName("start")[0],start);
	updatenode(doc,flight.getElementsByTagName("land")[0],land);
        updatenode(doc,flight.getElementsByTagName("towland")[0],towland);
	updatenode(doc,flight.getElementsByTagName("height")[0],height);
	updatenode(doc,flight.getElementsByTagName("charges")[0],charges);
	updatenode(doc,flight.getElementsByTagName("comments")[0],comments);
        updatenode(doc,flight.getElementsByTagName("del")[0],del);
   }
   else
   {
       var org=<?php echo $org;?>;
       var loc='<?php echo $location;?>';
       var vnode,newtext;
       updseq++;   	
       updatenode(doc,doc.getElementsByTagName("updseq")[0],updseq);       

       flight = doc.createElement('flight');
       doc.getElementsByTagName("flights")[0].appendChild(flight);
       
       vnode = doc.createElement('org');
       newtext=doc.createTextNode(org);
       vnode.appendChild(newtext);
       flight.appendChild(vnode);
 
       vnode = doc.createElement('location');
       newtext=doc.createTextNode(loc);
       vnode.appendChild(newtext);
       flight.appendChild(vnode);
 
       vnode = doc.createElement('id');
       newtext=doc.createTextNode(seq);
       vnode.appendChild(newtext);
       flight.appendChild(vnode);  

       vnode = doc.createElement('launchtype');
       newtext=doc.createTextNode(launchtype);
       vnode.appendChild(newtext);
       flight.appendChild(vnode);
 
       vnode = doc.createElement('plane');
       newtext=doc.createTextNode(plane);
       vnode.appendChild(newtext);
       flight.appendChild(vnode);
       
       vnode = doc.createElement('glider');
       newtext=doc.createTextNode(glider);
       vnode.appendChild(newtext);
       flight.appendChild(vnode);

       vnode = doc.createElement('towpilot');
       newtext=doc.createTextNode(towpilot);
       vnode.appendChild(newtext);
       flight.appendChild(vnode);

       vnode = doc.createElement('p1');
       newtext=doc.createTextNode(p1);
       vnode.appendChild(newtext);
       flight.appendChild(vnode);

       vnode = doc.createElement('p2');
       newtext=doc.createTextNode(p2);
       vnode.appendChild(newtext);
       flight.appendChild(vnode);
       
       vnode = doc.createElement('start');
       newtext=doc.createTextNode(start);
       vnode.appendChild(newtext);
       flight.appendChild(vnode);
       
       vnode = doc.createElement('land');
       newtext=doc.createTextNode(land);
       vnode.appendChild(newtext);
       flight.appendChild(vnode);
       
       vnode = doc.createElement('towland');
       newtext=doc.createTextNode(towland);
       vnode.appendChild(newtext);
       flight.appendChild(vnode);
       
       vnode = doc.createElement('height');
       newtext=doc.createTextNode(height);
       vnode.appendChild(newtext);
       flight.appendChild(vnode);
       
       vnode = doc.createElement('charges');
       newtext=doc.createTextNode(charges);
       vnode.appendChild(newtext);
       flight.appendChild(vnode);
       
       vnode = doc.createElement('comments');
       newtext=doc.createTextNode(comments);
       vnode.appendChild(newtext);
       flight.appendChild(vnode);
       
       vnode = doc.createElement('del');
       newtext=doc.createTextNode(del);
       vnode.appendChild(newtext);
       flight.appendChild(vnode);

   }
   if (locstore ==1)
       localStorage.setItem(datestring, xml2Str(doc) );
}

function AddEntriesToDropDown(selnode,listxml,listtag,selvalue,newval)
{
	//Create first null entry
	var opt = document.createElement("option");
        opt.value = "0";
	opt.innerHTML = "";
	selnode.appendChild(opt);
	
        opt = document.createElement("option");
        opt.value = "99999";
	opt.innerHTML = newval;
	selnode.appendChild(opt);
        
        parser=new DOMParser();
  	dropDoc=parser.parseFromString(listxml,"text/xml");	
	if (null != dropDoc)
	{	                                 
	    var mems = dropDoc.getElementsByTagName(listtag)[0].childNodes;	
            for (i=0;i < mems.length;i++)
            {
		var id = mems[i].getElementsByTagName("id")[0].childNodes[0].nodeValue;
		var name = mems[i].getElementsByTagName("name")[0].childNodes[0].nodeValue;
		opt = document.createElement("option");
		opt.value = id;
		opt.innerHTML = name;
		selnode.appendChild(opt);
	    }
	}
	
        //update the value to selected
	var optlist = selnode.childNodes;
	for (i=0; i<optlist.length; i++)
	{
	    if (optlist[i].value == selvalue)
		optlist[i].selected = true;
            else
		optlist[i].selected = false;

	}	
 
}
function CreateDropDownList(row,colnum,colname,collid,listxml,listtag,selvalue,newval)
{
        var r = row.insertCell(colnum);
	sel = "<select colname='" + colname + "' onchange='fieldchange(this)'></select>";
	r.innerHTML=sel;
	r.firstChild.setAttribute("id",collid);
	var selnode = r.firstChild;

	//Create first null entry
	var opt = document.createElement("option");
        opt.value = "0";
	opt.innerHTML = "";
	selnode.appendChild(opt);

	opt = document.createElement("option");
        opt.value = "99999";
	opt.innerHTML = newval;
	selnode.appendChild(opt);

        parser=new DOMParser();
  	dropDoc=parser.parseFromString(listxml,"text/xml");	
	if (null != dropDoc)
	{	                                 
	    var mems = dropDoc.getElementsByTagName(listtag)[0].childNodes;	
            for (i=0;i < mems.length;i++)
            {
		var id = mems[i].getElementsByTagName("id")[0].childNodes[0].nodeValue;
		var name = mems[i].getElementsByTagName("name")[0].childNodes[0].nodeValue;
		opt = document.createElement("option");
		opt.value = id;
		opt.innerHTML = name;
		selnode.appendChild(opt);
	    }
	}
	
        
	//update the value to selected
	var selnode = r.firstChild;
	var optlist = selnode.childNodes;
	for (i=0; i<optlist.length; i++)
	{
	    if (optlist[i].value == selvalue)
		optlist[i].setAttribute("selected","");
	}

	
}

function addrowdata(id,plane,glider,towy,p1,p2,start,towland,land,height,charges,comments,del)
{
	
        console.log("Add row data plane = " + plane);
        var sel;
	var table = document.getElementById("t1");
	var row = table.insertRow(-1);
	
	row.insertCell(0).innerHTML=id;
	

        var r1 = row.insertCell(1);
        sel = "<select colname='" + "launch" + "' onchange='fieldchange(this)'></select>";
	r1.innerHTML=sel;
        r1.firstChild.setAttribute("id","b" + nextRow);
	var selnode = r1.firstChild;

	parser=new DOMParser();
  	towPlaneDoc=parser.parseFromString(towplanes,"text/xml");	
	if (null != towPlaneDoc)
	{	                                 
	    var mems = towPlaneDoc.getElementsByTagName("TowPlanes")[0].childNodes;	
            for (i=0;i < mems.length;i++)
            {
		if (mems[i].nodeName == "plane")
                {
			var id = mems[i].getElementsByTagName("id")[0].childNodes[0].nodeValue;
			var rego = mems[i].getElementsByTagName("rego")[0].childNodes[0].nodeValue;
			var opt = document.createElement("option");
			
			opt.value = "t"+id;
			opt.innerHTML = rego;
			if (plane == ("t"+id))
                            opt.setAttribute("selected","");
			
			selnode.appendChild(opt);
                }
	    }
	}
  


        
        var opt = document.createElement("option");
        var id=<?php echo $launchTypeSelf;?>;
        opt.value = "l"+id;
	opt.innerHTML = "SELF";
        if (plane == ("l"+id))
           opt.setAttribute("selected","");
        selnode.appendChild(opt);	
        
        opt = document.createElement("option");
        id = <?php echo $launchTypeWinch;?>;
        opt.value = "l"+id;
	opt.innerHTML = "WINCH";
        if (plane == ("l"+id))
           opt.setAttribute("selected","");
        selnode.appendChild(opt);	

	var r2 = row.insertCell(2);
	r2.innerHTML="<input type='text' name='glider[]' maxlength='3' size='4' class='upper' onchange='fieldchange(this)'>";
	r2.firstChild.setAttribute("id","c" + nextRow);
	r2.firstChild.setAttribute("value",glider);

        //New towpilot code	
	
        CreateDropDownList(row,3,"towpilot","d"+nextRow,towpilotxml,"tpilots",towy,"new");
 	CreateDropDownList(row,4,"pic","e"+nextRow,allmembers,"allmembers",p1,"new");
	CreateDropDownList(row,5,"p2","f"+nextRow,allmembers,"allmembers",p2,"Trial");
	
	var r6 = row.insertCell(6);
	if (parseInt(start) == 0)
	{
		r6.innerHTML="<button name='start[]' type='button' onclick='startbutton(this)'>Start</button>";
		r6.firstChild.setAttribute("id","g" + nextRow);
		r6.firstChild.setAttribute("timedata","0");
	}
	else
	{
		var para = document.createElement("input");
		var d = new Date(parseInt(start));
		para.setAttribute("onchange","timechange(this)");
                para.setAttribute("timedata",d.getTime());	
		para.value= pad(d.getHours(),2) + ":" + pad(d.getMinutes(),2);
		para.setAttribute("prevval",para.value);
		para.size=5;
		r6.appendChild(para);
		r6.firstChild.setAttribute("id","g" + nextRow);
	}
	
	var nextCol = 7;
        //Tow charging based on time code follows
        if (towChargeType == 2)
        {
           var r13 = row.insertCell(nextCol);
           nextCol++;
	   if (parseInt(towland) == 0)
	   {
		r13.innerHTML="<button name='towland[]' type='button' onclick='towlandtbutton(this)'>Tow Land</button>";
		r13.firstChild.setAttribute("id","n" + nextRow);
		r13.firstChild.setAttribute("timedata","0");
	   }
	   else
	   {
		var para = document.createElement("input");
		var d = new Date(parseInt(towland));
		para.setAttribute("onchange","timechange(this)");
                para.setAttribute("timedata",d.getTime());	
		para.value= pad(d.getHours(),2) + ":" + pad(d.getMinutes(),2);
		para.setAttribute("prevval",para.value);
		para.size=5;
		r13.appendChild(para);
		r13.firstChild.setAttribute("id","n" + nextRow);
	   }
        }




        var r7=row.insertCell(nextCol);
        nextCol++;
	if (parseInt(land) == 0)
	{
		r7.innerHTML="<button name='land[]' type='button' onclick='landbutton(this)'>Land</button>";
		r7.firstChild.setAttribute("id","h" + nextRow);	
		r7.firstChild.setAttribute("timedata","0");
	}
	else
	{
		var para = document.createElement("input");
		var d = new Date(parseInt(land));
		para.setAttribute("onchange","timechange(this)");
                para.setAttribute("timedata",d.getTime());	
		para.value= pad(d.getHours(),2) + ":" + pad(d.getMinutes(),2);
		para.setAttribute("prevval",para.value);
		para.size=5;
		r7.appendChild(para);
		r7.firstChild.setAttribute("id","h" + nextRow);
	}	
	
	
        if (towChargeType == 1)
        {
          sel = "<select onchange='fieldchange(this)'></select>";
	  var r8=row.insertCell(nextCol);
          nextCol++;
	  r8.innerHTML=sel;
	  r8.firstChild.setAttribute("id","i" + nextRow);
	  var selnode = r8.firstChild;

	  //Create an empty node

	  var opt = document.createElement("option");
          opt.value = "0";
	  opt.innerHTML = "";
	  selnode.appendChild(opt);

	  for (h=500;h<6000;h+=500)
	  {	
		opt = document.createElement("option");
		opt.value = h.toString();
		opt.innerHTML =  h.toString();
		if (h == parseInt(height))
			opt.setAttribute("selected","");
		selnode.appendChild(opt);
	  }

	  opt = document.createElement("option");
          opt.value = "-1";
	  opt.innerHTML = "Check Flight";
	  if (-1 == parseInt(height))
	      opt.setAttribute("selected","");
	  selnode.appendChild(opt);

	  opt = document.createElement("option");
          opt.value = "-2";
	  opt.innerHTML = "Retrieve";
	  if (-2 == parseInt(height))
	      opt.setAttribute("selected","");
	  selnode.appendChild(opt);
	}

        //Time fields
        //If tow time option then we need tiem for tow
        if (towChargeType == 2)
        { 
	  r14 = row.insertCell(nextCol);
          nextCol++;
	  r14.id = "o" + nextRow; 

	  if (parseInt(start) != 0 && parseInt(towland) != 0)
	  {
		//We need to update the flight time.
		var dest = document.getElementById("o" + nextRow);
		var e = parseInt(towland) - parseInt(start);
		mins = Math.floor((e / 60000) % 60);
		var n = document.createTextNode(pad(Math.floor( e / (3600 * 1000)),2) + ":" + pad(mins,2));
		dest.appendChild(n);	
	  }

	
        }


	r9 = row.insertCell(nextCol);
        nextCol++;
	r9.id = "j" + nextRow; 	

	if (parseInt(start) != 0 && parseInt(land) != 0)
	{
		//We need to update the flight time.
		var dest = document.getElementById("j" + nextRow);
		var e = parseInt(land) - parseInt(start);
		mins = Math.floor((e / 60000) % 60);
		var n = document.createTextNode(pad(Math.floor( e / (3600 * 1000)),2) + ":" + pad(mins,2));
		dest.appendChild(n);	
	}

        

	
        r10 = row.insertCell(nextCol);
        nextCol++;
        sel = "<select colname='" + "charge" + "' onchange='fieldchange(this)'></select>";
	r10.innerHTML=sel;
        r10.firstChild.setAttribute("id","k" + nextRow);
	var selnode = r10.firstChild;

	parser=new DOMParser();
  	chargeDoc=parser.parseFromString(chargeopts,"text/xml");	
	if (null != chargeDoc)
	{	                                 
	    var mems = chargeDoc.getElementsByTagName("ChargeOpts")[0].childNodes;	
            for (i=0;i < mems.length;i++)
            {
		if (mems[i].nodeName == "opt")
                {
			var id = mems[i].getElementsByTagName("id")[0].childNodes[0].nodeValue;
			var desc = mems[i].getElementsByTagName("desc")[0].childNodes[0].nodeValue;
			opt = document.createElement("option");
			
			opt.value = "c"+id;
			opt.innerHTML = desc;
			if (charges == ("c"+id))
                            opt.setAttribute("selected","");
			
			selnode.appendChild(opt);
                }
	    }
	}

	parser2=new DOMParser();
  	membersDoc=parser2.parseFromString(allmembers,"text/xml");	
	if (null != membersDoc)
	{	                                 
	    var mems = membersDoc.getElementsByTagName("allmembers")[0].childNodes;	
            for (i=0;i < mems.length;i++)
            {
		
		var id = mems[i].getElementsByTagName("id")[0].childNodes[0].nodeValue;
		var name = mems[i].getElementsByTagName("name")[0].childNodes[0].nodeValue;
		opt = document.createElement("option");
			
		opt.value = "m"+id;
		opt.innerHTML = name;
		if (charges == ("m"+id))
                    opt.setAttribute("selected","");
			
		selnode.appendChild(opt);
                
	    }
	}




	


        r11 = row.insertCell(nextCol);
        nextCol++;
	r11.innerHTML="<input type='text' name='comment[]' size='30' onchange='fieldchange(this)'>";
	r11.firstChild.setAttribute("value",comments);
	r11.firstChild.setAttribute("id","l" + nextRow);
	
	r12 = row.insertCell(nextCol);
        nextCol++;
        if (del == "0")	
         r12.innerHTML="<button name='delete[]' type='button' onclick='deleteline(this)'>DELETE</button>";
	else
         r12.innerHTML="<button name='delete[]' type='button' onclick='deleteline(this)'>UNDELETE</button>";        
        r12.firstChild.setAttribute("id","m" + nextRow);	
	r12.firstChild.setAttribute("value",del);
	if (del != "0")
            greyRow(nextRow,1);
}

function greyItem(item,b)
{
    console.log('Grey Item ' + item);
    if (b > 0)
    {
     document.getElementById(item).style.textDecoration="line-through";
     document.getElementById(item).style.color="#c0c0c0";
    }
    else
    {
     document.getElementById(item).style.textDecoration="none";
     document.getElementById(item).style.color="#000000"; 
    }
}
function greyRow(iRow,b)
{
    greyItem("b" + iRow,b);  //Plane
    greyItem("c" + iRow,b);  //Gider
    greyItem("d" + iRow,b);  //Tow Pilot
    greyItem("e" + iRow,b);  //PIC
    greyItem("f" + iRow,b);  //P2
    greyItem("g" + iRow,b);  //Start
    if (towChargeType==1) 
    	greyItem("i" + iRow,b);  //Height
    if (towChargeType==2)
    	greyItem("n" + iRow,b);  //Tow Land button
    greyItem("h" + iRow,b);  //Land
    greyItem("j" + iRow,b);  //Glide Time
    greyItem("k" + iRow,b);
    greyItem("l" + iRow,b);
    if (towChargeType==2)
	greyItem("o" + iRow,b);  //Tow Time
}

function deleteline(what)
{
	var iRow = what.id; 
	iRow = iRow.substring(1,iRow.length);
        if (what.value == 0)
        {
	   what.value="1";
           what.innerHTML="UNDELETE";
           greyRow(iRow,1);
        }
        else
        {
	   what.value="0";
           what.innerHTML="DELETE";
           greyRow(iRow,0);
        }
        fieldchange(what);
}

function fieldchange(what)
{
	var iRow = what.id; 
	iRow = iRow.substring(1,iRow.length);
	
	var plane = document.getElementById("b" + iRow).value;
        var launchtype = <?php echo $launchTypeTow;?>;


        var glider = document.getElementById("c" + iRow).value;
	glider = glider.toUpperCase();
        if (glider.length > 0)
        {
            if (glider.length == 3 && glider.substring(0,1) != "G")
              glider = "G" + glider.substring(1,3);
            if (glider.length == 2)
              glider = "G" + glider;
            document.getElementById("c" + iRow).value = glider;
        }
    
        var towp = document.getElementById("d" + iRow).value;
	var p1 = document.getElementById("e" + iRow).value;
	var p2 = document.getElementById("f" + iRow).value;
        
	if (towp == "99999" || p1=="99999" || p2=="99999")
        {
            UnSelect(what.id);
            createAssociateMember(what.id);
            return;
        }
	
	var n = document.getElementById("g" + iRow);
	var start = n.getAttribute("timedata");
	
	n = document.getElementById("h" + iRow);
	var land = n.getAttribute("timedata");
	
        //towland is optional
        var towland = 0;
        if (towChargeType == 2)
        {
          n = document.getElementById("n" + iRow);
	  towland = n.getAttribute("timedata");
        }
        
        var height = 0;
        if (towChargeType == 1)
        {
          height = document.getElementById("i" + iRow).value;
          if (parseInt(height) == -1)
          {
	       var ch = document.getElementById("k" +iRow).childNodes;
               for (mm=0;mm<ch.length;mm++)
               {
                  ch[mm].selected=false;
                  if (ch[mm].value == "c9")
			ch[mm].selected=true;
	       }
          }  
        }      

        var charges= document.getElementById("k" + iRow).value;
	var comments = document.getElementById("l" + iRow).value;
	var del = document.getElementById("m" + iRow).value;
	
	updatexmlflight(xmlDoc,iRow,launchtype,plane,glider,towp,p1,p2,start,towland,land,height,charges,comments,del);
	//update the seq
	sendXMLtoServer();
}

function calcFlightTime(iRow)
{
 

 var dest = document.getElementById("j" + iRow);
 //get end time
 var edbut = document.getElementById("h" + iRow);
 
 if (edbut.nodeName.toUpperCase() == "INPUT")
 {
   var etv = edbut.getAttribute("timedata")				
              
   var stbut = document.getElementById("g" + iRow);
   var tv = stbut.getAttribute("timedata");
   var e = parseInt(etv) - parseInt(tv);
   mins = Math.floor((e / 60000) % 60);
   while (null != dest.childNodes[0])
     dest.removeChild(dest.childNodes[0]);	
 
   var n = document.createTextNode(pad(Math.floor( e / (3600 * 1000)),2) + ":" + pad(mins,2));
   dest.appendChild(n);
 }
 //Tow plane time if this type of charging
 if (towChargeType==2)
 {
    dest = document.getElementById("o" + iRow);
    edbut = document.getElementById("n" + iRow);
    if (edbut.nodeName.toUpperCase() == "INPUT")
    {
      var etv = edbut.getAttribute("timedata")				
              
      var stbut = document.getElementById("g" + iRow);
      var tv = stbut.getAttribute("timedata");
      var e = parseInt(etv) - parseInt(tv);
      mins = Math.floor((e / 60000) % 60);
      while (null != dest.childNodes[0])
         dest.removeChild(dest.childNodes[0]);	
 
      var n = document.createTextNode(pad(Math.floor( e / (3600 * 1000)),2) + ":" + pad(mins,2));
      dest.appendChild(n);
    }
 }
}

function getseconds(strTime)
{
 var n = strTime.search(":");  
 return (parseInt(strTime.substr(0,n)) * 3600)+(parseInt(strTime.substr(n+1,strTime.length-(n+1)) ) * 60);
}
function checktimestr(strTime)
{
  var n = strTime.search(":");  
  if (n <= 0)
    return false;
  if (strTime.length < (n+3))
    return false;

  var hours = parseInt(strTime.substr(0,n));
  var mins = parseInt(strTime.substr(n+1,strTime.length-(n+1)) );
  
  if (hours < 0 || hours > 23)
     return false;

  if (mins < 0 || mins > 59)
     return false;


  return true;
}

function timechange(what)
{
  var iRow = what.id;   
  iRow = iRow.substring(1,iRow.length);

  var strPrev = what.getAttribute('prevval');
  var strNew  = what.value;
  strNew= strNew.trim();
  strPrev= strPrev.trim();
  
  if (!checktimestr(strNew) || !checktimestr(strPrev))
  {
     alert("Invalid time format; please enter as hh:mm");
     what.value=strPrev;
     return;
  }
  
  var x = strNew.search(":");  
  var hours = parseInt(strNew.substr(0,x));
  var mins = parseInt(strNew.substr(x+1,strNew.length-(x+1)) );
  


  var n=getseconds(strNew)-getseconds(strPrev);
  n = n * 1000;  
  var d = new Date();
  var d2 = new Date(strTodayYear,parseInt(strTodayMonth)-1,strTodayDay,hours,mins,0);

  d.setTime(parseInt(what.getAttribute('timedata'))+n);
  console.log("Old time " + what.getAttribute('timedata'));
  console.log("New time " + d.getTime());

  what.setAttribute("timedata",d2.getTime());	
  what.value= pad(d2.getHours(),2) + ":" + pad(d2.getMinutes(),2);
  what.setAttribute("prevval",what.value);
  
  calcFlightTime(iRow);
  fieldchange(what);
}

function poll()
{
  pollcnt++;
  if ((pollcnt % 30) == 0)
  {
    if (inSync == 0)
    	sendXMLtoServer();  
  }
  if ((pollcnt % 180) == 5)
  {
      getBookings();
  }
  if ((pollcnt % 180) == 15)
  {
      if (inSync==1)
      	getMembers();
  }
  
  if ((pollcnt % 3600) == 0)
  {
      pollcnt = 0;
  }
}

function StartUp()
{
	setInterval(poll,1000);

        var bUpdServer = 0;
	var lastTowPilot="";
	var lxml=null;
	if(typeof(Storage) !== "undefined") 
	{
    		locstore = 1;
		lxml = localStorage.getItem(datestring);
	} else {
    		lxml = null;
	}


        
	parser=new DOMParser();
  	xmlDoc=parser.parseFromString(fxml,"text/xml");	
	
	var st = document.getElementById("sync");
	st.innerHTML = "Sync";
	st.setAttribute("class","green");
	inSync=1;

	
        if (null != lxml)
	{	        
		
		
		parserl=new DOMParser();
  		ldoc=parserl.parseFromString(lxml,"text/xml");

		//Is this version greater than that from the server.
		var s = xmlDoc.getElementsByTagName("updseq")[0].childNodes[0].nodeValue;
		var l = ldoc.getElementsByTagName("updseq")[0].childNodes[0].nodeValue;
		
		if (parseInt(l) > parseInt(s) )
		{
			fxml = lxml;
			xmlDoc=parser.parseFromString(fxml,"text/xml");
			st.innerHTML = "Not Syncronised";
			st.setAttribute("class","red");
			updseq = parseInt(l);
			bUpdServer = 1;
			inSync=0;
		}
		
	}
	
	
	if (locstore==1)
		localStorage.setItem(datestring, fxml);
	console.log(xml2Str(xmlDoc));
	
	
	var dt = xmlDoc.getElementsByTagName("date")[0].childNodes[0].nodeValue;
	
	
        nextRow = 1;
	var today = new Date(parseInt(dt));
	var year    = today.getFullYear();
	var month   = today.getMonth() + 1;
	var day     = today.getDate();
	document.getElementById("dayfield").innerHTML = dt.substring(6,8) + "/" + dt.substring(4,6) + "/" + dt.substring(0,4);
	
	
	grplist = xmlDoc.getElementsByTagName("flights")[0].childNodes;
        
	
        var k;
	for (k=0; k<grplist.length; k++)
	{
	    
	    if 	(grplist[k].nodeName == "flight")
            {
		var vid = grplist[k].getElementsByTagName("id")[0].childNodes[0].nodeValue;	    	
		var vplane = strNodeValue(grplist[k].getElementsByTagName("plane")[0].childNodes[0]);
		var vglider = strNodeValue(grplist[k].getElementsByTagName("glider")[0].childNodes[0]);
		var vtow = strNodeValue(grplist[k].getElementsByTagName("towpilot")[0].childNodes[0]);
		lastTowPilot=vtow;
		var vp1 = strNodeValue(grplist[k].getElementsByTagName("p1")[0].childNodes[0]);
		var vp2 = strNodeValue(grplist[k].getElementsByTagName("p2")[0].childNodes[0]);
		var vstart = grplist[k].getElementsByTagName("start")[0].childNodes[0].nodeValue;
		var vtowland = grplist[k].getElementsByTagName("towland")[0].childNodes[0].nodeValue;
                var vland = grplist[k].getElementsByTagName("land")[0].childNodes[0].nodeValue;
		var vheight = grplist[k].getElementsByTagName("height")[0].childNodes[0].nodeValue;
                var vcharge = grplist[k].getElementsByTagName("charges")[0].childNodes[0].nodeValue;
		var vcomments = strNodeValue(grplist[k].getElementsByTagName("comments")[0].childNodes[0]); 
		var vdel = strNodeValue(grplist[k].getElementsByTagName("del")[0].childNodes[0]); 
		addrowdata(vid,vplane,vglider,vtow,vp1,vp2,vstart,vtowland,vland,vheight,vcharge,vcomments,vdel);
		nextRow++
		
	    }
		
	}
	addrowdata(nextRow,"","",lastTowPilot,"","","0","0","0","","","","0");
	nextRow++;
	
	if (bUpdServer == 1)
		sendXMLtoServer();
	
	getBookings();
}

function startbutton(what)
{
	
 	var stid = what.id;
	var iRow = what.id;   // h rownumber
	iRow = iRow.substring(1,iRow.length);
	var parent = what.parentNode;
	parent.removeChild(what);
	var para = document.createElement("input");
	para.setAttribute("onchange","timechange(this)");
	var d = new Date();
	para.setAttribute("timedata",d.getTime());	
	para.value= pad(d.getHours(),2) + ":" + pad(d.getMinutes(),2);
	para.setAttribute("prevval",para.value);
	para.id = stid;	
	para.size=5;
	parent.appendChild(para);
	
	

        //Get the value of P2
        var p2 = document.getElementById("f" +iRow).value;	
        if (p2 == "0")
        {
		//check now check if k = set to P2 change to PIC
	       var ch1 = document.getElementById("k" +iRow).value;
               if (ch1=="c1")
               {
               var ch = document.getElementById("k" +iRow).childNodes;
               for (mm=0;mm<ch.length;mm++)
               {
                  ch[mm].selected=false;
                  if (ch[mm].value == "c2")
			ch[mm].selected=true;
	       }
		}	
	}
        
        
        fieldchange(what);

	//Create a new row in the table
	var nextrow = parseInt(iRow) + 1;
	var check = document.getElementById("b" +nextrow);
	if (null == check)	
	{
		var tp = "d" + iRow;
		var strtp = document.getElementById(tp).value;
		addrowdata(nextRow,"SUG","",strtp,"","","0","0","0","","","","0");
		nextRow++;
	}
}

function towlandbutton(what)
{
	var stid = what.id;
	var iRow = what.id;   // n rownumber
	iRow = iRow.substring(1,iRow.length);
	var n = document.getElementById("g" + iRow);	
        if (n.getAttribute("timedata") != "0")
	{
		
		var parent = what.parentNode;
		parent.removeChild(what);
		var para = document.createElement("input");
		var d = new Date();
		para.setAttribute("onchange","timechange(this)");
                para.setAttribute("timedata",d.getTime());
                para.value= pad(d.getHours(),2) + ":" + pad(d.getMinutes(),2);
		para.setAttribute("prevval",para.value);
		para.size=5;
		para.id = stid;	
		parent.appendChild(para);

		calcFlightTime(iRow);
		fieldchange(what);
	}
	
}

function landbutton(what)
{
	var stid = what.id;
	var iRow = what.id;   // h rownumber
	iRow = iRow.substring(1,iRow.length);
	var n = document.getElementById("g" + iRow);	
        if (n.getAttribute("timedata") != "0")
	{


		
		var parent = what.parentNode;
		parent.removeChild(what);
		var para = document.createElement("input");
		var d = new Date();
		para.setAttribute("onchange","timechange(this)");
                para.setAttribute("timedata",d.getTime());
                para.value= pad(d.getHours(),2) + ":" + pad(d.getMinutes(),2);
		para.setAttribute("prevval",para.value);
		para.size=5;
		para.id = stid;	
		parent.appendChild(para);

		calcFlightTime(iRow);
		fieldchange(what);
	}
	
}

function AddNewLine()
{
   var iRow = (nextRow-1);
   var strtp = document.getElementById("d" + iRow).value;
   addrowdata(nextRow,"SUG","",strtp,"","","0","0","0","","","","0");
   nextRow++;
}

</script>
</head>
<body id="body" onload="StartUp()">
<?php if ($org <= 0){ die("Cannot start daily log sheet as Club Organisation not specified");}  ?>
<?php if (strlen($location) == 0){ header('Location: StartDay.php?org='.$org);}  ?>
<div id="container">
<span id='dayfield'>DATE</span>
<span id='sync'>SYNC</span><br>
<table id='t1'>
<?php if ($towChargeType==2) echo "<tr><th colspan='9'></th><th colspan='2'>TIME</th></tr><tr>";?>
<th>SEQ</th>
<th>PLANE</th>
<th>GLIDER</th>
<th>TOW PILOT</th>
<th>PIC</th>
<th>P2</th>
<th>START</th>
<?php if ($towChargeType==2) echo "<th>TOW LAND</th>";?>
<th>LAND</th>
<?php if ($towChargeType==1) echo "<th>HEIGHT</th>";?>
<?php if ($towChargeType==2) echo "<th>TOW</th><th>GLIDER</th>";?>
<?php if ($towChargeType==1) echo "<th>TIME</th>";?>
<th>BILLING</th>
<th>COMMENTS</th>
</tr>
</table>
<div id='bottomdiv'>
<button onclick="AddNewLine()">Add Line</button>
<br><button id='final' onclick='finalise()'>Check and Finish Day</button>
</div>
<div id='areachecks'>
</div>
<div id='bookings'>
<p class='p1'>TODAY'S BOOKINGS</p>
<div id='bookings2'>
<p></p>
<table id='btable'>
</table>
</div>
</div>
<p id="err"></p>
<p id="diag"><?php if($DEBUG>0)echo $diagtext;?></p>
</div>
</body>
</html>
