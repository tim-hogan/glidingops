<?php
  include './helpers/session_helpers.php';
  include 'helpers.php';

  session_start();
  require_security_level(4);

  $org = current_org();
  $membershipStatusActive = App\Models\MembershipStatus::activeStatus();
?>

<?php
$con_params = require('./config/database.php'); $con_params = $con_params['gliding'];
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
{
 echo "<p>Unable to connect to database</p>";
}
$q="SELECT name, twitter_consumerKey from organisations where id = " . $org;
$r = mysqli_query($con,$q);
$row = mysqli_fetch_array($r);
$strOrgName = $row[0];
$consumerKey= $row[1];
?>

<!DOCTYPE HTML>
<html>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<head>
<style type="text/css">
body {font-family: sans-serif;}
h1.hh1{font-family: Calibri, Tahoma, sans-serif;text-shadow: 1px 1px #e0e0e0;color: #505070}
h2 {font-size: 12px; font-weight: normal;}
p.p1 {margin-top: 0px; font-size: 12px;}
span.span1 {margin-top: 0px; font-size: 12px;}
td.rederr {margin-top: 0px; font-size: 12px;color: #ff0000;}
td.td1 {font-size: 10px; font-weight: normal;}
#body {margin: 0px;font-family: Arial, Helvetica, sans-serif;}
#container{margin: 0px;
	border: 0px;}
#centrearea{width: 940px;background-color: #e8e8ff;margin:0 auto;}
#titlearea{width: 600px;height: 50px; position: relative;background-color: #fcfc83;margin:0px;left: 360px;top: -20px;text-align: center;border-radius: 10px;box-shadow: 8px 8px 8px #c0c0c0;}
#entrybox{width: 800px;background-color: #d8d8d8;border-radius: 5px;margin: 40px 40px 10px 40px;padding: 10px}
#wholists{width: 800px;background-color: #d8d8d8;border-radius: 5px;margin: 10px 40px 10px 40px;padding: 10px}
#nav_bar{width: 820px; height: 30px;background-color: #f0f0f0;margin: 10px 40px 10px 40px;border-radius: 5px 5px 5px 5px;}

ul {list-style-type: none;padding-left: 10px;padding-right: 10px;}
li {float:left; width:100px; background-color: #f0f0f0; text-align:center;padding-top: 5px;padding-bottom: 5px;}
li.right {float:right; width:100px; background-color: #f0f0f0; text-align:center;padding-top: 5px;padding-bottom: 5px;}
</style>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
<script>
function textCounter(field, cnt, maxlimit)
{
 var cntfield = document.getElementById(cnt)
 if (field.value.length > maxlimit) // if too long...trim it!
    field.value = field.value.substring(0, maxlimit);
    // otherwise, update 'characters left' counter
    else
    cntfield.innerHTML = maxlimit - field.value.length;
 }
</script>
<script>
function selectUnselectAll(element, role_id){
  var isChecked = element.checked;
  var checkboxes = document.querySelectorAll("#role_" + role_id + " input[type=checkbox]")
  for(var i = 0; i < checkboxes.length; i++){
	  checkboxes[i].checked = isChecked;
  }
};
</script>
</head>
<body id="body">
<?php include __DIR__.'/helpers/dev_mode_banner.php' ?>
<?php
$errtxt="";
function InputChecker($data)
{
 $data = trim($data);
 $data = stripslashes($data);
 $data = htmlspecialchars($data);
 return $data;
}

function CreateTextRecord($msgid,$memberid,$phonenum)
{
  $con_params = require('./config/database.php'); $con_params = $con_params['gliding'];
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
  if (!mysqli_connect_errno())
  {
    $q2 = "SELECT * FROM texts WHERE txt_msg_id = " . $msgid . " and txt_to = " . $phonenum ;
    $r2 = mysqli_query($con,$q2);
    if ($r2->num_rows == 0)
    {
      $q1 = "INSERT INTO texts (txt_msg_id,txt_member_id,txt_to,txt_status) VALUES ('" .	$msgid ."','" . $memberid . "','" . $phonenum . "','0')" ;
      mysqli_query($con,$q1);
    }
    mysqli_close($con);
  }
}

function CreateMsgRecord($msg)
{
   $msgid = 0;
   $con_params = require('./config/database.php'); $con_params = $con_params['gliding'];
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (!mysqli_connect_errno())
   {
        $msg2 = htmlspecialchars_decode($msg);
        $msg2 = mysqli_real_escape_string($con, $msg2);

        $sqlmsg = "INSERT INTO messages (org,msg) VALUES (".$_SESSION['org'].",'" . $msg2 ."')";
	if (mysqli_query($con,$sqlmsg) )
	{
	    $msgid=mysqli_insert_id($con);
	}
        mysqli_close($con);
   }
   return $msgid;
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $bHaveMember=0;
    $lastmsgid=0;
    $msg_f = InputChecker($_POST["msg"]);
    if (empty($msg_f) )
    {
        $errtxt="You must enter a message";
    }
    else
    {
        //Loop here checking what members have been checked
        {

	    $bHaveMember = 0;
	    //Loop here for all members
	    $sql= "SELECT * FROM members WHERE org=" . $_SESSION['org'];
            $r = mysqli_query($con,$sql);
            while ($row = mysqli_fetch_array($r) )
            {
	        if(in_array((string)$row['id'],$_POST["member"]) && $row['enable_text'] > 0)
                {
                     if ($bHaveMember == 0)
		     {
		        //Create the message record
                        if ($lastmsgid == 0)
			    $lastmsgid = CreateMsgRecord($msg_f);
                     }
		     $bHaveMember = 1;
		     //Create a text message linking to this message

		     if ($lastmsgid != 0)
		     {
			CreateTextRecord($lastmsgid,$row['id'],$row['phone_mobile']);
		     }

                }
            }
	    //Loop here for all groupds
            $sql= "SELECT * FROM groups";
            $r = mysqli_query($con,$sql);
            while ($row = mysqli_fetch_array($r) )
            {
	        if(in_array((string)$row['id'],$_POST["group"]) )
                {
			$q1 = "SELECT gm_member_id FROM group_member
                                INNER JOIN members ON members.id = group_member.gm_member_id
                                WHERE gm_group_id = {$row['id']} AND members.status = {$membershipStatusActive->id}";
			$r1 = mysqli_query($con,$q1);
			while ($row2 = mysqli_fetch_array($r1) )
            		{
				//Look up the member
				$q2 = "SELECT * FROM members WHERE org=" .$_SESSION['org']. " and id = " . $row2['gm_member_id'];
				$r2 = mysqli_query($con,$q2);
				$row3 = mysqli_fetch_array($r2);
				if ($row3['enable_text'] > 0)
				{
				     $bHaveMember = 1;
				     if ($lastmsgid == 0)
					$lastmsgid = CreateMsgRecord($msg_f);
				     if ($lastmsgid != 0)
				     	CreateTextRecord($lastmsgid,$row3['id'],$row3['phone_mobile']);
				}

			}
		}
	    }



        }
    }

    //Do we send to twitter
    if(in_array("twitter",$_POST["member"]))
    {
	require_once './twitter.class.php';

	// ENTER HERE YOUR CREDENTIALS (see readme.txt)
	//$consumerKey="KUeT6uiFJibrAAOFHly5fJJqH";
	$consumerSecret="ecH6zt0IAbuKCayUUV35BFwlsw6MlHodiPpZ22HWz1kvhncUIQ";
	$accessToken="2521364305-uQbdUP4p9xma4ec6gEaPkqHB6PjlPq1LwWLlwjb";
	$accessTokenSecret="bTxQVJ8MJLfDyvGKf64l8XN2MgxMd44RsK6KY2djzW1UZ";

	try
	{
		$twitter = new Twitter($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
	}
	catch (TwitterException $e)
	{
   		echo 'Error: ' . $e->getMessage();
	}
        if ($lastmsgid == 0)
            $lastmsgid = CreateMsgRecord($msg_f);

	try
	{
   		$tweetmsg=htmlspecialchars_decode($msg_f);
		if (strlen($tweetmsg) > 140)
			$tweetmsg = substr($tweetmsg,0,140);
		$tweet = $twitter->send($tweetmsg); // you can add $imagePath as second argument

	}
	catch (TwitterException $e)
	{
   		$errtxt =  "Twitter Error: " . $e->getMessage();
	}
    }


    if ($bHaveMember == 0)
    {
        if(!in_array("twitter",$_POST["member"]))
	{
		if (empty($errtxt))
			$errtxt= "Error, you must select someone or twitter to send the message too.";
	}
    }
    else
    {
	header('Location: SendTxt.php');
    }
}
?>



<div id="container">
<div id="centrearea">
<div id="titlearea">
<h1 class='hh1'><?php echo $strOrgName . " Messaging";?></h1>
</div>
<div id="nav_bar">
<ul>
<li><a href = 'home'>Home</a></li>
<li><a href = 'members-list.php'>Members</a></li>
<li><a href = 'GroupAllocate.php'>Groups</a></li>
<li><a href = 'texts-list.php'>List Texts</a></li>
<li class='right'>Help</li>
</ul>
</div>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<div id="entrybox">
<p class='p1'>Enter your broadcast message:</p>
<textarea onkeyup="textCounter(this,'cnter' ,140)" onmouseup="textCounter(this,'cnter' ,140)" rows="4" cols="80" type='text' name='msg' maxlength="140"></textarea>
<span id="cnter" class='span1'>140</span>
</div>
<div id="wholists">
<p class='p1'>Select who to send to:</p>
<?php
if (strlen($consumerKey) > 0)
{
 echo "<table><tr><td class='td1'><input type='checkbox' name='member[]' value='twitter' checked>Twitter</td></tr></table>";
}
?>
    <?php
$q_retrieve_roles_used_by_current_org = <<<SQL
SELECT id, name
FROM  roles
WHERE id in(
    SELECT DISTINCT role_id FROM gliding.role_member WHERE org = {$_SESSION['org']}
)
SQL;
$roles = mysqli_fetch_all(mysqli_query($con, $q_retrieve_roles_used_by_current_org), MYSQLI_ASSOC);


for ($roleidx=0;$roleidx<count($roles);$roleidx++)
{
 echo "<h2><b>";
 echo "<input type='checkbox' onchange='selectUnselectAll(this, ".$roles[$roleidx]['id'] .")'/>";
 echo $roles[$roleidx]['name'];
 echo "s</b></h2>";
 echo "<table id='role_".$roles[$roleidx]['id'] ."'>";
  $colm = 0;


  $sql2= "SELECT a.id, a.displayname, a.surname , a.firstname from role_member LEFT JOIN members a ON a.id = role_member.member_id where role_member.org = ".$_SESSION['org']. " and role_id = " .$roles[$roleidx]['id'] . " order by a.surname,a.firstname";


  $r2 = mysqli_query($con,$sql2);
  while ($row2 = mysqli_fetch_array($r2) )
  {
    if ($colm == 0)
        echo "<tr>";
    echo "<td class='td1'>";
    echo "<input type='checkbox' name='member[]' value='";
    echo $row2[0];
    echo "'>";
    echo $row2[1];
    echo "</td>";
    $colm = $colm + 1;
    if ($colm == 6)
    {
        $colm = 0;
        echo "</tr>";
    }
  }
  if ($colm !=0)
      echo "</tr>";
  echo "</table>";

}

 echo "<h2>Members</h2>";
 echo "</h2>";
 echo "<table>";
  $colm = 0;

$sql2 = "SELECT members.id,members.displayname,members.surname,membership_class.disp_message_broadcast
        FROM members
          LEFT JOIN membership_class  ON membership_class.id = members.class
        WHERE members.org = {$org} AND membership_class.disp_message_broadcast = 1
                                   AND members.status = {$membershipStatusActive->id}
        ORDER BY surname,firstname ASC";

$r2 = mysqli_query($con,$sql2);
while ($row2 = mysqli_fetch_array($r2) )
{
    if ($colm == 0)
        echo "<tr>";
    echo "<td class='td1'>";
    echo "<input type='checkbox' name='member[]' value='";
    echo $row2[0];
    echo "'>";
    echo $row2[1];
    echo "</td>";
    $colm = $colm + 1;
    if ($colm == 6)
    {
        $colm = 0;
        echo "</tr>";
    }
  }
  if ($colm !=0)
      echo "</tr>";
  echo "</table>";


echo "<h2>Groups</h2>";
echo "<table>";
$colm = 0;
$sql= "SELECT * FROM groups where org = " . $org;
$r = mysqli_query($con,$sql);
while ($row = mysqli_fetch_array($r) )
{

    if ($colm == 0)
        echo "<tr>";
    echo "<td class='td1'>";
    echo "<input type='checkbox' name='group[]' value='";
    echo $row['id'];
    echo "'>";
    echo $row['name'];
    echo "</td>";
    $colm = $colm + 1;
    if ($colm == 6)
    {
        $colm = 0;
        echo "</tr>";
    }
  }
  if ($colm !=0)
      echo "</tr>";

echo "</table>";

mysqli_close($con);
    ?>
<table>
<tr><td><input type='submit' value='Send'></td>
<?php echo "<td class = 'rederr'>" . $errtxt . "</td>"; ?>
</tr></table>
</div>
</form>
<?php
if ($org == 1)
{
echo "<a href='https://twitter.com/glidingwlgtn' class='twitter-follow-button' data-show-count='true' data-lang='en'>Follow @glidingwlgtn</a>";
}
?>
</div>
</div>
</body>
</html>