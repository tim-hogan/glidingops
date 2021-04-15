<?php
session_start();
require_once "../includes/classSecure.php";
include '../helpers/session_helpers.php';
require "../includes/classFormList2.php";
require "../includes/classGlidingDB.php";

require_security_level(SECURITY_ADMIN);
$current_org = current_org();

$DB = new GlidingDB($devt_environment->getDatabaseParameters());

?>

<!DOCTYPE HTML>
<html>
  <head>
    <meta name="viewport" content="width=device-width" />
    <meta name="viewport" content="initial-scale=1.0" />
    <link rel="icon" type="image/png" href="favicon.png" />
    <script type="text/javascript">
      function genuineIdChanged() {
        var submitBtn = document.getElementById('submit');
        submitBtn.disabled = false;
      }
    </script>
    <style type="text/css">
      table { 
        border-collapse: collapse; 
      }
      th {
        text-align: left;
        padding-left: 5px;
        padding-right: 5px;
      }
      td {
        padding-left: 5px;
        padding-right: 5px;
      }
      thead tr {
        border-bottom-width: 1px;
        border-bottom-style: solid;  
      }
      .even {
        background-color: lightgray;
      }
    </style>
  </head>

<?php
if (! isset($_GET['v']))
    die('Invalid input parameter');

$a = FormList::decryptParamRaw($_GET['v']);
$firstname = $a['firstname'];
$surname = $a['surname'];
$org = $a['org'];

if($org != $current_org) {
die('You can only delete members from your own organisation.');
}

$columns = array("id", "create_time", "member_id",
                "firstname", "surname",
                "org_name", "date_of_birth", "phone_home",
                "phone_mobile",
                "phone_work",
                "email", "class", "status", "gnz_number");

?>

  <body>
    <form action="./duplicates_delete.php" method="post">
    <table>
      <thead>
        <tr>
          <?php 
          foreach ($columns as $column)
            echo "<th>{$column}</th>";
          ?>
          <th>Keep</th>
        </tr>
      </thead>
      <tbody>
<?php
$tr_class = 'even';
$ids = array();
$result = $DB->DuplicateMember($firstname,$surname,$org);
while($row = $result->fetch_assoc())
{
$tr_class = ($tr_class == 'even') ? 'odd' : 'even';
echo "<tr class='{$tr_class}'>";
foreach ($columns as $column)
{
    $str = htmlspecialchars($row[$column]);
    echo "<td>{$str}</td>";
}
echo "<td><input type='radio' name='genuine_id' value='{$row['id']}' onClick='genuineIdChanged();' /></td>";
echo "</tr>";
array_push($ids, $row['id']);
}
?>
      </tbody>
    </table>
    <input type="hidden" name="ids" value="<?php echo implode(",", $ids) ?>"/>
    <div style="margin-top: 10px;">
      <input type="submit" name="Clean" id="submit" disabled style="font-size: x-large;" />
    </div>
    </form>
    <div style="width: 100%; margin-top: 20px;">
      <div style="float: left; margin-right: 20px;">
        <a href='./duplicates_index.php'>BACK</a>
      </div>
      <div style="float: left;">
        <a href="/">HOME</a>
      </div>
    </div>
  </body>
</html>