<?php 
  include '../helpers/session_helpers.php';
  session_start();
  require_security_level(64);
?>

<!DOCTYPE HTML>
<html>
  <meta name="viewport" content="width=device-width">
  <meta name="viewport" content="initial-scale=1.0">
  <head>
    <link rel="icon" type="image/png" href="favicon.png" />
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

  $q = "
        SELECT 
            firstname, surname, org, COUNT(*) AS dup_count, organisations.name AS org_name
        FROM
            members
        JOIN organisations 
        ON members.org = organisations.id
        GROUP BY firstname , surname , org
        HAVING COUNT(*) > 1";
?>

  <body>
    <table>
      <thead>
        <tr>
          <th>firstname</th>
          <th>surname</th>
          <th>org</th>
          <th>count</th>
        </tr>
      </thead>
      <tbody>
<?php
  $tr_class = 'even';
  $result = mysqli_query($con,$q);
  while($row = $result->fetch_assoc())
  {
    $tr_class = ($tr_class == 'even') ? 'odd' : 'even';
?>
      <tr class='<?php echo $tr_class ?>'>
        <td><?php echo $row['firstname'] ?></td>
        <td><?php echo $row['surname'] ?></td>
        <td><?php echo $row['org_name'] ?></td>
<?php
      $firstname=urlencode($row['firstname']);
      $surname=urldecode($row['surname']);
      $query = "firstname={$firstname}&surname={$surname}&org={$row['org']}"
?>
        <td>
          <a href='./duplicates_show.php?<?php echo $query ?>'><?php echo $row['dup_count'] ?></a>
        </td>
      </tr>
<?php
  }
  mysqli_free_result($result);
  mysqli_close($con);
?>
      </tbody>
    </table>
    <div>
      <p><a href="/">HOME</a></p>
    </div>
  </body>
</html>