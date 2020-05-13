<?php session_start(); ?>
<?php
include 'timehelpers.php';
$org=0;
if(isset($_SESSION['org'])) $org=$_SESSION['org'];
if(isset($_SESSION['security'])){
 if (!($_SESSION['security'] & 6)){die("Secruity level too low for this page");}
}else{
 header('Location: Login.php');
 die("Please logon");
}

$DEBUG=0;
$dateTimeZone = new DateTimeZone($_SESSION['timezone']);
$dateTime = new DateTime('now', $dateTimeZone);
$dateStr = $dateTime->format('Y-m-d');
$timeoffset = $dateTime->getOffset();
$pageid=11;
$errtext="";
$sqltext="";
$error=0;
$trantype="Create";
$recid=-1;
$id_f="";
$id_err="";
$member_id_f="";
$member_id_err="";
$firstname_f="";
$firstname_err="";
$surname_f="";
$surname_err="";
$displayname_f="";
$displayname_err="";
$date_of_birth_f=$dateStr;
$date_of_birth_err="";
$mem_addr1_f="";
$mem_addr1_err="";
$mem_addr2_f="";
$mem_addr2_err="";
$mem_addr3_f="";
$mem_addr3_err="";
$mem_addr4_f="";
$mem_addr4_err="";
$mem_city_f="";
$mem_city_err="";
$mem_country_f="";
$mem_country_err="";
$mem_postcode_f="";
$mem_postcode_err="";
$emerg_addr1_f="";
$emerg_addr1_err="";
$emerg_addr2_f="";
$emerg_addr2_err="";
$emerg_addr3_f="";
$emerg_addr3_err="";
$emerg_addr4_f="";
$emerg_addr4_err="";
$emerg_city_f="";
$emerg_city_err="";
$emerg_country_f="";
$emerg_country_err="";
$emerg_postcode_f="";
$emerg_postcode_err="";
$gnz_number_f="";
$gnz_number_err="";
$qgp_number_f="";
$qgp_number_err="";
$class_f="";
$class_err="";
$status_f="";
$status_err="";
$phone_home_f="";
$phone_home_err="";
$phone_mobile_f="";
$phone_mobile_err="";
$phone_work_f="";
$phone_work_err="";
$email_f="";
$email_err="";
$gone_solo_f="";
$gone_solo_err="";
$enable_text_f="";
$enable_text_err="";
$enable_email_f="";
$enable_email_err="";
$medical_expire_f=$dateStr;
$medical_expire_err="";
$icr_expire_f=$dateStr;
$icr_expire_err="";
$bfr_expire_f=$dateStr;
$bfr_expire_err="";
$official_observer_f="";
$official_observer_err="";
$first_aider_f="";
$first_aider_err="";
function InputChecker($data)
{
 $data = trim($data);
 $data = stripslashes($data);
 $data = htmlspecialchars($data);
 return $data;
}
if ($_SERVER["REQUEST_METHOD"] == "GET")
{
 if(isset($_GET['id']))
 {
  $recid = $_GET['id'];
  if ($recid >= 0)
  {
   $trantype="Update";
   $con_params = require('./config/database.php'); $con_params = $con_params['gliding'];
   $con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (mysqli_connect_errno())
   {
    $errtext= "Failed to connect to Database: " . mysqli_connect_error();
   }
   else
   {
    $q = "SELECT * FROM members WHERE id = " . $recid ;
    $r = mysqli_query($con,$q);
    $row = mysqli_fetch_array($r);
    if ($_SESSION['org'] > 0 && $row['org'] != $_SESSION['org'])
       die("Record does not exist");
    $id_f = $row['id'];
    $member_id_f = $row['member_id'];
    $firstname_f = htmlspecialchars($row['firstname'],ENT_QUOTES);
    $surname_f = htmlspecialchars($row['surname'],ENT_QUOTES);
    $displayname_f = htmlspecialchars($row['displayname'],ENT_QUOTES);
    $date_of_birth_f = $row['date_of_birth'];
    $mem_addr1_f = htmlspecialchars($row['mem_addr1'],ENT_QUOTES);
    $mem_addr2_f = htmlspecialchars($row['mem_addr2'],ENT_QUOTES);
    $mem_addr3_f = htmlspecialchars($row['mem_addr3'],ENT_QUOTES);
    $mem_addr4_f = htmlspecialchars($row['mem_addr4'],ENT_QUOTES);
    $mem_city_f = htmlspecialchars($row['mem_city'],ENT_QUOTES);
    $mem_country_f = htmlspecialchars($row['mem_country'],ENT_QUOTES);
    $mem_postcode_f = htmlspecialchars($row['mem_postcode'],ENT_QUOTES);
    $emerg_addr1_f = htmlspecialchars($row['emerg_addr1'],ENT_QUOTES);
    $emerg_addr2_f = htmlspecialchars($row['emerg_addr2'],ENT_QUOTES);
    $emerg_addr3_f = htmlspecialchars($row['emerg_addr3'],ENT_QUOTES);
    $emerg_addr4_f = htmlspecialchars($row['emerg_addr4'],ENT_QUOTES);
    $emerg_city_f = htmlspecialchars($row['emerg_city'],ENT_QUOTES);
    $emerg_country_f = htmlspecialchars($row['emerg_country'],ENT_QUOTES);
    $emerg_postcode_f = htmlspecialchars($row['emerg_postcode'],ENT_QUOTES);
    $gnz_number_f = $row['gnz_number'];
    $qgp_number_f = $row['qgp_number'];
    $class_f = $row['class'];
    $status_f = $row['status'];
    $phone_home_f = htmlspecialchars($row['phone_home'],ENT_QUOTES);
    $phone_mobile_f = htmlspecialchars($row['phone_mobile'],ENT_QUOTES);
    $phone_work_f = htmlspecialchars($row['phone_work'],ENT_QUOTES);
    $email_f = htmlspecialchars($row['email'],ENT_QUOTES);
    $gone_solo_f = $row['gone_solo'];
    $enable_text_f = $row['enable_text'];
    $enable_email_f = $row['enable_email'];
    $medical_expire_f = $row['medical_expire'];
    $icr_expire_f = $row['icr_expire'];
    $bfr_expire_f = $row['bfr_expire'];
    $official_observer_f = $row['official_observer'];
    $first_aider_f = $row['first_aider'];
    $roleIds = [];
    if($recid != null) {
      $userRoles = App\Models\Member::find($recid)->roles;
      $roleIds = $userRoles->map(function($role){ return $role->id; })->all();
    }

    mysqli_close($con);
   }
  }
 }
}
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
 $error=0;
 $id_err = "";
 $member_id_err = "";
 $org_err = "";
 $create_time_err = "";
 $effective_from_err = "";
 $firstname_err = "";
 $surname_err = "";
 $displayname_err = "";
 $date_of_birth_err = "";
 $mem_addr1_err = "";
 $mem_addr2_err = "";
 $mem_addr3_err = "";
 $mem_addr4_err = "";
 $mem_city_err = "";
 $mem_country_err = "";
 $mem_postcode_err = "";
 $emerg_addr1_err = "";
 $emerg_addr2_err = "";
 $emerg_addr3_err = "";
 $emerg_addr4_err = "";
 $emerg_city_err = "";
 $emerg_country_err = "";
 $emerg_postcode_err = "";
 $gnz_number_err = "";
 $qgp_number_err = "";
 $class_err = "";
 $status_err = "";
 $phone_home_err = "";
 $phone_mobile_err = "";
 $phone_work_err = "";
 $email_err = "";
 $gone_solo_err = "";
 $enable_text_err = "";
 $enable_email_err = "";
 $medical_expire_err = "";
 $icr_expire_err = "";
 $bfr_expire_err = "";
 $official_observer_err = "";
 $first_aider_err = "";
 $localdate_lastemail_err = "";
 $trantype=$_POST["tran"];
 $roleIds = $_POST["roles"];
 if(isset($_POST['updateid'])){
  $recid = $_POST['updateid'];
 }
 $member_id_f = InputChecker($_POST["member_id_i"]);
 if (!empty($member_id_f ) ) {if (!is_numeric($member_id_f ) ) {$member_id_err = "MEMBER NUM is not numeric";$error = 1;}}
 $firstname_f = InputChecker($_POST["firstname_i"]);
 if (empty($firstname_f) )
 {
  $firstname_err = "FIRSTNAME is required";
  $error = 1;
 }
 $surname_f = InputChecker($_POST["surname_i"]);
 if (empty($surname_f) )
 {
  $surname_err = "SURNAME is required";
  $error = 1;
 }
 $displayname_f = InputChecker($_POST["displayname_i"]);
 if (empty($displayname_f) )
 {
  $displayname_err = "DISPLAY NAME is required";
  $error = 1;
 }
 $date_of_birth_f = InputChecker($_POST["date_of_birth_i"]);
 $mem_addr1_f = InputChecker($_POST["mem_addr1_i"]);
 $mem_addr2_f = InputChecker($_POST["mem_addr2_i"]);
 $mem_addr3_f = InputChecker($_POST["mem_addr3_i"]);
 $mem_addr4_f = InputChecker($_POST["mem_addr4_i"]);
 $mem_city_f = InputChecker($_POST["mem_city_i"]);
 $mem_country_f = InputChecker($_POST["mem_country_i"]);
 $mem_postcode_f = InputChecker($_POST["mem_postcode_i"]);
 $emerg_addr1_f = InputChecker($_POST["emerg_addr1_i"]);
 $emerg_addr2_f = InputChecker($_POST["emerg_addr2_i"]);
 $emerg_addr3_f = InputChecker($_POST["emerg_addr3_i"]);
 $emerg_addr4_f = InputChecker($_POST["emerg_addr4_i"]);
 $emerg_city_f = InputChecker($_POST["emerg_city_i"]);
 $emerg_country_f = InputChecker($_POST["emerg_country_i"]);
 $emerg_postcode_f = InputChecker($_POST["emerg_postcode_i"]);
 $gnz_number_f = InputChecker($_POST["gnz_number_i"]);
 if (!empty($gnz_number_f ) ) {if (!is_numeric($gnz_number_f ) ) {$gnz_number_err = "GNZ NUMBER is not numeric";$error = 1;}}
 $qgp_number_f = InputChecker($_POST["qgp_number_i"]);
 if (!empty($qgp_number_f ) ) {if (!is_numeric($qgp_number_f ) ) {$qgp_number_err = "QGP NUMBER is not numeric";$error = 1;}}
 $class_f = InputChecker($_POST["class_i"]);
 $status_f = InputChecker($_POST["status_i"]);
 $phone_home_f = InputChecker($_POST["phone_home_i"]);
 $phone_mobile_f = InputChecker($_POST["phone_mobile_i"]);
 $phone_work_f = InputChecker($_POST["phone_work_i"]);
 $email_f = InputChecker($_POST["email_i"]);
if(is_array($_POST['gone_solo_i']) && in_array("1",$_POST['gone_solo_i']))
 $gone_solo_f = 1;
else
 $gone_solo_f = 0;
 if (!empty($gone_solo_f ) ) {if (!is_numeric($gone_solo_f ) ) {$gone_solo_err = "SOLO is not numeric";$error = 1;}}
if(is_array($_POST['enable_text_i']) && in_array("1",$_POST['enable_text_i']))
 $enable_text_f = 1;
else
 $enable_text_f = 0;
 if (!empty($enable_text_f ) ) {if (!is_numeric($enable_text_f ) ) {$enable_text_err = "ENABLE TEXTS is not numeric";$error = 1;}}
if(is_array($_POST['enable_email_i']) && in_array("1",$_POST['enable_email_i']))
 $enable_email_f = 1;
else
 $enable_email_f = 0;
 if (!empty($enable_email_f ) ) {if (!is_numeric($enable_email_f ) ) {$enable_email_err = "ENABLE EMALS is not numeric";$error = 1;}}
if ($_SESSION['security'] & 16) { $medical_expire_f = InputChecker($_POST["medical_expire_i"]);
}
if ($_SESSION['security'] & 16) { $icr_expire_f = InputChecker($_POST["icr_expire_i"]);
}
if ($_SESSION['security'] & 16) { $bfr_expire_f = InputChecker($_POST["bfr_expire_i"]);
}
if(is_array($_POST['official_observer_i']) && in_array("1",$_POST['official_observer_i']))
 $official_observer_f = 1;
else
 $official_observer_f = 0;
if(is_array($_POST['first_aider_i']) && in_array("1",$_POST['first_aider_i']))
 $first_aider_f = 1;
else
 $first_aider_f = 0;
 if ($error != 1)
 {
$con_params = require('./config/database.php'); $con_params = $con_params['gliding'];
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (mysqli_connect_errno())
   {
    $errtext= "Failed to connect to Database: " . mysqli_connect_error();
   }
   else
   {
     $Q = "";
     if (isset($_POST["del"]) ) {if ($_POST["del"] == "Delete"){
       $Q="DELETE FROM members WHERE id = " . $_POST['updateid'] ;}}
     if (isset($_POST["tran"]) ) {if ($_POST["tran"] == "Update"){
      $Q = "UPDATE members SET ";
      $Q .= "member_id=";
      $Q .= "'" . $member_id_f . "'";
      $Q .= ",firstname=";
      $Q .= "'" . mysqli_real_escape_string($con, $firstname_f)  . "'";
      $Q .= ",surname=";
      $Q .= "'" . mysqli_real_escape_string($con, $surname_f)  . "'";
      $Q .= ",displayname=";
      $Q .= "'" . mysqli_real_escape_string($con, $displayname_f)  . "'";
      $Q .= ",date_of_birth=";
      $Q .= "'" . $date_of_birth_f . "'";
      $Q .= ",mem_addr1=";
      $Q .= "'" . mysqli_real_escape_string($con, $mem_addr1_f)  . "'";
      $Q .= ",mem_addr2=";
      $Q .= "'" . mysqli_real_escape_string($con, $mem_addr2_f)  . "'";
      $Q .= ",mem_addr3=";
      $Q .= "'" . mysqli_real_escape_string($con, $mem_addr3_f)  . "'";
      $Q .= ",mem_addr4=";
      $Q .= "'" . mysqli_real_escape_string($con, $mem_addr4_f)  . "'";
      $Q .= ",mem_city=";
      $Q .= "'" . mysqli_real_escape_string($con, $mem_city_f)  . "'";
      $Q .= ",mem_country=";
      $Q .= "'" . mysqli_real_escape_string($con, $mem_country_f)  . "'";
      $Q .= ",mem_postcode=";
      $Q .= "'" . mysqli_real_escape_string($con, $mem_postcode_f)  . "'";
      $Q .= ",emerg_addr1=";
      $Q .= "'" . mysqli_real_escape_string($con, $emerg_addr1_f)  . "'";
      $Q .= ",emerg_addr2=";
      $Q .= "'" . mysqli_real_escape_string($con, $emerg_addr2_f)  . "'";
      $Q .= ",emerg_addr3=";
      $Q .= "'" . mysqli_real_escape_string($con, $emerg_addr3_f)  . "'";
      $Q .= ",emerg_addr4=";
      $Q .= "'" . mysqli_real_escape_string($con, $emerg_addr4_f)  . "'";
      $Q .= ",emerg_city=";
      $Q .= "'" . mysqli_real_escape_string($con, $emerg_city_f)  . "'";
      $Q .= ",emerg_country=";
      $Q .= "'" . mysqli_real_escape_string($con, $emerg_country_f)  . "'";
      $Q .= ",emerg_postcode=";
      $Q .= "'" . mysqli_real_escape_string($con, $emerg_postcode_f)  . "'";
      $Q .= ",gnz_number=";
      $Q .= "'" . $gnz_number_f . "'";
      $Q .= ",qgp_number=";
      $Q .= "'" . $qgp_number_f . "'";
      $Q .= ",class=";
       if ($class_f ==0) $Q .= "null"; else {$Q .= "'";$Q .= "$class_f"; $Q .= "'"; }
      $Q .= ",status=";
       if ($status_f ==0) $Q .= "null"; else {$Q .= "'";$Q .= "$status_f"; $Q .= "'"; }
      $Q .= ",phone_home=";
      $Q .= "'" . mysqli_real_escape_string($con, $phone_home_f)  . "'";
      $Q .= ",phone_mobile=";
      $Q .= "'" . mysqli_real_escape_string($con, $phone_mobile_f)  . "'";
      $Q .= ",phone_work=";
      $Q .= "'" . mysqli_real_escape_string($con, $phone_work_f)  . "'";
      $Q .= ",email=";
      $Q .= "'" . mysqli_real_escape_string($con, $email_f)  . "'";
      $Q .= ",gone_solo=";
      $Q .= "'" . $gone_solo_f . "'";
      $Q .= ",enable_text=";
      $Q .= "'" . $enable_text_f . "'";
      $Q .= ",enable_email=";
      $Q .= "'" . $enable_email_f . "'";
if ($_SESSION['security'] & 16) {      $Q .= ",medical_expire=";
      $Q .= "'" . $medical_expire_f . "'";
}
if ($_SESSION['security'] & 16) {      $Q .= ",icr_expire=";
      $Q .= "'" . $icr_expire_f . "'";
}
if ($_SESSION['security'] & 16) {      $Q .= ",bfr_expire=";
      $Q .= "'" . $bfr_expire_f . "'";
}
      $Q .= ",official_observer=";
      $Q .= "'" . $official_observer_f . "'";
      $Q .= ",first_aider=";
      $Q .= "'" . $first_aider_f . "'";
      $Q .= " WHERE ";$Q .= "id ";$Q .= "= ";
$Q .= $_POST['updateid'];
}
     else
     if ($_POST["tran"] == "Create"){
       $Q = "INSERT INTO members (";$Q .= "member_id";$Q .= ", org";$Q .= ", firstname";$Q .= ", surname";$Q .= ", displayname";$Q .= ", date_of_birth";$Q .= ", mem_addr1";$Q .= ", mem_addr2";$Q .= ", mem_addr3";$Q .= ", mem_addr4";$Q .= ", mem_city";$Q .= ", mem_country";$Q .= ", mem_postcode";$Q .= ", emerg_addr1";$Q .= ", emerg_addr2";$Q .= ", emerg_addr3";$Q .= ", emerg_addr4";$Q .= ", emerg_city";$Q .= ", emerg_country";$Q .= ", emerg_postcode";$Q .= ", gnz_number";$Q .= ", qgp_number";$Q .= ", class";$Q .= ", status";$Q .= ", phone_home";$Q .= ", phone_mobile";$Q .= ", phone_work";$Q .= ", email";$Q .= ", gone_solo";$Q .= ", enable_text";$Q .= ", enable_email";if ($_SESSION['security'] & 16) $Q .= ", medical_expire";if ($_SESSION['security'] & 16) $Q .= ", icr_expire";if ($_SESSION['security'] & 16) $Q .= ", bfr_expire";$Q .= ", official_observer";$Q .= ", first_aider";$Q .= " ) VALUES (";
       $Q .= "'" . $member_id_f . "'";
       $Q.= ",";
$Q.=$_SESSION['org'];       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $firstname_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $surname_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $displayname_f) . "'";
       $Q.= ",";
       $Q .= "'" . $date_of_birth_f . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $mem_addr1_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $mem_addr2_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $mem_addr3_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $mem_addr4_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $mem_city_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $mem_country_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $mem_postcode_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $emerg_addr1_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $emerg_addr2_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $emerg_addr3_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $emerg_addr4_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $emerg_city_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $emerg_country_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $emerg_postcode_f) . "'";
       $Q.= ",";
       $Q .= "'" . $gnz_number_f . "'";
       $Q.= ",";
       $Q .= "'" . $qgp_number_f . "'";
       $Q.= ",";
       if ($class_f ==0)$Q .= "null"; else{$Q .= "'";$Q .= $class_f;$Q .= "'";}
       $Q.= ",";
       if ($status_f ==0)$Q .= "null"; else{$Q .= "'";$Q .= $status_f;$Q .= "'";}
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $phone_home_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $phone_mobile_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $phone_work_f) . "'";
       $Q.= ",";
       $Q .= "'" . mysqli_real_escape_string($con, $email_f) . "'";
       $Q.= ",";
       $Q .= "'" . $gone_solo_f . "'";
       $Q.= ",";
       $Q .= "'" . $enable_text_f . "'";
       $Q.= ",";
       $Q .= "'" . $enable_email_f . "'";
if ($_SESSION['security'] & 16) {       $Q.= ",";
       $Q .= "'" . $medical_expire_f . "'";
}
if ($_SESSION['security'] & 16) {       $Q.= ",";
       $Q .= "'" . $icr_expire_f . "'";
}
if ($_SESSION['security'] & 16) {       $Q.= ",";
       $Q .= "'" . $bfr_expire_f . "'";
}
       $Q.= ",";
       $Q .= "'" . $official_observer_f . "'";
       $Q.= ",";
       $Q .= "'" . $first_aider_f . "'";
      $Q.= ")";
    }}
    $sqltext = $Q;
    if(isset($_POST["del"])) {
      $member =  App\Models\Member::find($recid);
      $member->roles()->sync([]);
    }
    if(!mysqli_query($con,$Q) )
    {
       $errtext = "Database entry: " . mysqli_error($con) . "<br>" . $Q;
    } else {
      if($_POST["tran"] == "Create") {
        $recid = $con->insert_id;
      }

      if(!isset($_POST["del"]) && isset($_POST["roles"])) {
        // Update Roles collection from roles[] POST parameter
        if (is_array($roleIds)) {
          $member =  App\Models\Member::find($recid);
          $member->roles()->sync($roleIds);
        }
      }

      if(isset($_POST["del"])){
        header("Location: AllMembers");
      } else {
        header("Location: Member?id={$recid}");
      }
      exit();
    }
    mysqli_close($con);
  }
 }
$firstname_f=htmlspecialchars($firstname_f,ENT_QUOTES);
$surname_f=htmlspecialchars($surname_f,ENT_QUOTES);
$displayname_f=htmlspecialchars($displayname_f,ENT_QUOTES);
$mem_addr1_f=htmlspecialchars($mem_addr1_f,ENT_QUOTES);
$mem_addr2_f=htmlspecialchars($mem_addr2_f,ENT_QUOTES);
$mem_addr3_f=htmlspecialchars($mem_addr3_f,ENT_QUOTES);
$mem_addr4_f=htmlspecialchars($mem_addr4_f,ENT_QUOTES);
$mem_city_f=htmlspecialchars($mem_city_f,ENT_QUOTES);
$mem_country_f=htmlspecialchars($mem_country_f,ENT_QUOTES);
$mem_postcode_f=htmlspecialchars($mem_postcode_f,ENT_QUOTES);
$emerg_addr1_f=htmlspecialchars($emerg_addr1_f,ENT_QUOTES);
$emerg_addr2_f=htmlspecialchars($emerg_addr2_f,ENT_QUOTES);
$emerg_addr3_f=htmlspecialchars($emerg_addr3_f,ENT_QUOTES);
$emerg_addr4_f=htmlspecialchars($emerg_addr4_f,ENT_QUOTES);
$emerg_city_f=htmlspecialchars($emerg_city_f,ENT_QUOTES);
$emerg_country_f=htmlspecialchars($emerg_country_f,ENT_QUOTES);
$emerg_postcode_f=htmlspecialchars($emerg_postcode_f,ENT_QUOTES);
$phone_home_f=htmlspecialchars($phone_home_f,ENT_QUOTES);
$phone_mobile_f=htmlspecialchars($phone_mobile_f,ENT_QUOTES);
$phone_work_f=htmlspecialchars($phone_work_f,ENT_QUOTES);
$email_f=htmlspecialchars($email_f,ENT_QUOTES);
}

// =========== USER ROLES ===============
$allRoles = App\Models\Role::all();
$userRoles = App\Models\Role::find($roleIds);
?>
<!DOCTYPE HTML>
<html>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<head>
<style><?php $inc = "./orgs/" . $org . "/heading2.css"; include $inc; ?></style>
<style>
<?php $inc = "./orgs/" . $org . "/menu1.css"; include $inc; ?></style>
<link rel="stylesheet" type="text/css" href="styleform1.css">
</head>
<body>
<?php include __DIR__.'/helpers/dev_mode_banner.php' ?>
<?php $inc = "./orgs/" . $org . "/heading2.txt"; include $inc; ?>
<?php $inc = "./orgs/" . $org . "/menu1.txt"; include $inc; ?>
<script>function goBack() {window.location = 'AllMembers'}</script>
<div id='divform'>
<form method="post" action="<?php echo htmlspecialchars('./Member');?>">
<table>
<?php if (true)
{
echo "<tr><td class='desc'>ID</td><td></td>";
echo "<td>";
echo $id_f; echo "</td>";echo "<td>";
echo $id_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>MEMBER NUM</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($member_id_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='member_id_i' ";echo "Value='";echo $member_id_f;echo "' ";echo " autofocus ";echo ">";echo "</td>";echo "<td>";
echo $member_id_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>FIRSTNAME</td><td>*</td>";
echo "<td>";
echo "<input ";
  if (strlen($firstname_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='firstname_i' ";echo "size='30' ";echo "Value='";echo $firstname_f;echo "' ";echo "maxlength='40'";echo "><span class='field-error-msg'>{$firstname_err}</span>";echo "</td>";echo "<td>"; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>SURNAME</td><td>*</td>";
echo "<td>";
echo "<input ";
if (strlen($surname_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='surname_i' ";echo "size='30' ";echo "Value='";echo $surname_f;echo "' ";echo "maxlength='40'";echo "><span class='field-error-msg'>{$surname_err}</span>";echo "</td>";echo "<td></td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>DISPLAY NAME</td><td>*</td>";
echo "<td>";
echo "<input ";
if (strlen($displayname_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='displayname_i' ";echo "size='40' ";echo "Value='";echo $displayname_f;echo "' ";echo "maxlength='80'";echo "><span class='field-error-msg'>{$displayname_err}</span>";echo "</td>";echo "<td></td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>DATE OF BIRTH</td><td></td>";
echo "<td><input type='date' name='date_of_birth_i' Value='" . substr($date_of_birth_f,0,10) . "'></td>";
echo "<td>";
echo $date_of_birth_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>POSTAL ADDRESS</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($mem_addr1_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='mem_addr1_i' ";echo "Value='";echo $mem_addr1_f;echo "' ";echo "maxlength='45'";echo ">";echo "</td>";echo "<td>";
echo $mem_addr1_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'></td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($mem_addr2_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='mem_addr2_i' ";echo "Value='";echo $mem_addr2_f;echo "' ";echo "maxlength='45'";echo ">";echo "</td>";echo "<td>";
echo $mem_addr2_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'></td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($mem_addr3_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='mem_addr3_i' ";echo "Value='";echo $mem_addr3_f;echo "' ";echo "maxlength='45'";echo ">";echo "</td>";echo "<td>";
echo $mem_addr3_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'></td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($mem_addr4_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='mem_addr4_i' ";echo "Value='";echo $mem_addr4_f;echo "' ";echo "maxlength='45'";echo ">";echo "</td>";echo "<td>";
echo $mem_addr4_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>CITY</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($mem_city_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='mem_city_i' ";echo "Value='";echo $mem_city_f;echo "' ";echo "maxlength='45'";echo ">";echo "</td>";echo "<td>";
echo $mem_city_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>COUNTRY</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($mem_country_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='mem_country_i' ";echo "Value='";echo $mem_country_f;echo "' ";echo "maxlength='45'";echo ">";echo "</td>";echo "<td>";
echo $mem_country_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>POSTCODE</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($mem_postcode_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='mem_postcode_i' ";echo "Value='";echo $mem_postcode_f;echo "' ";echo "maxlength='45'";echo ">";echo "</td>";echo "<td>";
echo $mem_postcode_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>EMERGENCY CONTACT ADDRESS</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($emerg_addr1_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='emerg_addr1_i' ";echo "Value='";echo $emerg_addr1_f;echo "' ";echo "maxlength='45'";echo ">";echo "</td>";echo "<td>";
echo $emerg_addr1_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'></td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($emerg_addr2_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='emerg_addr2_i' ";echo "Value='";echo $emerg_addr2_f;echo "' ";echo "maxlength='45'";echo ">";echo "</td>";echo "<td>";
echo $emerg_addr2_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'></td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($emerg_addr3_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='emerg_addr3_i' ";echo "Value='";echo $emerg_addr3_f;echo "' ";echo "maxlength='45'";echo ">";echo "</td>";echo "<td>";
echo $emerg_addr3_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'></td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($emerg_addr4_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='emerg_addr4_i' ";echo "Value='";echo $emerg_addr4_f;echo "' ";echo "maxlength='45'";echo ">";echo "</td>";echo "<td>";
echo $emerg_addr4_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>CITY</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($emerg_city_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='emerg_city_i' ";echo "Value='";echo $emerg_city_f;echo "' ";echo "maxlength='45'";echo ">";echo "</td>";echo "<td>";
echo $emerg_city_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>COUNTRY</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($emerg_country_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='emerg_country_i' ";echo "Value='";echo $emerg_country_f;echo "' ";echo "maxlength='45'";echo ">";echo "</td>";echo "<td>";
echo $emerg_country_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>POSTCODE</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($emerg_postcode_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='emerg_postcode_i' ";echo "Value='";echo $emerg_postcode_f;echo "' ";echo "maxlength='45'";echo ">";echo "</td>";echo "<td>";
echo $emerg_postcode_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>GNZ NUMBER</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($gnz_number_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='gnz_number_i' ";echo "size='10' ";echo "Value='";echo $gnz_number_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $gnz_number_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>QGP NUMBER</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($qgp_number_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='qgp_number_i' ";echo "size='10' ";echo "Value='";echo $qgp_number_f;echo "' ";echo ">";echo "</td>";echo "<td>";
echo $qgp_number_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>CLASS</td><td></td>";
echo "<td><select name='class_i'>";
$con_params = require('./config/database.php'); $con_params = $con_params['gliding'];
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (mysqli_connect_errno())
   {
    $errtext= "Failed to connect to Database: " . mysqli_connect_error();
   }
   else
   {
     echo "<option value='0'></option>";
     $qs= "SELECT * FROM membership_class";if($_SESSION['org'] > 0) {$qs .= " WHERE membership_class.org = " . $_SESSION['org'] . " ";}$qs .= " ORDER BY class ASC";
     $r = mysqli_query($con,$qs);
     while($row = mysqli_fetch_array($r))
     {
     if ($class_f == $row['id'])
     echo "<option value='" . $row['id'] ."' selected>" . $row['class'] ."</option>" ;
    else
     echo "<option value='" . $row['id'] ."'>" . $row['class'] ."</option>" ;
      }
    mysqli_close($con);
   }
echo "</select></td>";echo "<td>";
echo $class_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>STATUS</td><td></td>";
echo "<td><select name='status_i'>";
$con_params = require('./config/database.php'); $con_params = $con_params['gliding'];
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
   if (mysqli_connect_errno())
   {
    $errtext= "Failed to connect to Database: " . mysqli_connect_error();
   }
   else
   {
     echo "<option value='0'></option>";
     $qs= "SELECT * FROM membership_status ORDER BY status_name ASC";
     $r = mysqli_query($con,$qs);
     while($row = mysqli_fetch_array($r))
     {
     if ($status_f == $row['id'])
     echo "<option value='" . $row['id'] ."' selected>" . $row['status_name'] ."</option>" ;
    else
     echo "<option value='" . $row['id'] ."'>" . $row['status_name'] ."</option>" ;
      }
    mysqli_close($con);
   }
echo "</select></td>";echo "<td>";
echo $status_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>HOME PHONE</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($phone_home_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='phone_home_i' ";echo "size='20' ";echo "Value='";echo $phone_home_f;echo "' ";echo "maxlength='30'";echo ">";echo "</td>";echo "<td>";
echo $phone_home_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>MOBILE PHONE</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($phone_mobile_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='phone_mobile_i' ";echo "size='20' ";echo "Value='";echo $phone_mobile_f;echo "' ";echo "maxlength='30'";echo ">";echo "</td>";echo "<td>";
echo $phone_mobile_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>WORK PHONE</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($phone_work_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='phone_work_i' ";echo "size='20' ";echo "Value='";echo $phone_work_f;echo "' ";echo "maxlength='30'";echo ">";echo "</td>";echo "<td>";
echo $phone_work_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>EMAIL</td><td></td>";
echo "<td>";
echo "<input ";
if (strlen($email_err) > 0) echo "class='err' ";echo "type='text' ";echo "name='email_i' ";echo "size='50' ";echo "Value='";echo $email_f;echo "' ";echo "maxlength='50'";echo ">";echo "</td>";echo "<td>";
echo $email_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>SOLO</td><td></td>";
echo "<td><input type='checkbox' name='gone_solo_i[]' Value='1' ";if ($gone_solo_f ==1) echo "checked";echo "></td>";echo "<td>";
echo $gone_solo_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>ENABLE TEXTS</td><td></td>";
echo "<td><input type='checkbox' name='enable_text_i[]' Value='1' ";if ($enable_text_f ==1) echo "checked";echo "></td>";echo "<td>";
echo $enable_text_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>ENABLE EMALS</td><td></td>";
echo "<td><input type='checkbox' name='enable_email_i[]' Value='1' ";if ($enable_email_f ==1) echo "checked";echo "></td>";echo "<td>";
echo $enable_email_err; echo "</td></tr>";
}
?>
<?php if ($_SESSION['security'] & 16)
{?>
  <tr>
    <td colspan="4">Please edit these fields via the <b>Edit Documents</b> page:</td>
  </tr>
  <tr>
    <td colspan="4"><hr/></td>
  </tr>
  <tr>
    <td class='desc'>MEDICAL EXPIRES</td>
    <td></td>
    <td><input disabled="true" type='date' name='medical_expire_i' Value='<?=substr($medical_expire_f,0,10)?>'></td>
    <td><?=$medical_expire_err?></td>
  </tr>
  <tr>
    <td class='desc'>ICR EXPIRES</td>
    <td></td>
    <td><input disabled="true" type='date' name='icr_expire_i' Value='<?=substr($icr_expire_f,0,10)?>'></td>
    <td><?=$icr_expire_err?></td>
  </tr>
  <tr>
    <td class='desc'>BFR EXPIRES</td>
    <td></td>
    <td><input disabled="true" type='date' name='bfr_expire_i' Value='<?=substr($bfr_expire_f,0,10)?>'></td>
    <td><?=$bfr_expire_err?></td>
  </tr>
  <tr>
    <td colspan="4"><hr/></td>
  </tr>
<?php
}
?>

<?php if (true)
{
echo "<tr><td class='desc'>OFFICIAL OBSERVER</td><td></td>";
echo "<td><input type='checkbox' name='official_observer_i[]' Value='1' ";if ($official_observer_f ==1) echo "checked";echo "></td>";echo "<td>";
echo $official_observer_err; echo "</td></tr>";
}
?>
<?php if (true)
{
echo "<tr><td class='desc'>FIRST AIDER</td><td></td>";
echo "<td><input type='checkbox' name='first_aider_i[]' Value='1' ";if ($first_aider_f ==1) echo "checked";echo "></td>";echo "<td>";
echo $first_aider_err; echo "</td></tr>";
}
?>
<tr>
  <td class='desc'>ROLES</td>
  <td/>
  <td>
  <?php
    $allRoles->each(function($role) use ($userRoles) {
      $selected = ($userRoles) ? $userRoles->contains($role) : false;
      $key = "role-{$role->id}";
  ?>
    <input type='checkbox' value="<?=$role->id?>" <?=($selected) ? 'checked' : ''?> name='roles[]' id='<?=$key?>'/>
    <label for='<?=$key?>'><?=$role->name?></label>
  <?php
    });
  ?>
  </td>
</tr>
</table>
<table>
<tr>
  <td>
    <?php if ($recid > -1) echo "<input type='submit' formmethod='get' formaction='/app/members/${id_f}/edit' name = 'edit_member' value = 'Edit documents'>";?>
  </td>
  <td>
    <?php
    if($recid > -1) {
      $submitValue = 'Update';
    } else {
      $submitValue = 'Create';
    }
    ?>
    <input type="submit" name = 'tran' value = '<?=$submitValue?>'>
  </td>
  <td>
    <?php if ($recid > -1) echo "<input type='submit' name = 'del' value = 'Delete'>";?>
  </td>
  <td></td></tr>
</table>
<input type="hidden" name = 'updateid' value = '<?php echo $recid; ?>'>
</form>
</div>
<div>
<p><?php echo $errtext; ?></p>
<?php if ($DEBUG>0) echo "<p>".$sqltext."</p>"; ?>
</div>
</body>
</html>
