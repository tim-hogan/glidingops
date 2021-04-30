<?php session_start(); ?>
<?php
//devt.Version = 1.0
function var_error_log( $object=null )
{
    ob_start();                    // start buffer capture
    var_dump( $object );           // dump the values
    $contents = ob_get_contents(); // put the buffer into a variable
    ob_end_clean();                // end capture
    error_log( $contents );        // log contents of the result of var_dump( $object )
}


$selff = htmlspecialchars($_SERVER["PHP_SELF"]);
$_SESSION['returnto'] = $selff;

$errormsg = [
    'default'=> [
            '1' => "You have arrived here as you do not have sufficient permissions to do what you were trying to do.",
            '2' => "You can try <a href='Signin.php'>Signing</a> in again or see the site administrator.",
        ],
     'verification' => [
            '1' => "The verification link you submitted was invalid.",
            '2' => "Either Sign-in and check your registartion or re-register.",
     ],
];

if (isset($_SESSION['new_error_message']))
{
    $newm = trim($_SESSION['new_error_message']);
    $newm = stripslashes($newm);
    $newm = strip_tags(htmlspecialchars_decode($newm));
    $_SESSION['security_message'] = $newm;
}

$errortype = "default";
$additionalmsg = '';
if (isset($_SESSION['security_error'] ))
{
    $errortype = $_SESSION['security_error'];
    unset($_SESSION['security_error']);

    if (isset($_SESSION['security_message']))
    {
        $additionalmsg = htmlspecialchars($_SESSION['security_message']);
        unset($_SESSION['security_message']);
    }

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<head>
    <title>Security Error</title>
    <link rel='stylesheet' type='text/css' href='css/basecentre.css' />
    <link rel='stylesheet' type='text/css' href='css/heading.css' />
    <link rel='stylesheet' type='text/css' href='css/maincentre.css' />
    <style>
    #mainconatiner {max-width: 1000px; margin: auto;}
    #error {font-family: 'Roboto'; font-size: 12pt;}
    #error img {height: 200px;}
    #error h1 {margin-left: 40px; color: #666;}
    #error p {margin-left: 60px;}
    </style>
</head>
<body>
  <div class="container">
      <div id='heading'>
          <span>
              nVALUATE
              <sup>&#174;</sup>
          </span>
      </div>
    <div id='mainconatiner'>
        <div id='main'>
            <div id='error'>
                <img src='/images/SecurityShield.png'/>
                <h1>SECURITY ERROR</h1>
                <?php
                error_log($errortype);
                $err1 = $errormsg[$errortype] ['1'];
                $err2 = $errormsg[$errortype] ['2'];
                echo "<p>{$err1}</p>";
                echo "<p>{$err2}</p>";
                if (strlen($additionalmsg) > 0)
                    echo "<p>{$additionalmsg}</p>";
                ?>
            </div>
        </div>
    </div>
  </div>
</body>
</html>
