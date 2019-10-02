<?php

?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript"src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBACKvSv3Dkose3DNn9rmvfCdJnEUwcGaE"></script>
<script type="text/javascript" src="https://static.devt.nz/devtscript.js?api_key=2fb411936371c7e2d5381e0aec347e36"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
var g_bDataLoaded = false;
var g_screenwidth = 900;
var g_screenheight = 600;
var map;
var g_mapType = 1;
var g_tracks;
var g_tracks2 = [];
var runway1 = [];
var runway2 = [];
var g_minlat = 0.0;
var g_maxlat = 0.0;
var g_minlng = 0.0;
var g_maxlng = 0.0;
var g_screen;
var g_base;
var g_showpoints = false;
var g_show2DInsert = true;
var g_zoom2d = 4;
var FEET_TO_METRES = 0.3048;
var g_height3d = 1500 * FEET_TO_METRES;
var vCamera = new ThreeD.Matrix3D();
var veye = new ThreeD.Vector3D(0.0,1500*FEET_TO_METRES,-200.0);
var vat = new ThreeD.Vector3D(0.0,1500*FEET_TO_METRES,0.0);
var vMoveDir = new ThreeD.Vector3D(0.0,0.0,1.0);
var vViewDir = new ThreeD.Vector3D(0.0,0.0,1.0);
var vmax = new ThreeD.Vector3D(0.0,0.0,0.0);
var vmin = new ThreeD.Vector3D(0.0,0.0,0.0);
var g_interval;
var g_2dSize = 200;

//Build Runway
var g_runway1 = [{lat: -41.108259,lng: 175.492762, alt: 40.0},{lat: -41.109087, lng: 175.493745, alt: 40.0},{lat: -41.097663,lng: 175.511714,alt: 40.0},{lat: -41.097179,lng: 175.511203,alt: 40.0},{lat: -41.108259,lng: 175.492762, alt: 40.0}];
var g_runway2 = [{lat: -41.096367, lng: 175.488748, alt: 40.0},{lat: -41.095841, lng: 175.489622, alt: 40.0},{lat: -41.105211, lng: 175.501318,alt: 40.0},{lat: -41.105536,lng: 175.500868,alt: 40.0},{lat: -41.096367, lng: 175.488748, alt: 40.0}];


devt.api = new devt.apiJSON('glidingops.com','api/v1/json','1234567890123456',true);
devt.api.parseReply = function(r) {
    if (r['meta'] ['status'] == 'OK' ) {
        var d = r['data'];
        switch (r['meta'] ['req']) {
        case 'flightdata':
            g_bDataLoaded = true;
            g_tracks = d['tracks'];
            prep3DTracks();
            draw3d();
            buildmap(g_tracks);
            drawChart(g_tracks);
            break;
        }
    }
};
devt.getFlight = function(f) {
    devt.api.queueReq('GET','flightdata/' + f,null);    
};

function findminmax() {
    g_minlat = 0.0;
    g_maxlat = 0.0;
    g_minlng = 0.0;
    g_maxlng = 0.0;
    for(var i =0; i < g_tracks.length;i++) {
        var pos = g_tracks[i];
        if (i != 0) {
            if (parseFloat(pos['lat']) < g_minlat) g_minlat = parseFloat(pos['lat']);
            if (parseFloat(pos['lat']) > g_maxlat) g_maxlat = parseFloat(pos['lat']);
            if (parseFloat(pos['lng']) < g_minlng) g_minlng = parseFloat(pos['lng']);
            if (parseFloat(pos['lng']) > g_maxlng) g_maxlng = parseFloat(pos['lng']);
        }
        else
        {
            g_minlat = parseFloat(pos['lat']);
            g_maxlat = parseFloat(pos['lat']);
            g_minlng = parseFloat(pos['lng']);
            g_maxlng = parseFloat(pos['lng']);
        }
    }
    //Now take into consideration the runways
    for (i = 0 ; i < g_runway1.length;i++) {
        if (parseFloat(g_runway1[i].lat) < g_minlat) g_minlat = parseFloat(g_runway1[i].lat);
        if (parseFloat(g_runway1[i].lat) > g_maxlat) g_maxlat = parseFloat(g_runway1[i].lat);
        if (parseFloat(g_runway1[i].lng) < g_minlng) g_minlng = parseFloat(g_runway1[i].lng);
        if (parseFloat(g_runway1[i].lng) > g_maxlng) g_maxlng = parseFloat(g_runway1[i].lng);
    }
    for (i = 0 ; i < g_runway2.length;i++) {
        if (parseFloat(g_runway2[i].lat) < g_minlat) g_minlat = parseFloat(g_runway2[i].lat);
        if (parseFloat(g_runway2[i].lat) > g_maxlat) g_maxlat = parseFloat(g_runway2[i].lat);
        if (parseFloat(g_runway2[i].lng) < g_minlng) g_minlng = parseFloat(g_runway2[i].lng);
        if (parseFloat(g_runway2[i].lng) > g_maxlng) g_maxlng = parseFloat(g_runway2[i].lng);
    }
}

function convertToFlat(lat,lng,alt) {
    var cs = Math.cos((parseFloat(lat) * 3.1415926) / 180.0);
    return new ThreeD.Vector3D( parseFloat(lng - g_base['lng']) * cs * 111320.0, parseFloat(alt), parseFloat(lat - g_base['lat']) * 110574.0);
}

function prep3DTracks() {
    findminmax();
    var baselng = ((parseFloat(g_maxlng)-parseFloat(g_minlng))/2.0)+parseFloat(g_minlng);
    var base = {lat:parseFloat(g_minlat),alt:0,lng:parseFloat(baselng)};
    g_base = base;
    g_tracks2 = [];
    
    for (var c = 0;c < g_tracks.length;c++)
    {
        var p = g_tracks[c];
        var cs = Math.cos((parseFloat(p['lat']) * 3.1415926) / 180.0);
        g_tracks2[c] = convertToFlat(p['lat'],p['lng'],p['alt']);
        if (c == 0)
        {
            vmin.x = g_tracks2[c].x;
            vmin.y = g_tracks2[c].y;
            vmin.z = g_tracks2[c].z;
            vmax.x = g_tracks2[c].x;
            vmax.y = g_tracks2[c].y;
            vmax.z = g_tracks2[c].z;
        }
        else
        {
            if (g_tracks2[c].x < vmin.x) vmin.x = g_tracks2[c].x;
            if (g_tracks2[c].y < vmin.y) vmin.y = g_tracks2[c].y;
            if (g_tracks2[c].z < vmin.z) vmin.z = g_tracks2[c].z;
            if (g_tracks2[c].x > vmax.x) vmax.x = g_tracks2[c].x;
            if (g_tracks2[c].y > vmax.y) vmax.y = g_tracks2[c].y;
            if (g_tracks2[c].z > vmax.z) vmax.z = g_tracks2[c].z;
            
            g_tracks[c].rate = (parseFloat(g_tracks[c].alt) - parseFloat(g_tracks[c-1].alt)) / parseFloat( (parseInt(g_tracks[c].time) - parseInt(g_tracks[c-1].time)) ) ;
            g_tracks[c].colour = rateColour(g_tracks[c].rate);
        }
    }
    
    //Build runway
    for (var i = 0; i < 5;i++) {
        runway1[i] = convertToFlat(g_runway1[i].lat, g_runway1[i].lng,g_runway1[i].alt);
        runway2[i] = convertToFlat(g_runway2[i].lat, g_runway2[i].lng,g_runway2[i].alt);
    }  
    g_screen = new ThreeD.Screen(50.0,g_screenwidth/-2.0,g_screenheight/-2.0);
    devt.ge('detail1').innerHTML = 'Max Height: ' + parseInt(vmax.y / FEET_TO_METRES); 
}

function buildmap(tracks) {
    if (!g_bDataLoaded)
        return;
    var c;    
    var mapOptions = {
          center: { lat: -41.104, lng: 175.5},
          zoom: 10
        };
    map = new google.maps.Map(document.getElementById('map'),
            mapOptions);
            
    //Loop here doing the lines
    for (var i=1; i < tracks.length;i++) {
        var coord = [new google.maps.LatLng(tracks[i-1] ['lat'], tracks[i-1] ['lng']),new google.maps.LatLng(tracks[i] ['lat'], tracks[i] ['lng'])];
        if (parseInt(tracks[i] ['alt']) > parseInt(tracks[i-1] ['alt']) )
            c = '#00FF00';
        else
            c = '#FF0000';
        var l = new google.maps.Polyline({
    		            path: coord,
    		            geodesic: true,
    		            strokeColor: c,
    		            strokeOpacity: 1.0,
    		            strokeWeight: 2
  		            });
        l.setMap(map);
          
    }
    
    var runwayx = new google.maps.Polygon({
        paths: g_runway1,
        strokeColor: '#00ee00',
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: '#008000',
        fillOpacity: 0.35
      });
   runwayx.setMap(map);
   var runwayy = new google.maps.Polygon({
        paths: g_runway2,
        strokeColor: '#00ee00',
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: '#008000',
        fillOpacity: 0.35
      });
   runwayy.setMap(map);
}

function eraseCanvas(id) {
    var n = devt.ge(id);
    ctx = n.getContext("2d");
    ctx.fillStyle = "#000000";
    ctx.fillRect(0, 0, n.width, n.height);
}

function erase(ctx) {
    ctx.fillStyle = "#000000";
    ctx.fillRect(0, 0, g_screenwidth, g_screenheight);
}

function erase2d(ctx) {
    ctx.fillStyle = "#000000";
    ctx.fillRect(g_screenwidth-g_2dSize, g_screenheight-g_2dSize, g_screenwidth, g_screenheight);
}


function prepline(v1,v2) {
    var sout = [];
    //if (v1.z > 0 && v2.z > 0) {
       // sout[0] = g_screen.RealToScreen(v1);
        //sout[1] = g_screen.RealToScreen(v2);
        //return sout;
    //}
    if (v1.z < 0 && v2.z < 0)
        return null;
    else {
        var dv = g_screen.RealToScreenLine(v1,v2);
        sout[0] = dv.v1;
        sout[1] = dv.v2;
        return sout;
    }
    
    
    
    
    return null;
}

function rateColour(r) {
    var rd,gr;
    var max = 5.0;
    var v = r;
    if (v >=0) {
        if (v > max) v = max;
        rd = 255 * ((max - v) / max);
        gr = 255;
    }
    if (v <= 0) {
        if (v < -(max)) v = 0-max;
        gr = 255 * ((max + v) / max);
        rd = 255;
    }
    rd = parseInt(rd);
    gr = parseInt(gr);
    if (rd > 255) rd = 255;
    if (gr > 255) gr = 255;
    if (rd < 0) rd = 0;
    if (gr < 0) gr = 0;
    
    return "#" + devt.pad(rd.toString(16),2) + devt.pad(gr.toString(16),2) + "00"; 
}

function plotPoint(ctx,v,c) {
    var s = g_screen.RealToScreen(v);
    if (s.z >= 0)
    {
        ctx.beginPath();
        ctx.arc(s.x, g_screenheight - s.y, 2, 0, 2 * Math.PI);
        ctx.fillStyle = c;
        ctx.fill();
    }
}

function draw3d() {
    if (!g_bDataLoaded)
        return;

    var bone = false;
    var c = devt.ge('map3d');
    var ctx = c.getContext("2d");
    erase(ctx);
    
    //Set view direction
    vat.x = veye.x + vViewDir.x;
    vat.y = veye.y + vViewDir.y;
    vat.z = veye.z + vViewDir.z;
    vCamera.setView(veye,vat,new ThreeD.Vector3D(0.0,1.0,0.0));
    g_height3d
    var v2 = [];
    
    
    //Draw runway
    ctx.strokeStyle = "#004000";
    for (var c = 0;c < runway1.length;c++)
    {
        v2[c] = runway1[c];
    }
    vCamera.transformarray(v2);
    for (var c = 0;c < (runway1.length-1);c++)
    {
        s = prepline(v2[c],v2[c+1]);
        if (s)
        {
            ctx.beginPath();
            ctx.moveTo(s[0].x,g_screenheight - s[0].y);
            ctx.lineTo(s[1].x,g_screenheight - s[1].y);
            ctx.strokeStyle = "#004000";
            ctx.stroke();
        }
    }
    v2 = [];
    plotPoint(ctx,v2[0],'#0000ee');
    
    for (var c = 0;c < runway2.length;c++)
    {
        v2[c] = runway2[c];
    }
    vCamera.transformarray(v2);
    for (var c = 0;c < (runway2.length-1);c++)
    {
        s = prepline(v2[c],v2[c+1]);
        if (s)
        {
                ctx.beginPath();
                ctx.moveTo(s[0].x,g_screenheight - s[0].y);
                ctx.lineTo(s[1].x,g_screenheight - s[1].y);
                ctx.strokeStyle = "#004000";
                ctx.stroke();
        }
    }
    v2 = [];
    //Make a copy of the tracks
    for (var c = 0;c < g_tracks2.length;c++)
    {
        v2[c] = g_tracks2[c];
    }
    
    //Transorm the points to a view.
    vCamera.transformarray(v2);
    
    for (var c = 0;c < (v2.length-1);c++)
    {
        s = prepline(v2[c],v2[c+1]);  
        var l = veye.lenthto(g_tracks2[c+1]);
        ctx.strokeStyle = g_tracks[c+1].colour;    
        if (v2[c+1].z > 2000.0)
            ctx.lineWidth = 1;
        else
            ctx.lineWidth = parseInt(((2000.0 - l) / 500.0) + 1.0);
        if (s)
        {
                ctx.beginPath();
                ctx.moveTo(s[0].x,g_screenheight - s[0].y);
                ctx.lineTo(s[1].x,g_screenheight - s[1].y);
                ctx.stroke();
        }
       
    }
    
    //Points
    if (g_showpoints) {
        for (var c = 0;c < v2.length;c++)
        {
            var s = g_screen.RealToScreen(v2[c]);
            if (s.z >= 0)
            {
                ctx.beginPath();
                ctx.arc(s.x, g_screenheight - s.y, 2, 0, 2 * Math.PI);
                if (c == 0)
                    ctx.fillStyle = '#00ff00';
                else
                if (c == v2.length-1)
                    ctx.fillStyle = '#ff0000';
                else
                    ctx.fillStyle = '#0000e0';
                ctx.fill();
            }
        }
    }
    
    if (g_show2DInsert) {
        erase2d(ctx);
        var xoffset = (g_screenwidth-g_2dSize);
        var yoffset = g_screenheight-g_2dSize;
        
        //Draw Border
        ctx.beginPath();
        ctx.moveTo(xoffset,yoffset);
        ctx.lineTo(xoffset+g_2dSize,yoffset);
        ctx.lineTo(xoffset+g_2dSize,yoffset+g_2dSize);
        ctx.lineTo(xoffset,yoffset+g_2dSize);
        ctx.lineTo(xoffset,yoffset);
        ctx.stroke();
        
        xoffset += 100;
        
        
        var scale = g_zoom2d / 100.0;
        for (var i = 0; i < g_tracks2.length; i++) {
            var x = (g_tracks2[i].x * scale) + xoffset;
            var y = g_screenheight - ((g_tracks2[i].z + 300.0) * scale);
            if (x > (g_screenwidth-g_2dSize) && y > (g_screenheight-g_2dSize) ) {
                ctx.beginPath();
                ctx.arc(x,y, 2, 0, 2 * Math.PI);
                ctx.fillStyle = "#ffff00";
                ctx.fill(); 
            }
        }
        //Where is the camera
        var x = (veye.x * scale) + xoffset;
        var y = g_screenheight - ((veye.z + 300.0) * scale);
        if (x > (g_screenwidth-g_2dSize) && y > (g_screenheight-g_2dSize) ) {
            ctx.beginPath();
            ctx.arc(x,y, 2, 0, 2 * Math.PI);
            ctx.fillStyle = "#0000ff";
            ctx.fill(); 
        }
    }
}

function maptypechange(n) {
    var maptype = parseInt(n.value);
    if (n.checked) {
        g_mapType = maptype;
        switch (maptype) {
        case 1:
            devt.ge('map').style.display='block';
            devt.ge('map3d').style.display='none';
            devt.ge('divani').style.display='none';
            devt.ge('heightchart').style.display='none';
            break;
        case 2:
            devt.ge('map').style.display='none';
            devt.ge('map3d').style.display='block';
            devt.ge('divani').style.display='block';
            devt.ge('heightchart').style.display='none';
            break;
        case 3:
            devt.ge('map').style.display='none';
            devt.ge('map3d').style.display='none';
            devt.ge('divani').style.display='none';
            devt.ge('heightchart').style.display='block';
            drawChart(g_tracks);
            break;
        }        
        console.log('Map type change value' + maptype);
    }
}

function optionchange(n) {
    var option = parseInt(n.getAttribute('typenum')); 
    switch (option) {
    case 1:
        g_showpoints = n.checked;
        draw3d();
        break;
    case 2:
        g_show2DInsert = n.checked;
        draw3d();
        break;
    }
}

function zoomfactor(n) {
    g_zoom2d = parseFloat(n.value);
    draw3d();
}

function animateFwd() {
    var movemetres = 5.0;
    veye.x = veye.x + (vMoveDir.x * movemetres);
    veye.z = veye.z + (vMoveDir.z * movemetres);
    vat.x = vat.x + (vMoveDir.x * movemetres);
    vat.z = vat.z + (vMoveDir.z * movemetres);
        
    if (veye.z > vmax.z + 1000)  // Stop when 1 km past
    {
        clearInterval(g_interval);
        var n = devt.ge('anibut1');
        n.setAttribute('on','0');
        n.innerHTML = 'ANIMATE';
        n = devt.ge('anibut2');
        n.setAttribute('on','0');
        n.innerHTML = 'ANIMATE';
    }
    
    devt.ge('eyex').value = veye.x;
    devt.ge('eyey').value = veye.y;
    devt.ge('eyez').value = veye.z;
    
    draw3d();
    
}

function startAnimate(n) {
    var state = parseInt(n.getAttribute('on'));
    if (state == 0) {
        n.setAttribute('on','1');
        n.innerHTML = 'PAUSE';
        g_interval = setInterval(animateFwd, 50);
    }
    else 
    if (state == 1) { 
        n.setAttribute('on','2');
        n.innerHTML = 'RESUME';
        clearInterval(g_interval);
    }
    else
    if (state == 2) { //Pressed for resume
        n.setAttribute('on','1');
        n.innerHTML = 'PAUSE';
        g_interval = setInterval(animateFwd, 100);    
    }
    
}

function canvasmouse(e) {
    var n;  
    var x;
    var y;
    
    if (g_mapType != 2)
        return;
    //Check 2d map
    n = devt.ge('map3d');
    x = e.pageX - n.offsetLeft;
    y = e.pageY - n.offsetTop;
    
    if (x >= (g_screenwidth-g_2dSize) && x < g_screenwidth && y >= (g_screenheight - g_2dSize) && y < g_screenheight) {
        //Relcoate the eye
        var xoffset = (g_screenwidth-g_2dSize) + 100;
        var yoffset = g_screenheight-g_2dSize;
        var scale = g_zoom2d / 100.0;
        var ex = (x - xoffset) / scale;
        var ey = ((y-g_screenheight) / -(scale)) - 300.0;
        veye.x = ex;
        veye.z = ey;
        draw3d();
    }
    
    //Camera Direction
    n = devt.ge('dir1');
    x = e.pageX - n.offsetLeft;
    y = e.pageY - n.offsetTop;
    
    if (x >= 0 && x < 100 && y >= 0 && y < 100) {
        console.log('Hit dir wheel');
        vMoveDir = new ThreeD.Vector3D(x-50.0,0,(100.0-y)-50.0);
        vMoveDir.normalize();
        vViewDir.x = vMoveDir.x
        vViewDir.y = vMoveDir.y
        vViewDir.z = vMoveDir.z
        initAniCanvas();
        draw3d();
    }
    
    //View Direction
    n = devt.ge('dir2');
    x = e.pageX - n.offsetLeft;
    y = e.pageY - n.offsetTop;
    
    if (x >= 0 && x < 100 && y >= 0 && y < 100) {
        console.log('Hit dir wheel');
        vViewDir = new ThreeD.Vector3D(x-50.0,0,(100.0-y)-50.0);
        vViewDir.normalize();
        initAniCanvas();
        draw3d();
    }
    
}

function camheight(n) {
    var h = parseInt(n.value) * FEET_TO_METRES;
    veye.y = h;
    vat.y = h;
    g_height3d = h;
    draw3d();
}

function initAniCanvas() {
    var c; 
    var ctx;
    eraseCanvas('dir1');
    c = devt.ge('dir1');
    ctx = c.getContext("2d");
    ctx.strokeStyle = "#808080";
    ctx.beginPath();
    ctx.arc(50, 50, 48, 0, 2 * Math.PI);
    ctx.stroke();
    
    ctx.strokeStyle = "#ee0000";
    ctx.beginPath();
    ctx.moveTo(50,50);
    ctx.lineTo(50+(vMoveDir.x * 40), 100-(50+vMoveDir.z * 40));
    ctx.stroke();
    
    eraseCanvas('dir2');
    c = devt.ge('dir2');
    ctx = c.getContext("2d");
    ctx.strokeStyle = "#808080";
    ctx.beginPath();
    ctx.arc(50, 50, 48, 0, 2 * Math.PI);
    ctx.stroke();
    
    ctx.strokeStyle = "#ee0000";
    ctx.beginPath();
    ctx.moveTo(50,50);
    ctx.lineTo(50+(vViewDir.x * 40), 100-(50+vViewDir.z * 40));
    ctx.stroke();
       
}

function drawChart(tracks) {
    
    //Create the data
    var d = [];
    d.push(['Time','Height']);
    
    for (var i = 0;  i < tracks.length; i++) {
        date = new Date(parseInt(tracks[i].time) * 1000);
        var h = parseFloat(date.getHours());
        var m = parseFloat(date.getMinutes()) / 60.0;
        var s = parseFloat(date.getSeconds()) / 3600.0;
        d.push([(h+m+s),tracks[i].alt / FEET_TO_METRES ]);
    }
    
    var data = google.visualization.arrayToDataTable(d);

    var options = {
        title: 'Flight Height Map',
        curveType: 'function',
        height: 600,
        width: 1000,
        backgroundColor: '#000000',
        titleTextStyle: {color: '#b0b0ff'},
        vAxis: {textStyle: {color: '#ffe000'}},
        hAxis: {textStyle: {color: '#ffe000'}},
        legend: { position: 'bottom',textStyle: {color: '#e0e0e0', fontSize: 12} }
    };

    var chart = new google.visualization.LineChart(document.getElementById('chart1'));

    chart.draw(data, options);
}

function newflight(n) {
    devt.getFlight(parseInt(n.value));    
}
function start() {
    google.charts.load('current', {'packages':['corechart']});
    document.addEventListener("click", canvasmouse);
    initAniCanvas();
}
</script>
<style>
body {font-family: Arial, Helvetica, sans-serif;font-size: 9pt;margin: 0; padding: 0;background-color: black;}
#container {}
#left {width: 200px; float: left;}
#left p {color: #e0e0e0; margin-bottom: 0px;}
#left span.span1 {color: #e0e0e0;}
#left input.ieye {font-size: 7pt;}
#right {float: left; padding: 20px;}
#map {width: 900px; height: 500px;}
#map3d {border: solid 1px #808080; display: none;}
#heightchart {display: none;}
#chart1 {width: 1000px; height: 600px;}
#divani {display: none;}

</style>
</head>
<body onload='start()'>
    <div id='container'>
        <div id='left'>
            <p>FLIGHT</p>
            <input id='flightid' type='text' size='5' onchange='newflight(this)'/><br/>
            <input type="radio" name="maptype" value="1" onchange='maptypechange(this)' checked/><span class='span1'>Google MAP</span><br>
            <input type="radio" name="maptype" value="2" onchange='maptypechange(this)' /><span class='span1'>3D MAP</span><br>
            <input type="radio" name="maptype" value="3" onchange='maptypechange(this)' /><span class='span1'>HEIGHT CHART</span><br>
            <p id='detail1'></p>
            <div id='divani'>
                <p>ANIMATE DIRECTION</p>
                <canvas id='dir1' width=100 height=100></canvas>
                <p>VIEW DIRECTION</p>
                <canvas id='dir2' width=100 height=100></canvas>
                <p>OPTIONS</p>
                <input type="checkbox" typenum="1" onchange='optionchange(this)'/><span class='span1'>SHOW POINTS</span><br/>
                <input type="checkbox" typenum="2" onchange='optionchange(this)' checked/><span class='span1'>2D INSERT</span><br/>
                <p>CAMERA HEIGHT (feet)</p>
                <input typre='text' size='4' onchange='camheight(this)' value='1500' /><br/>
                <p>2D ZOOM</p>
                <input type="text" size='3' onchange='zoomfactor(this)' value='4' /><br/>
                <p>ANIMATE</p>
                <button id='anibut1' on='0' onclick='startAnimate(this)'>ANIMATE</button><br/>
                <input id='eyex' class='ieye' size='3' /><input id='eyey' class='ieye' size='3' /><input id='eyez' class='ieye' size='3' /><br/>
            </div>
        </div>
        <div id='right'>
            <div id='map'>
            </div>
            <canvas id='map3d' width=900 height=600></canvas>
            <div id='heightchart'><div id='chart1'></div></div>
        </div>
        <div class='clear'></div>
    </div>
</body>
</html>
