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
            $result = $DB->allDuplicateMembers($current_org);
            while($row = $result->fetch_assoc())
            {
                $tr_class = ($tr_class == 'even') ? 'odd' : 'even';
                echo "<tr class='{$tr_class}'>";
                    $strfirstname = htmlspecialchars($row['firstname']);
                    $strsurname = htmlspecialchars($row['surname']);
                    $strorg_name = htmlspecialchars($row['org_name']);
                    echo "<td>{$strfirstname}</td><td>{$strsurname}</td><td>{$strorg_name}</td>";
                    $firstname=urlencode($row['firstname']);
                    $surname=urlencode($row['surname']);
                    $e = FormList::encryptParam("firstname={$firstname}&surname={$surname}&org={$row['org']}");
                    echo "<td><a href='./duplicates_show.php?v={$e}'>{$row['dup_count']}</a></td>";
                echo "</tr>";
            }
        ?>
      </tbody>
    </table>
    <div>
      <p><a href="/">HOME</a></p>
    </div>
  </body>
</html>