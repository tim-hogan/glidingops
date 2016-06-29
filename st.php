<!DOCTYPE HTML>
<html>
<head>
<title>Screen Test</title>
<style>
body {margin: 0px;font-family: Arial, Helvetica, sans-serif;}
#container {background-color:#f0f0ff;}
</style>
<script>
function Start()
{
 var d = document.getElementById("b1");
 d.innerHTML = "User-agent header: " + navigator.userAgent;
 d = document.getElementById("b2");
 d.innerHTML = "Java Enabled: " + navigator.javaEnabled();
 d = document.getElementById("id1");
 d.innerHTML = "Screen Size = " + window.screen.width + "," + window.screen.height;
 d = document.getElementById("id2");
 d.innerHTML = "Screen Available = " + window.screen.availWidth + "," + window.screen.availHeight;
 d = document.getElementById("id3");
 d.innerHTML = "Window Outer Size = " + window.outerWidth + "," + window.outerHeight;
 d = document.getElementById("id4");
 d.innerHTML = "Window Inner Size = " + window.innerWidth + "," + window.innerHeight;

}
</script>
</head>
<body onload='Start()'>
<h1>BROWSER PROPERTIES</h1>
<p id='b1'></p>
<p id='b2'></p>
<h1>SCREEN DIMENTIONS</h1>
<h2>Screen Properties</h2>
<p id='id1'></p>
<p id='id2'></p>
<h2>Window Properties</h2>
<p id='id3'></p>
<p id='id4'></p>
</body>
</html>
