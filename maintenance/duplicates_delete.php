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
        $con->query("UPDATE flights SET pic = {$genuine_id} WHERE pic={$id}");
        $con->query("UPDATE flights SET p2 = {$genuine_id} WHERE p2={$id}");
        $con->query("UPDATE flights SET towpilot = {$genuine_id} WHERE towpilot={$id}");
        $con->query("UPDATE role_member SET member_id = {$genuine_id} WHERE member_id={$id}");
        $con->query("UPDATE texts SET txt_member_id = {$genuine_id} WHERE txt_member_id={$id}");

        $con->query("DELETE FROM members WHERE id = {$id}");
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