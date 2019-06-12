<?php
require './includes/classGlidingDB.php';
$con_params = require('./config/database.php'); 
$DB = new GlidingDB($con_params['gliding']);

?>
<!DOCTYPE HTML>
<html>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<head>
<script type="text/javascript" src="http://static.devt.nz/devtscript.js?api_key=2fb411936371c7e2d5381e0aec347e36"></script>
<script>
var d = null;
var tracks = null;
var startidx = 0;
devt.api = new devt.apiJSON('gliding.terrace.devt.nz:88','api/v1/json','1234567890123456',false);
devt.api.parseReply = function(r) {
    if (r['meta'] ['status'] == 'OK' ) {
        switch (r['meta'] ['req']) {
            case 'flightdata':
                d = r['data'];
                processFlightData(d);
                break;
         }
     }
}
devt.getFlight = function(f) {
    devt.api.queueReq('GET','flightdata/' + f,null);    
}

devt.createTrack = function(glider,track) {
    var p = {};
    p.org = 1;
    p.glider = glider;
    p.lat = track['lat'];
    p.lon = track['lng'];
    p.alt = track['alt'];
    p.timeunix = parseInt(track['time']);
    
    devt.api.queueReq('POST','createtrack',p);    
}

function processFlightData(d) {
    tracks = d['tracks'];
    
    //Recalc time stamps
    var t1 = parseFloat(tracks[0] ['time']) * 1000.0;
    var now = new Date();
    var t2 = now.getTime();
    var offset = t2 - t1;
    
    for (var i = 0; i < tracks.length;i++)
        tracks[i] ['time'] =  parseInt(((parseInt(tracks[i] ['time']) * 1000) + offset) / 1000);
    startidx = 0;
    
}

function tick() {
    if (d) {
        var now = new Date();
        while ( startidx < tracks.length && (parseInt(tracks[startidx] ['time']) * 1000) < now.getTime() ) {
            console.log("Issue track idx " + startidx);
            devt.createTrack(d['aircraft'],tracks[startidx]);
            startidx++;
        }
    }
}

function newFlight(n) {
    devt.getFlight(n.value);
}

function start() {
    setInterval(tick,1000);
}
</script>
</head>
<body onload='start()'>
    <div id = 'container'>
        <input type='text' onchange='newFlight(this)'/>
    </div>
</body>
</html>