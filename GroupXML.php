<?php
  include './helpers/session_helpers.php';
  include 'helpers.php';
?>

<!DOCTYPE HTML>
<html>
<head>
</head>
<body>
<?php
if ($_SERVER["REQUEST_METHOD"] == "GET")
{
    header('Content-type: text/xml');
    $groupid = $_GET["groupid"];
    $org = $_GET["org"];
    //Loop here checking what members have been checked
    $con_params = require('./config/database.php'); $con_params = $con_params['gliding'];
    $con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
    if (mysqli_connect_errno())
    {
      $errtxt= "Unable to connect to database";
    }
    else
    {
      echo "<grouplist><groupid>" . $groupid . "</groupid>";

      $membershipStatusActive = App\Models\MembershipStatus::activeStatus();
      $sql = "SELECT members.id,members.displayname,membership_class.disp_message_broadcast
        FROM members
          LEFT JOIN membership_class  ON membership_class.id = members.class
        WHERE members.org = {$org} AND membership_class.disp_message_broadcast = 1
                                   AND members.status = {$membershipStatusActive->id}
        ORDER BY surname,firstname ASC";
      $r = mysqli_query($con,$sql);
      while ($row = mysqli_fetch_array($r) )
      {
        echo "<member><id>" . $row[0] . "</id><name>" . $row[1] . "</name>";
        //Check to see if member is in groups
        $q2 = "SELECT gm_member_id FROM group_member WHERE gm_group_id = " . $groupid . " AND gm_member_id = " . $row[0];
        echo "<sql>";
        echo $q2;
        echo "</sql>";
        $r2 = mysqli_query($con,$q2);
        if ($r2->num_rows == 0)
        {
          echo "<incl>0</incl>";
        }
        else
        {
          echo "<incl>1</incl>";
        }
        echo "</member>";
      }
      echo "</grouplist>";
      mysqli_close($con);
    }
}
?>
</body>
</html>
