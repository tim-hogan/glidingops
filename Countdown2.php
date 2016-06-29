<!DOCTYPE html>
<head>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<meta http-equiv="refresh" content="3600">
<style>
body {margin:0; font-family: Arial, Helvetica, sans-serif; background: black;}
#clk {color: #ffffff;font-size: 24px;text-align: center;}
#msg {background-color: #1010ff;width: 200px;margin-left: auto;margin-right: auto;border-radius: 8px; box-shadow: 5px 5px 5px #888888;}
#msgtxt {margin:5px;color: #f0f000;font-size: 12px;padding: 5px;}
</style>
<script>
function pad(num, size) {
    var s = num+"";
    while (s.length < size) s = "0" + s;
    return s;
}
var quotes = ["Some people drink from the fountain of knowledge, others just gargle.<br><i>Robert Newton Anthony 2002</i>",
            "I hear Socrates saying that the best seasoning for food is hunger; for drink, thirst.<br><i>Cicero, De Finibus Bonorum et Malorum, II. 28</i>",
	    "Nothing will benefit human health and increase the chances for survival of life on Earth as much as the evolution to a vegetarian diet.<br><i>Albert Einstein</i>",
	    "Time is an illusion, lunchtime doubly so<br><i>Douglas Adams, The Hitchhiker's Guide to the Galaxy (said by Ford Prefect)</i>"];
var timeCnt = 0;
var nextQuote = 0;

function updateTime()
{
  var a = document.getElementById('clk');
  var dtNow = new Date();
  var dtThen = new Date(2015,3,28,15,0,0,0);
  var secs = (dtThen.getTime() - dtNow.getTime()) / 1000;
          var hrs = pad(Math.floor( secs / 3600),2);
          var mins = pad(Math.floor((secs / 60) % 60),2);     
          var secs = pad(Math.floor( secs % 60),2);
  a.innerHTML = hrs + ":" + mins + ":" + secs;

  if (timeCnt == 0)
  {
      document.getElementById('msgtxt').innerHTML = quotes[nextQuote];
      nextQuote = (nextQuote + 1) % quotes.length;
  }
  timeCnt = (timeCnt + 1) % 120;

}
function start()
{
setInterval(updateTime,1000);
}
</script>
</head>
<body onload='start()'>
<p id='clk'></p>
<div id='msg'>
<p id='msgtxt'></p>
</div>
</body>
</html>