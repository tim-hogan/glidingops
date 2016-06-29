<!DOCTYPE html>
<head>
<meta http-equiv="refresh" content="3600">
<?php
$org=0;
if (isset($_GET['org']))
 $org=$_GET['org'];
else
 die("Organisation number not set");
?>
<style><?php $inc = "./orgs/" . $org . "/heading1.css"; include $inc; ?></style>
<style>
body {margin:0; font-family: Arial, Helvetica, sans-serif; background: black;}
#container {margin: 0 auto;}
#area1 {background: #000000}
#area1 p{color: #d0d0d0;}
#areamap {float: right;height: 800px;background: #c0c0c0;margin: 10px;}
#map-canvas { height: 100%; margin: 0; padding: 0;}
#bttns {padding: 5px;}
h1 {font-size: 20px;color: #0000e0}
.FlyingDiv {background: #505050;border-style: outset;color: white;font-family: Calibri, Arial, Helvetica, sans-serif;width: 420px;}
table.Flying1 {border-collapse: collapse;margin: 5px;width: 400px;}
table.Flying2 {border-collapse: collapse;margin: 5px;}
#completed td {color: white;font-size:14px;}
#completed td.b1 {color: #8080ff;font-size:14px;}
.Flyingbig {font-size: 24px;}
.Flyingbig2 {font-size: 24px; width: 50px;}
.Flyinggr {color: #00ff00;}
.Flyingyl {color: #ffff00;}
.Flyingbl {color: #8080ff;}
.right {text-align: right;}
.legend0 {background-color: #ff0000; width: 30px; color: black;text-align: center;font-size: 18px;}
.legend1 {background-color: #00ff00; width: 30px; color: black;text-align: center;font-size: 18px;}
.legend2 {background-color: #0000ff; width: 30px; color: black;text-align: center;font-size: 18px;}
.legend3 {background-color: #ffff00; width: 30px; color: black;text-align: center;font-size: 18px;}
.legend4 {background-color: #00ffff; width: 30px; color: black;text-align: center;font-size: 18px;}
.legend5 {background-color: #800000; width: 30px; color: black;text-align: center;font-size: 18px;}
.legend6 {background-color: #008000; width: 30px; color: black;text-align: center;font-size: 18px;}
.legend7 {background-color: #000080; width: 30px; color: black;text-align: center;font-size: 18px;}
.slegend0 {background-color: #ff0000; width: 15px; color: black;text-align: center;font-size: 18px;}
.slegend1 {background-color: #00ff00; width: 15px; color: black;text-align: center;font-size: 18px;}
.slegend2 {background-color: #0000ff; width: 15px; color: black;text-align: center;font-size: 18px;}
.slegend3 {background-color: #ffff00; width: 15px; color: black;text-align: center;font-size: 18px;}
.slegend4 {background-color: #00ffff; width: 15px; color: black;text-align: center;font-size: 18px;}
.slegend5 {background-color: #800000; width: 15px; color: black;text-align: center;font-size: 18px;}
.slegend6 {background-color: #008000; width: 15px; color: black;text-align: center;font-size: 18px;}
.slegend7 {background-color: #000080; width: 15px; color: black;text-align: center;font-size: 18px;}
.slegendblank {width: 15px; color: black;text-align: center;font-size: 18px;}
.space1 {width: 5px;}
.buttonbig {font-size: 28px;width: 36px; height: 36px;}
.buttonbig0 {background-image: url(MapMinus.png); background-repeat: no-repeat;width: 36px; height: 36px;}
.buttonbig1 {background-image: url(MapPlus.png); background-repeat: no-repeat;width: 36px; height: 36px;}
.buttonbig2 {background-image: url(MapLeft.png); background-repeat: no-repeat;width: 36px; height: 36px;}
.buttonbig3 {background-image: url(MapRight.png); background-repeat: no-repeat;width: 36px; height: 36px;}
.buttonbig4 {background-image: url(MapDown.png); background-repeat: no-repeat;width: 36px; height: 36px;}
.buttonbig5 {background-image: url(MapUp.png); background-repeat: no-repeat;width: 36px; height: 36px;}
.buttonbig6 {background-image: url(MapShow.png); background-repeat: no-repeat;width: 36px; height: 36px;}
.showtxt {color: white;}
</style>
<script type="text/javascript"
 src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBACKvSv3Dkose3DNn9rmvfCdJnEUwcGaE">
</script>
<script type="text/javascript"
 src="/mapiconmaker.js">
</script>
<script>
<?PHP
$clockNow = new DateTime('now');
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
$q="SELECT def_launch_lat,def_launch_lon,map_centre_lat,map_centre_lon from organisations where id = ".$org;
$r = mysqli_query($con,$q);
$row = mysqli_fetch_array($r);
$orgLat=$row[0];
$orgLon=$row[1];
$mapLat=$row[2];
$mapLon=$row[3];
?>
var pollcnt=0;
var map;
var flightpaths = [];
var markers = [];
var LaunchLat = <?php echo $orgLat;?>;
var LaunchLon = <?php echo $orgLon;?>;
var MapLat = <?php echo $mapLat;?>;
var MapLon = <?php echo $mapLon;?>;
var toRadians = (3.14159265358979 / 180.0);
var tripColours = ["#ff0000","#00ff00","#0000ff","#ffff00","#00ffff","#800000","#008000","#000080"];
var gOrg = <?php echo $org;?>;
var clok = <?php echo $clockNow->getTimestamp();?>;
var clokoff = 0;
var mapId = 0;
var respMapNode = null;
var mapShowAll = 1;

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
function xmlReplyType(xml)
{
  if (null != xml)
  {
    console.log(xml2Str(xml));
    var node;
    try {node=xml.getElementsByTagName("resp")[0].childNodes;}catch(err){node=null;}
    if (null != node) return "resp";
    try {node=xml.getElementsByTagName("getspotdata")[0].childNodes;}catch(err){node=null;}
    if (null != node) return "getspotdata";
  }
  return "";    	
}

xmlhttp.onreadystatechange = function () 
{
    if (xmlhttp.readyState == 4) 
    {
        console.log("Reply from server");
        var xmlReply = xmlhttp.responseXML;
        var strXMLReplyType = xmlReplyType(xmlReply);
        if (strXMLReplyType == "resp")    
        {
         var node;
         //try {
          node=xmlReply.getElementsByTagName("resp")[0].childNodes;
          console.log(" Have node: " + node.nodeName);
          respMapNode = null;
          for (i1=0;i1<node.length;i1++)
          {
   	   if (node[i1].nodeName == "map")
   	   {
              respMapNode = node[i1];
           }
          }       

          for (i1=0;i1<node.length;i1++)
          {
 	   console.log("  sub node: " + node[i1].nodeName);
           if (node[i1].nodeName == "duties")
     	   {
    	     BuildDuties(node[i1]);
    	   }
    	   if (node[i1].nodeName == "flights")
    	   {
	     BuildMap(node[i1],xmlReply);
             BuildFlying(node[i1]);
             BuildCompleted(node[i1]);
  	   }

    	  }
    	  //}
   	  //catch(err)
    	  //{
    	  //node=null;
    	  //console.log("Catch error");            
   	  //}
        }
    }
}
function GetUpd()
{
 var v="todayxml.php?org=" + gOrg;
 console.log("Send Request for Todays");
 xmlhttp.open("GET", v, true);
 xmlhttp.send();
}
function GetSpotData(org)
{
 var v="GetSpotData.php?org=" + org;
 console.log("Send Request for Spots");
 xmlhttp.open("GET", v, true);
 xmlhttp.send();
}
function colourPick(n)
{
 return tripColours[n % tripColours.length];
}
function legendstylePick(n)
{
   var ret = "";
   ret = "legend" + String(n % tripColours.length);
   return ret;
}
function mod(y, x)
{
        return  (y - x*Math.floor(y/x));
}

function DistKM(lat,long,dlat,dlong)
{
  var latFrom = lat * toRadians;
  var longFrom = long * toRadians;
  var latTo = dlat * toRadians;
  var longTo = dlong * toRadians;
  var theta;
  theta = Math.sin(latFrom) * Math.sin(latTo) + (Math.cos(latFrom) * Math.cos(latTo) * Math.cos(longFrom-longTo)); 
  return (6378.15 * Math.acos(theta));
}
function Azimuth(lat,long,dlat,dlong)
{
   var lat1 = lat * toRadians;
   var lon1 = -long * toRadians;
   var lat2 = dlat * toRadians;
   var lon2 = -dlong * toRadians;
   var d1 = mod(Math.atan2(Math.sin(lon1-lon2)*Math.cos(lat2),
           	    Math.cos(lat1)*Math.sin(lat2)-Math.sin(lat1)*Math.cos(lat2)*Math.cos(lon1-lon2)), 2*3.14159265358979);
   return (d1 * (180.0/3.14159265358979));
}

function UpdateFlightTimes()
{
   var i;
   for (i=0;i < 40;i++)
   {
      var a = document.getElementById('timeId' + i);
      if (null != a)
      {
          var strt = a.getAttribute('tvalue');  
	  num = parseInt(strt) * 1000;
     
          var dtNow = new Date();
          var e = (dtNow.getTime() - num) / 1000;
          e = e - clokoff;
          var hrs = pad(Math.floor( e / 3600),2);
          var mins = pad(Math.floor((e / 60) % 60),2);     
          var secs = pad(Math.floor( e % 60),2);
          a.innerHTML = hrs + ":" + mins + ":" + secs;
 
      }
   }
}

function UpdateAgeTimes()
{
   var i;
   for (i=0;i < 40;i++)
   {
      var a = document.getElementById('ageId' + i);
      if (null != a)
      {
         var strt = a.getAttribute('tvalue');  
     
	 var num = parseInt(strt) * 1000;   
         var dtNow = new Date();
         var e = (dtNow.getTime() - num) / 1000;
         e = e - clokoff;
         var strAge = '';
         if (e < 60)
         {
             strAge = String(Math.floor(e)) + "s";
         }
         else
             strAge = String(Math.floor(e/60)) + "m";
         a.innerHTML = strAge;
 
      }
   }
}

function poll()
{
  pollcnt++;
  UpdateFlightTimes();
  UpdateAgeTimes();
  if ((pollcnt % 30) == 0)
  {
      GetUpd();
  }
  if ((pollcnt % 70) == 0)
  {
     GetSpotData(gOrg);
     pollcnt = 0;
  }
}
function Start() 
{
 //Calculate any timeoffsets
 var dtNow = new Date();
 var e = dtNow.getTime() / 1000;
 var off = Math.floor(((e-clok) / 1800) + 0.49);  //Rounded to the nearest half hour
 if (off >= 1)
    clokoff = off * 1800;


 var wid1 = (window.innerWidth - 15).toString() + "px";
 var wid2 = (window.innerWidth - 500).toString() + "px";
 var h1 = (window.innerHeight - 105).toString() + "px";
 document.getElementById("container").style.width = wid1;
 document.getElementById("areamap").style.width = wid2;
 document.getElementById("areamap").style.height = h1;

 setInterval(poll,1000);
 GetUpd();

 var mapOptions = {
          center: { lat: <?php echo $mapLat;?>, lng: <?php echo $mapLon;?>},
          zoom: 11,
          zoomControl: false,
          rotateControl: false,
	  panControl: false,
          streetViewControl: false,
          scaleControl: false,
          mapTypeId: google.maps.MapTypeId.HYBRID
        };
 map = new google.maps.Map(document.getElementById('map-canvas'),mapOptions);
}
google.maps.event.addDomListener(window, 'load', Start);
function pad(num, size) {
    var s = num+"";
    while (s.length < size) s = "0" + s;
    return s;
}

function getNodeValue(n,tag)
{
 var v='';
 var t;
 try {
     t=n.getElementsByTagName(tag)[0].childNodes;
     if (t.length > 0)
     {
       v = t[0].nodeValue;
     }
 }
 catch (err)
 {
 }
 
 return v;
}
function removeChildNodes(n)
{
  var cn=n.childNodes;
  while (cn.length > 0)
  {
    n.removeChild(cn[0]);
    cn=n.childNodes;
  }
}


function toggleMapShow()
{
  if (mapShowAll == 1)
  {
     document.getElementById('mapshow').innerHTML = "DISPLAYING FLYING ONLY";  
     mapShowAll = 0;
  }
  else
  {
     document.getElementById('mapshow').innerHTML = "DISPLAYING ALL FLIGHTS";  
     mapShowAll = 1;
  }
  GetUpd();
}

function BuildDuties(n)
{
}

function BuildFlying(n)
{
  var timeId=0;
  mapId=0;
  console.log("Build Flying");
  var a = document.getElementById('flying');
  removeChildNodes(a);
  console.log(" n node name="+n.nodeName);
  var f1 = n.childNodes;
  if (null !=f1)
  {
   for (x=0;x<f1.length;x++)
   {
     var strLanded = getNodeValue(f1[x],"landed");
     if (parseInt(strLanded) == 0)
     {
       var strGlider = getNodeValue(f1[x],"glider");
       if (strGlider.length == 0)
         strGlider = "???";
       var strName1 = getNodeValue(f1[x],"name1");
       var strName2 = getNodeValue(f1[x],"name2");
       var strStart = getNodeValue(f1[x],"start");
       var strLegend = getNodeValue(f1[x],"legend");
       console.log("Legend = " + strLegend);       
       var num=0;
       num = parseInt(strStart) * 1000;
       console.log("Num = " + num);
       var dtNow = new Date();
       var e = (dtNow.getTime() - num) / 1000;
       e = e -clokoff;

       var hrs = pad(Math.floor( e / 3600),2);
       var mins = pad(Math.floor((e / 60) % 60),2);     
       var secs = pad(Math.floor( e % 60),2);

       v = document.createElement('div');
       v.setAttribute('class','FlyingDiv');
       w = document.createElement('table');
       w.setAttribute('class','Flying1');
       var s="<tr><td class=\"Flyingbig2 Flyingbl\">";
       s += strGlider;
       s += "</td>";
       if (strName1.length > 0)
       	s += "<td>" + strName1 + "</td>";
       if (strName2.length > 0)
     	  s += "<td>" + strName2 + "</td>";
       s += "<td class=\"Flyingbig right Flyinggr\" id = \"timeId" + timeId +  "\" tvalue=\""+strStart+"\">" + hrs + ":" + mins + ":" + secs  +"</td></tr>";
       w.innerHTML = s;
       v.appendChild(w);


       if(f1[x].getElementsByTagName("points").length > 0)
       {
         g=f1[x].getElementsByTagName("points")[0].childNodes;
         if (g.length > 0)
         {
           var idx = g.length - 1;
           if (g[idx].nodeName == "p")
           {
             w = document.createElement('table');
             w.setAttribute('class','Flying2');
             var tm = g[idx].getElementsByTagName("t")[0].childNodes[0].nodeValue;
             var lat = g[idx].getElementsByTagName("lt")[0].childNodes[0].nodeValue;
             var lon = g[idx].getElementsByTagName("ln")[0].childNodes[0].nodeValue;
             var alt = g[idx].getElementsByTagName("al")[0].childNodes[0].nodeValue;
                 
	     var num = parseInt(tm) * 1000;   
             var dtNow = new Date();
             var e = (dtNow.getTime() - num) / 1000;
             e = e -clokoff;
             var strAge = '';
             if (e < 60)
             {
                 strAge = String(Math.floor(e)) + "s";
             }
             else
                 strAge = String(Math.floor(e/60)) + "m";
         
             if (parseInt(alt) < 0)
                 alt = "N/A";
             else
                alt = String(Math.floor(alt * 3.2808399)) + "ft";
             dist = DistKM(LaunchLat,LaunchLon,lat,lon);
             if (dist < 10.0)
                 dist = Math.floor(dist * 10.0) / 10.0;
             else
                 dist = Math.floor(dist);

             var azi = Azimuth(LaunchLat,LaunchLon,lat,lon);
	     azi = Math.floor(azi + 0.5);
             var mapId = parseInt(strLegend);
             var seqid = mapId+1;
             strAgeId = "ageId" + String(mapId);
             s = "<tr><td rowspan=\"2\" class=\""+legendstylePick(mapId)+"\">"+seqid+"</td><td class=\"space1\"></td><td><span>AGE: </span><span id=\""+strAgeId+"\" class='Flyingyl' tvalue=\""+parseInt(tm)+"\">"+strAge+"</span><span> ALT: </span><span class='Flyingyl'>"+alt+"</span><span> DIST:</span><span class='Flyingyl'>"+dist+"km</span><span> VECT:</span><span class='Flyingyl'>"+azi+"&deg</span></td></tr><tr><td class=\"space1\"></td><td><span class=\"Flyingyl\">"+lat+","+lon+"</span></td></tr>";
             w.innerHTML = s;
             v.appendChild(w);
             
            }
          }
       }
       timeId++;
       w.innerHTML = s;
       v.appendChild(w);
       a.appendChild(v);
     }
    
   }
  }
}

function BuildCompleted(n)
{
  var timeId=0;
  console.log("Build Completed");
  var a = document.getElementById('completed');
  removeChildNodes(a);
  console.log(" n node name="+n.nodeName);
  w = document.createElement('table');
  var v ='';
  var f1 = n.childNodes;
  if (null !=f1)
  {
   for (x=0;x<f1.length;x++)
   {
     var strLanded = getNodeValue(f1[x],"landed");
     if (parseInt(strLanded) == 1)
     {
       var strSeq = getNodeValue(f1[x],"seq");
       var strGlider = getNodeValue(f1[x],"glider");
       if (strGlider.length == 0)
           strGlider = "???";
       var strName1 = getNodeValue(f1[x],"name1");
       var strName2 = getNodeValue(f1[x],"name2");
       var strLegend = getNodeValue(f1[x],"legend");
       console.log("Legend = " + strLegend);
       var dur = getNodeValue(f1[x],"dur");
       e = parseInt(dur);
     
       var hrs = pad(Math.floor( e / 3600),2);
       var mins = pad(Math.floor((e / 60) % 60),2);     
       var secs = pad(Math.floor( e % 60),2);
       var strtm = hrs + ":" + mins;
       var weHavePoints = 0;
       if(f1[x].getElementsByTagName("points").length > 0)
       {
         g=f1[x].getElementsByTagName("points")[0].childNodes;
         if (g.length > 0)
         {
           if (g[g.length-1].nodeName == "p")
           {
              weHavePoints = 1;
           }
         }
       }
       if (weHavePoints == 1 && mapShowAll)
       {
         var mapId = parseInt(strLegend);
         var seqid = mapId+1;
         v += "<tr><td class=\"s"+legendstylePick(mapId)+"\">"+seqid+"</td>><td class =\"b1\">"+strGlider+"</td><td>"+strName1+"</td><td>"+strName2+"</td><td>"+strtm+"</td></tr>";
       
       }
       else
         v += "<tr><td class='slegendblank'></td><td class =\"b1\">"+strGlider+"</td><td>"+strName1+"</td><td>"+strName2+"</td><td>"+strtm+"</td></tr>";
    
     }
     if (v.length > 0)
     {
       w.innerHTML = v;
       a.appendChild(w);
     }
   }
 }
}
function BuildMap(n,doc)
{
  var tripnum = 0;
  console.log("Build Maps");
  //remove all existing lines.  
  for (i=0;i<flightpaths.length;i++)
     flightpaths[i].setMap(null);
  while (flightpaths.length > 0)
    flightpaths.pop();
  for (i=0;i<markers.length;i++)
     markers[i].setMap(null);
  while (markers.length > 0)
    markers.pop();

  var t = n.childNodes;
  if (null !=t)
  {
   for (x=0;x<t.length;x++)
   {
    if (t[x].nodeName == "flight")
    {
      
      var trip = t[x];
      var Coordinates = [];
      if (trip.getElementsByTagName("glider").length > 0)
      {
        var landed = trip.getElementsByTagName("landed")[0].childNodes[0].nodeValue;
        if (parseInt(landed) == 0 || mapShowAll == 1)
        {
          var tripname = getNodeValue(trip,"glider");
          if (trip.getElementsByTagName("points").length > 0)
          {
           var points=trip.getElementsByTagName("points")[0].childNodes;
           for (y=0;y<points.length;y++)
           {
             var lt = points[y].getElementsByTagName("lt")[0].childNodes[0].nodeValue;
             var ln = points[y].getElementsByTagName("ln")[0].childNodes[0].nodeValue;
             Coordinates.push(new google.maps.LatLng(lt, ln));
           }
           if (points.length > 0)
           {
              var flightPath = new google.maps.Polyline({
    		path: Coordinates,
    		geodesic: true,
    		strokeColor: colourPick(tripnum),
    		strokeOpacity: 1.0,
    		strokeWeight: 2
  		});  
	     flightpaths.push(flightPath);
             flightPath.setMap(map);

             //Set marker for he last
             //Create a marker
             var iconOptions = {};
             var strlabel = String(tripnum+1);
	     iconOptions.primaryColor = colourPick(tripnum);
	     iconOptions.strokeColor = "#000000";
             iconOptions.label = strlabel;
             iconOptions.labelColor = "#000000";
             iconOptions.addStar = false;
             iconOptions.starPrimaryColor = "#FFFF00";
             iconOptions.starStrokeColor = "#0000FF";
             var iconF = MapIconMaker.createLabeledMarkerIcon(iconOptions);
           
             var marker = new google.maps.Marker({
              position: Coordinates[Coordinates.length-1],
              icon: iconF.icon,
              title: tripname
	         });
             markers.push(marker);
             marker.setMap(map);
             var leg = doc.createElement("legend");
             leg.appendChild(doc.createTextNode(tripnum));
             trip.appendChild(leg);
	     tripnum++;
           }
        } 
      }
     }
    }
   }
  }
}
</script>
</head>
<body>
<div id='container'>
<div id='areamap'>
<div id="map-canvas"></div>
<div id='bttns'>
<button onclick='map.setZoom(map.getZoom()-1)' class='buttonbig0'> </button>
<button onclick='map.setZoom(map.getZoom()+1)' class='buttonbig1'> </button>
<button onclick='map.panBy(-10, 0)' class='buttonbig2'></button>
<button onclick='map.panBy(10, 0)' class='buttonbig3'></button>
<button onclick='map.panBy(0, 10)' class='buttonbig4'></button>
<button onclick='map.panBy(0, -10)' class='buttonbig5'></button>
<button onclick='toggleMapShow()' class='buttonbig6'></button>
<span class='showtxt' id='mapshow'>DISPLAYING ALL FLIGHTS</span>
</div>
</div>
<div id='area1'>
<div id='duties'></div>
<h1>FLYING NOW</h1>
<div id='flying'></div>
<h1>COMPLETED TODAY</h1>
<div id='completed'></div>
</div>
</div>
</body>
</html>
