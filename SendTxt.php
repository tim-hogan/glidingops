<?php session_start(); ?>
<!DOCTYPE HTML>
<html>
<head>
</head>
<body>
<?php
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
{
 echo "<p>Unable to connect to database</p>";
}
else
{
	$sql="SELECT texts.txt_id,texts.txt_to, messages.msg FROM texts INNER JOIN messages ON messages.id = texts.txt_msg_id WHERE txt_status = 0";
	$r = mysqli_query($con,$sql);
	while ($row = mysqli_fetch_array($r) )
	{
		//Create a socket to the text engin
	
		error_reporting(E_ALL);


		/* Get the port for the WWW service. */
		$service_port = getservbyname('www', 'tcp');

		/* Get the IP address for the target host. */
		$address = gethostbyname('gateway.sonicmobile.com');

		/* Create a TCP/IP socket. */
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($socket === false) 
		{
    			echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "<br>";
		} 
		
		
		$result = socket_connect($socket, $address, $service_port);
		if ($result === false) 
		{
    			echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "<br>";
		} 
		
                echo "socket_connect()<br>";
                
		$in = "GET /message?application=gw_hogan_consultancy_lt&password=etj1mh5q2&customer=hogan_consultancy_lt&class=mt_message";
		$in .= "&content=";	
		$in .= urlencode($row[2]);
		$in .= "&destination=%2b";
		$strTo = trim($row['txt_to']);
		$strTo = trim($strTo,"+");
		$in .= urlencode($strTo);

		
		$in .= " HTTP/1.1\r\n";
		$in .= "Host: gateway.sonicmobile.com\r\n";
		$in .= "Connection: Close\r\n";
		$in .= "Cache-Control: max-age=0\r\n";
		$in .= "Accept: text/*\r\n";
		$in .= "User-Agent: Remote-Locator-1.1\r\n";
		$in .= "Accept-Encoding: *\r\n";
		$in .= "Accept-Language: *\r\n";
		$in .=  "\r\n";
		

		$out = '';
		$resp = '';
		
		socket_write($socket, $in, strlen($in));
		
		
		while ($out = socket_read($socket, 2048)) 
		{
			$resp .= $out;
		}

		
		
                $bFound = false;
		if (substr($resp,0,15) == "HTTP/1.1 200 OK")
		{
			
			$Q = "UPDATE texts SET txt_status=1, txt_timestamp_sent = now() WHERE txt_id = " . $row['txt_id'] ;
			$r2 = mysqli_query($con,$Q);
			
			$token = strtok($resp,"\n");		
			while ($token != false)
			{
				
				if ($bFound)
				{
					if (strlen($token) > 0)
					{
						
						$val = (int) $token;
						$bFound = false;
						$Q = "UPDATE texts SET txt_unique=" . $val . " WHERE txt_id = " . $row['txt_id'] ;
						
						$r2 = mysqli_query($con,$Q);
					}
				}
				if (substr($token,0,1) == "9")
				   $bFound = true;
				$token = strtok("\n");
			}		

		}		
		
		socket_close($socket);
		

	}

	mysqli_close($con);
}
header('Location: MessagingPage');
?>
</body>
</html>
