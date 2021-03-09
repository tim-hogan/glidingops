<?php
$when = "today";
if (isset($_GET['d']))
    $when = $_GET['d'];
?>
<!DOCTYPE HTML>
<html>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<head>
<title>GLIDER HEIGHTS</title>
<style>
body {font-family: Arial, Helvetica, sans-serif;font-size: 10pt;margin: 0;background-color: black;}
#graph1 {width: 1400; height: 800;}
p.err {color: red;}
</style>
<script type="text/javascript" src="/js/devtcore.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script>
    var api = new devt.apiJSON('glidingops.com', 'api/v1/json', '1234567890123456', true);
    google.charts.load('current', {'packages':['corechart']});

    api.parseReply = function (d) {
        if (d.meta.status != "OK") {
            document.getElementById("graph1").innerHTML = "<p class='err'>NO DATA TO GRAPH</p>";
        }

        if (d.meta.status == "OK") {
            graphheights(d.data);
        }
    }
    function getdata() {
        api.queueReq("GET", "trackheights/1/<?php echo $when;?>", null);
    }

    function graphheights(d) {
        var s = [[{ "label": "Time", "type": "timeofday" }, "GCH", "GGR", "GLD", "GPJ", "GRK", "GUS"], [[8, 0, 0], null, 1.3, 1.4, 1.5, 1.6, 1.7], [[8, 1, 0], 1.2, 1.3, 1.4, 1.5, 1.6, 1.7],[[8, 2, 0], 1.3, 1.4, 1.5, 1.6, 1.7, 1.8]];
        var data = google.visualization.arrayToDataTable(d);
        var options = {
            title: 'Glider Heights',
            curveType: 'function',
            height: 800,
            width: 1400,
            backgroundColor: '#000000',
            titleTextStyle: { color: '#b0b0ff' },
            vAxis: {title: "Height in feet", titleTextStyle: {color: '#ffe000' },textStyle: { color: '#ffe000' }, minValue: 0, viewWindow:{ min: 0 } },
            hAxis: {textStyle: { color: '#ffe000' }},
            legend: { position: 'bottom',textStyle: {color: '#e0e0e0', fontSize: 12} }
        };
        var chart = new google.visualization.LineChart(document.getElementById('graph1'));
        chart.draw(data, options);
        //ticks: [[8, 0, 0], [10, 0, 0],[12, 0, 0],[14, 0, 0],[16, 0, 0],[18, 0, 0],[20, 0, 0]],
    }
    function tick() {
        getdata();
    }
    function start() {
        getdata();
        setInterval(tick, 300000);
    }
</script>
</head>
<body onload="start()">
    <div id="graph1">

    </div>
</body>

