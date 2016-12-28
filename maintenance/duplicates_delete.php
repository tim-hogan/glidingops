<?php 
  include '../helpers/session_helpers.php';
  include '../helpers/audit_helpers.php';
  session_start();
  require_security_level(64);
?>

<!DOCTYPE HTML>
<html>
  <meta name="viewport" content="width=device-width">
  <meta name="viewport" content="initial-scale=1.0">
  <head>
    <link rel="icon" type="image/png" href="favicon.png" />
  </head>

<?php
  function purge() {
    $con_params = require('../config/database.php'); $con_params = $con_params['gliding'];
    mysqli_report(MYSQLI_REPORT_ALL); 
    $con=mysqli_connect($con_params['hostname'],$con_params['username'],
                        $con_params['password'],$con_params['dbname']);

    
    if(is_null($_POST['ids'])) {
      return "Member ids are mandatory!";
    }

    if(is_null($_POST['genuine_id'])) {
      return "One member has to selected as the genuine one!";
    }

    $ids = explode(',', $_POST['ids']);
    $genuine_id = $_POST['genuine_id'];
    
    try {
      $con->begin_transaction();

      foreach ($ids as $id) {
        if($id == $genuine_id) {
          continue;
        }

        $con->query("UPDATE flights SET billing_member2 = {$genuine_id} WHERE billing_member2 = {$id}");
        $con->query("UPDATE flights SET billing_member1 = {$genuine_id} WHERE billing_member1 = {$id}");
        $con->query("UPDATE flights SET pic = {$genuine_id} WHERE pic={$id}");
        $con->query("UPDATE flights SET p2 = {$genuine_id} WHERE p2={$id}");
        $con->query("UPDATE flights SET towpilot = {$genuine_id} WHERE towpilot={$id}");
        
        $con->query("UPDATE role_member SET member_id = {$genuine_id} WHERE member_id={$id}");
        $con->query("UPDATE texts SET txt_member_id = {$genuine_id} WHERE txt_member_id={$id}");

        $con->query("UPDATE audit SET memberid = {$genuine_id} WHERE memberid={$id}");

        $con->query("UPDATE bookings SET member = {$genuine_id} WHERE member={$id}");
        $con->query("UPDATE bookings SET instructor = {$genuine_id} WHERE instructor={$id}");

        $con->query("UPDATE duty SET member = {$genuine_id} WHERE member={$id}");

        $con->query("UPDATE group_member SET gm_member_id = {$genuine_id} WHERE gm_member_id={$id}");

        $con->query("UPDATE scheme_subs SET member = {$genuine_id} WHERE member={$id}");

        $con->query("UPDATE users SET member = {$genuine_id} WHERE member={$id}");

        $con->query("DELETE FROM members WHERE id = {$id}");
        audit_log($con, "Merged member with id {$id} into member with id {$genuine_id}");
      }

      $con->commit();
    } catch(mysqli_sql_exception $e) {
      $con->rollback();
      return "Error: {$e->getMessage()}; Code: {$e->getCode()}";
    } finally {
      mysqli_close($con);
    }

    return NULL;
  }

  $error = purge();
?>

  <body>
    <div style="width: 100%">
    <?php if (!is_null(($error))): ?>
      <p><?php echo $error ?></p>
    <?php else: ?>
      <p>Success</p>
    <?php endif ?>
    </div>
    <div style="width: 100%">
      <a href='./duplicates_index.php'>BACK</a>
    </div>
  </body>
</html>

<?php
/**
USE information_schema;
SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
FROM
  KEY_COLUMN_USAGE
WHERE
  REFERENCED_TABLE_NAME = 'members'
  AND REFERENCED_COLUMN_NAME = 'id'
  AND CONSTRAINT_SCHEMA = 'gliding'; 

+---------------------+--------------+-----------------+-----------------------+------------------------+
| CONSTRAINT_NAME     | TABLE_NAME   | COLUMN_NAME     | REFERENCED_TABLE_NAME | REFERENCED_COLUMN_NAME |
+---------------------+--------------+-----------------+-----------------------+------------------------+
| ✓ audit_ibfk_2        | audit        | memberid        | members               | id                     |
| ✓ bookings_ibfk_3     | bookings     | member          | members               | id                     |
| ✓ bookings_ibfk_5     | bookings     | instructor      | members               | id                     |
| ✓ duty_ibfk_2         | duty         | member          | members               | id                     |
| ✓ flights_ibfk_10     | flights      | billing_member2 | members               | id                     |
| ✓ flights_ibfk_5      | flights      | towpilot        | members               | id                     |
| ✓ flights_ibfk_6      | flights      | pic             | members               | id                     |
| ✓ flights_ibfk_7      | flights      | p2              | members               | id                     |
| ✓ flights_ibfk_9      | flights      | billing_member1 | members               | id                     |
| ✓ group_member_ibfk_2 | group_member | gm_member_id    | members               | id                     |
| ✓ role_member_ibfk_3  | role_member  | member_id       | members               | id                     |
| ✓ scheme_subs_ibfk_2  | scheme_subs  | member          | members               | id                     |
| ✓ texts_ibfk_2        | texts        | txt_member_id   | members               | id                     |
| ✓ users_ibfk_2        | users        | member          | members               | id                     |
+---------------------+--------------+-----------------+-----------------------+------------------------+
**/
?>