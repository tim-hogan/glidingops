<?php 
  include '../helpers/session_helpers.php';
  session_start();
  require_security_level(64);
  $current_org = current_org();
?>

<!DOCTYPE HTML>
<html>
  <meta name="viewport" content="width=device-width">
  <meta name="viewport" content="initial-scale=1.0">
  <head>
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
  $con_params = require('../config/database.php'); $con_params = $con_params['gliding']; 
  $con=mysqli_connect($con_params['hostname'],$con_params['username'],
                      $con_params['password'],$con_params['dbname']);
  $firstname = urldecode($_GET['firstname']);
  $surname = urldecode($_GET['surname']);
  $org = $_GET['org'];

  if($org != $current_org) {
    die('You can only delete members from your own organisation.');
  }

  $columns = array("id", "create_time", "member_id", 
                    "firstname", "surname", 
                    "org_name", "date_of_birth", "phone_home", 
                    "phone_mobile",
                    "phone_work",
                    "email", "class", "status", "gnz_number");
  
  $q =<<<SQL
    SELECT members.*, organisations.name AS org_name
    FROM
        members
        JOIN organisations 
        ON members.org = organisations.id
        WHERE members.firstname = "{$firstname}"
        AND   members.surname   = "{$surname}"
        AND   members.org       = {$org};
SQL;
?>

  <body>
    <form action="./duplicates_delete.php" method="post">
    <table>
      <thead>
        <tr>
          <?php foreach ($columns as $column): ?>
          <th><?php echo $column ?></th>
          <?php endforeach ?>
          <th>Keep</th>
        </tr>
      </thead>
      <tbody>
<?php
  $tr_class = 'even';
  $ids = array();
  $result = mysqli_query($con,$q);
  while($row = $result->fetch_assoc())
  {
    $tr_class = ($tr_class == 'even') ? 'odd' : 'even';
    // print_r(array_keys($row));
?>
    <tr class='<?php echo $tr_class ?>'>
    <?php foreach ($columns as $column): ?>
      <td><?php echo $row[$column] ?></td>
    <?php endforeach ?>
      <td>
        <input type='radio' name='genuine_id' value='<?php echo $row['id'] ?>' onClick='genuineIdChanged();'/>
      </td>
    </tr>
<?php
    array_push($ids, $row['id']);
  }
  mysqli_free_result($result);
  mysqli_close($con);
?>
      </tbody>
    </table>
    <input type="hidden" name="ids" value="<?php echo implode(",", $ids) ?>"/>
    <input type="hidden" name="org" value="<?php echo implode(",", $org) ?>"/>
    <div style="margin-top: 10px;">
      <input type="submit" name="Clean" id="submit" disabled="true" style="font-size: x-large;" />
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