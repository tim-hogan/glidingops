<?php
  function audit_log($con, $description) {
    $userid = $_SESSION['userid'];
    $memberid = $_SESSION['memberid'];

    $con->query("INSERT INTO audit (userid,memberid,description) 
                        VALUES ({$userid}, {$memberid}, '{$description}')");
  }
?>