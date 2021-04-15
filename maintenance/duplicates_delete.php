<?php
 session_start();
 require_once "../includes/classSecure.php";
 include '../helpers/session_helpers.php';
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
</head>

<?php
function purge() {
    global $DB;
    global $current_org;

    if(is_null($_POST['ids'])) {
        return "Member ids are mandatory!";
    }

    if(is_null($_POST['genuine_id'])) {
        return "One member has to selected as the genuine one!";
    }

    $ids = explode(',', $_POST['ids']);
    $genuine_id = $_POST['genuine_id'];


    try {
        if ($DB->countOrgsNotMineFromList($current_org,$_POST['ids']))
            die('You can only manage members in your own organisation.');
        $DB->BeginTransaction();
        foreach ($ids as $id)
        {
            if($id == $genuine_id)
            {
                continue;
            }

            if (! $DB->replaceFlightsMemberWith($id, $genuine_id) )
                $DB->TransactionError();
            if ( ! $DB->deleteRoleMemberDuplicate($id, $genuine_id) )
                $DB->TransactionError();
            if ( ! $DB->replaceRoleMemberMemberWith($id, $genuine_id) )
                $DB->TransactionError();
            if (! $DB->replaceTextsMemberWith($id, $genuine_id) )
                $DB->TransactionError();
            if (! $DB->replaceAuditMemberWith($id, $genuine_id) )
                $DB->TransactionError();
            if (! $DB->replaceBookingsMemberWith($id, $genuine_id) )
                $DB->TransactionError();
            if (! $DB->replaceDutyMemberWith($id, $genuine_id) )
                $DB->TransactionError();
            if (! $DB->replaceGroupMemberWith($id, $genuine_id) )
                $DB->TransactionError();
            if (! $DB->replaceSchemeSubsMemberWith($id, $genuine_id) )
                $DB->TransactionError();
            if (! $DB->replaceUsersMemberWith($id, $genuine_id) )
                $DB->TransactionError();
            if ( ! $DB->deleteUser($id) )
                $DB->TransactionError();
        }
        $DB->EndTransaction();
    }
    catch (Exception $e) {
        $DB->EndTransaction();
        return "Exception in deleting duplicate member {$e->getMessage()}, refer error log";
    }

    //Create the audit
    //We have this outside the transaction as we dont want the failure of a audit record creation prevent the real work.
    $DB->creatAudit("Merged member with id {$id} into member with id {$genuine_id}",$_SESSION['userid']);

    return NULL;
}

  $error = purge();
?>

    <body>
        <div style="width: 100%">
        <?php
        if ($error)
            echo "<p>{$error}</p>";
        else
            echo "<p>Success</p>";
        ?>
        </div>
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
