<?php session_start(); ?>
<?php
$con_params = require('./config/database.php'); $con_params = $con_params['gliding'];
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
{
    echo "Database Error";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if (isset($_POST['what']) )
    {
        error_log("Abount to change to {$_POST['what']}");
        $q = "UPDATE spots set rego_short = '{$_POST['what']}' WHERE spotkey = '01Gd66kbzX1grQGKt0W4d4zzClcB0nECk'";
        $r = mysqli_query($con,$q);
    }
}

$q = "SELECT * FROM spots WHERE spotkey = '01Gd66kbzX1grQGKt0W4d4zzClcB0nECk'";
$r = mysqli_query($con,$q);
$spot = mysqli_fetch_array($r);

?>
<!DOCTYPE HTML>
<html>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<head>
<style>
body {font-family: Arial, Helvetica, sans-serif;font-size: 10pt; margin: 0;}
#container {}
#main {margin: auto; width: 350px;}
#form {border: solid 1px #666; background-color: #eee; padding: 10px;}
p {text-align: center;}
p.p1 {font-size: 20pt; font-weight: bold; color: #444;}
p.p2 {text-align: left;}
select {margin-bottom: 20px;}
</style>
</head>
<body>
    <div id='container'>
        <div id='heading'>
        </div>
        <div id='main'>
            <div id='current'>
                <p>MARTYN'S SPOT IS CURRENTLY SET TO</p>
                <p class='p1'><?php echo $spot['rego_short'];?></p>
            </div>
            <div id='form'>
                <p class='p2'>CHANGE</p>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <p>WHAT AIRCARFT IS YOUR SPOT IN TODAY MARTYN?</p>
                    <select name='what'>
                        <option value='GCK'>GCK</option>
                        <option value='GPH'>GPH</option>
                        <option value='GTR'>GTR</option>
                        <option value='GUS'>GUS</option>
                    </select><br/>
                    <input type='submit' name='enter' value='PRESS TO CHANGE'/>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
