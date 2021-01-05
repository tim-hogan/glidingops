<?php
session_start();
function var_error_log( $object=null,$text='')
{
    ob_start();
    var_dump( $object );
    $contents = ob_get_contents();
    ob_end_clean();
    error_log( "{$text} {$contents}" );
}

//Start
//Gte t=environemnt variables
$smskey = getenv("SMS_KEY");
$gateway_host = getenv("SMS_HOST");
if (!$smskey || strlen($smskey) == 0)
{
    error_log("Send text cannot get SMS key from environemnt varaibales");
	echo "<p>Unable to get SMS key from environemnt variables</p>";
	exit();
}

if (!$gateway_host || strlen($gateway_host) == 0)
{
    error_log("Send text cannot get SMS gateway host from environemnt varaibales");
	echo "<p>Unable to get SMS gateway host  from environemnt variables</p>";
	exit();
}

$con_params = require('./config/database.php'); $con_params = $con_params['gliding'];
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
{
	error_log("SendTxt ERROR: Unable to cpnnect to database");
	echo "<p>Unable to connect to database</p>";
	exit();
}
else
{

	$sql="SELECT texts.txt_id,texts.txt_to, messages.msg FROM texts INNER JOIN messages ON messages.id = texts.txt_msg_id WHERE txt_status = 0";
	$r = mysqli_query($con,$sql);
	while ($row = mysqli_fetch_array($r) )
	{

		//Parse the DB parameters
        if ($row['txt_to'] && $row[2] && strlen($row[2]) > 0)
        {
            $strTo = trim($row['txt_to']);
            $strTo = trim($strTo,"+");
            $strTo = str_replace(" ","",$strTo);

            if (strlen($strTo) > 0)
            {

                $strTo = urlencode($strTo);


                $postparam = array();
                $postparam['smskey'] = getenv("SMS_KEY");
                $postparam['phone'] = $strTo;
                $postparam['msg'] = $row[2];
                $postparam['county_code'] ="64";
                $callback = '';
                if (empty($_SERVER['HTTPS']))
                    $callback = "http://";
                else
                    $callback = "https://";

                $callback .= $_SERVER['HTTP_HOST'];
                $callback .= "/TextStatus.php";
                $postparam['callback_url'] = $callback;

                $str = json_encode($postparam);
                $url = $gateway_host;

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS,$str);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
                $result = curl_exec($ch);


                $result = json_decode($result,true);


                $smsid = 0;
                $status = "ERROR";
                if (isset($result['meta']))
                {
                    if ($result['meta'] ['status'] = "OK")
                    {
                        $Q = "UPDATE texts SET txt_status=1, txt_timestamp_sent = now() WHERE txt_id = " . $row['txt_id'];
                        $r2 = mysqli_query($con,$Q);

                        $data = $result['data'];
                        $smsid = intval($data['textid']);
                        //if (isset($data['status']) && $data['status'])
                        //    $status = "SENT";
                        $Q = "UPDATE texts SET txt_unique=" . $smsid . " WHERE txt_id = " . $row['txt_id'] ;
                        $r2 = mysqli_query($con,$Q);
                    }
                }
            }
            else
            {
                //Mark as error
                $Q = "UPDATE texts SET txt_status=2, WHERE txt_id = " . $row['txt_id'];
                $r2 = mysqli_query($con,$Q);
            }
        }
        else
        {
            //Mark as error
            $Q = "UPDATE texts SET txt_status=2, WHERE txt_id = " . $row['txt_id'];
            $r2 = mysqli_query($con,$Q);
        }
	}

	mysqli_close($con);
}
header('Location: MessagingPage');
?>
