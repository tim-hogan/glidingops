<!DOCTYPE html>
<head>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<meta http-equiv="refresh" content="3600">
<style>
body {margin:0; font-family: Arial, Helvetica, sans-serif; background: black;}
#hd {color: #2020ff;font-size: 20px;text-align: center;}
p.clk {color: #ffffff;font-size: 24px;text-align: center;}
#msg {background-color: #1010ff;width: 250px;margin-left: auto;margin-right: auto;border-radius: 8px; box-shadow: 5px 5px 5px #888888;}
#msgtxt {margin:5px;color: #f0f000;font-size: 12px;padding: 5px;}
#im {text-align: center;}
#im2 {text-align: center;}
</style>
<script>
function pad(num, size) {
    var s = num+"";
    while (s.length < size) s = "0" + s;
    return s;
}
var quotes = ["We live in an age when unnecessary things are our only necessities.<br><i>Oscar Wilde</i><br>1",
            "Whatever you do will be insignificant, but it is very important that you do it.<br><i>Mahatma Gandhi</i><br>2",
	     "Good judgment comes from experience, and experience comes from bad judgment.<br><i>Rita Mae Brown</i><br>3",
             "<b>MANAGEIRUM</b><br>The heaviest element known to science is managerium. The element has no protons or electrons but has a nucleus composed of one neutron, two vice-neutrons, five assistant vice-neutrons, 25 pro vice-neutrons and 125 assistant pro vice-neutrons all going round in circles. Managerium has a half-life of three years at which time it does not decay but institutes a series of reviews leading to reorganization. Its molecules are held together by means of the exchange of tiny particles known as morons.<br>4",
             "For the first time, she did not want more. She did not know what she wanted, knew that it was dangerous and that she should rest content with what she had, but she knew and emptiness deep insider her, which began to ache.<br><i>Ian Pears</i><br>5"
];
var timeCnt = 0;
var nextQuote = 0;

function dispClk(id,dt,t)
{
	var a = document.getElementById(id);
        var dtNow = new Date();
	var secs = (dt.getTime() - dtNow.getTime()) / 1000;
  	if (secs < 0)
    		a.innerHTML = "Countdown Reached";
  	else
        {
         var hrs = pad(Math.floor( secs / 3600),2);
         var mins = pad(Math.floor((secs / 60) % 60),2);     
         var secs = pad(Math.floor( secs % 60),2);
  	 a.innerHTML = t + hrs + ":" + mins + ":" + secs;
        }

}
function updateTime()
{
  var dtThen1 = new Date(2015,11,11,17,00,0,0);
  var dtThen2 = new Date(2015,11,25,00,00,0,0);
  var dtThen3 = new Date(2016,3,29,0,0,0,0);
  var dtThen4 = new Date(2016,6,22,12,40,0,0);
  var dtThen5 = new Date(2016,9,5,0,0,0,0);
  dispClk('clk1',dtThen1,'');
  dispClk('clk2',dtThen2,'RT: ');
  dispClk('clk3',dtThen3,'');
  dispClk('clk4',dtThen4,'BD: ');
  dispClk('clk5',dtThen5,'BD: ');

  if (timeCnt == 0)
  {
      //document.getElementById('msgtxt').innerHTML = quotes[nextQuote];
      nextQuote = Math.floor((Math.random() * quotes.length));
  }
  timeCnt = (timeCnt + 1) % 15;

}
function start()
{
nextQuote = Math.floor((Math.random() * quotes.length));
setInterval(updateTime,1000);
}
</script>
</head>
<body onload='start()'>
<div id='im2'>
<img src="deVT-Logo-Black.png"  alt="deVT">
</div>
<!--  <p id='hd'>A SPECIAL PLACE FOR VIC</p> -->
<p id='hd'>A NEW BEGINNING</p>
<p id = 'clk1' class='clk'></p>
<p id = 'clk2' class='clk'></p>
<p id = 'clk3' class='clk'></p>
<p id = 'clk4' class='clk'></p>
<p id = 'clk5' class='clk'></p>
<div id='msg'>
<!--<p id='msgtxt'></p>-->
</div>
<div id='im'>
<img src="True-Magic.png" alt="True Magic">
<!-- style="width:304px;height:228px;" -->
</div>
</body>
</html>