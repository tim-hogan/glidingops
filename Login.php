<!DOCTYPE HTML>
<html>
<head>
<title>glidingops.com Home</title>
<style>
body {margin: 0px;font-family: Arial, Helvetica, sans-serif;background-color:#f0f0ff;}
#container {background-color:#f0f0ff;}
#heading {background-color:#0066cc;}
#entry {background-color:#d1d1ff;float: right;width: 300px;font-size: 11px;margin-left: 40px;}
#form {margin: 10px;}
#picy {float: right;}
#picy2 {margin: 20px;}
#main {margin: 10px;max-width:800px;}
#main2 {margin: 10px;max-width:800px;}
#foot {float: bottom; text-align:center;}
table {border-collapse: collapse;}
h1 {font-size: 16px;}
p.fg {font-size:11px;}
.right {text-align: right;}
a {text-decoration: none;}
a.fg {font-size:11px;}
a:link{color: #0000c0;}
a:visited {color: #0000C0;}
a:hover {color: #0000FF;}
</style>
</head>
<body>
    <link rel="stylesheet" href="./css/notify.css" />
    <script src="./js/notify.js"></script>
    <?php
        if(isset($_GET['registered'])){
        ?>
        <script>
        var options = {
            message: "<br/> Check your mail box! <br/> </br> You should have received a welcome email from operations@glidingops.com",
            color: "success",
            timeout: 10000,
        };
        notify(options);
        </script>
        <?php
        }
    ?>    
    <div id='container'>
        <div id='heading'>
            <img src='HomeLogo.png'>
        </div>
        <div id='entry'>
            <div id='form'>
                <p>Please enter your login details</p>
                <form method='POST' action='checklogin.php'>
                    <table>
                        <tr><td>Username:</td><td><input type='text' name='user' size='20' title='Enter email address' autofocus></td><td></td></tr>
                        <tr><td>Password:</td><td><input type='password' name='pcode' size='20' ></td><td></td></tr>
                        <tr><td class='fg'><a href='Forgotten.php' class='fg'>Forgotten Password</a></td><td class='right'><input type="submit" name"Submit" value="Login"></td><td></td></tr>
                    </table>
                </form>
                <p>Club members not registered yet, click <a href='RegisterMe'>here</a></p>
            </div>
        </div>
        <div id='main'>
            <p>Welcome to glidingops.com, the place where we help manage your Gliding club's operations.  Here you will find a set of web based tools that helps the operations of your club, giving you more time to do what you really want to do, fly.</p>  
            <p>The glidingops.com project started with the <a href='http://www.soar.co.nz/'>Wellington Gliding Club, New Zealand</a> in 2014 when a better tool for communicating with varying groups of multiple members was required. From there, it has now grown to a rich operations and management tool for the club.</p>
            <p>The club Treasurer can now get a spreadsheet with all the members' billing information with just a few clicks whilst engineers and the CFI can get immediate access to flying hours and logs for both gliders and pilots.</p>
            <p>A large screen TV in the club house gives live coverage of the days activities, creating a central spot for both members and visitors to congregate and enjoy the activities.</p> 
        </div>
        <div id="picy">
            <div id="picy2">
                <img src="HomePhoto.jpg"></img>
            </div>
        </div>
        <div id='main2'>
            <h1>Features</h1>
            <ul>
            <li>Management of membership database</li>
            <li>Flexible communications to selected groups of members</li>
            <li>Enables electronic flight recording (with data entry possible on mobile devices)</li>
            <li>CFI, CTP, Treasurer and Engineering Reports</li>
            <li>Automated billing</li>
            <li>Individual flight summaries emailed to pilots at the end of each flying day</li>
            <li>Integrated SPOT and other tracking</li>
            <li>Enables display of flight tracking</li>
            <li>Full member portal incluidng booking system under devlopment</li>
            </ul>
            <p>If your club is interested in options to use the system, please email us at <a href='mailto:wgcoperations@gmail.com?subject=Interest%20in%20gliding%20ops'>wgcoperations@gmail.com</a></p>
        </div>
    </div>
    <footer id='foot'>
        Copyright &#169; glidingops.com 2014
    </footer>
    </body>
</html>
