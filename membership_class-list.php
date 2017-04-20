<?php session_start(); ?>
<?php
require __DIR__.'/lrv/bootstrap/autoload.php';
use Illuminate\Database\Capsule\Manager as Capsule;

$cfg = require __DIR__.'/lrv/config/database.php';
$capsule = new Capsule;
$capsule->addConnection($cfg['connections']['mysql']);
$capsule->setAsGlobal();
$capsule->bootEloquent();
// set timezone for timestamps etc
date_default_timezone_set('UTC');

use App\MembershipClass;

$org=0;
if(isset($_SESSION['org'])) $org=$_SESSION['org'];
if(isset($_SESSION['security'])){
 if (!($_SESSION['security'] & 64)) {die("Secruity level too low for this page");}
}else{
 header('Location: Login.php');
 die("Please logon");
}

?>
<!DOCTYPE HTML>
<html>
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<head>
<title>Membership Classes</title>
<style>
<?php $inc = "./orgs/" . $org . "/heading2.css"; include $inc; ?>
</style>
<style>
<?php $inc = "./orgs/" . $org . "/menu1.css"; include $inc; ?></style>
<link rel="stylesheet" type="text/css" href="styletable1.css">
<script>function goBack() {window.history.back()}</script>
</head>
<body>
<?php $inc = "./orgs/" . $org . "/heading2.txt"; include $inc; ?>
<?php $inc = "./orgs/" . $org . "/menu1.txt"; include $inc; ?>
<?php
include 'timehelpers.php';
function dtfmt($dt)
{
	if (substr($dt,0,4) != '0000')
		return substr($dt,8,2).'/'.substr($dt,5,2).'/'.substr($dt,0,4);
	else
		return '';
}
$DEBUG=0;
$diagtext="";
$pageid=8;
$pkcol=1;
$pagesortdata = $_SESSION['pagesortdata'];
$colsort = $pagesortdata[$pageid];
if ($_SERVER["REQUEST_METHOD"] == "GET")
{
 if(isset($_GET['col']))
 {
  if($_GET['col'] != "" && $_GET['col'] != null)
  {
   $colsort = $_GET['col'];
   $pagesortdata[$pageid] = $colsort;
   $_SESSION['pagesortdata'] = $pagesortdata;
  }
 }
}
if ($colsort == 0)
 	$colsort = $pkcol;
?>
<div id="div1">
<div id="div2">
<table>
  <tr>
    <th class="<?php if ($colsort == 1) echo 'colsel' ?>"
        onclick="location.href='membership_class-list.php?col=1'" style='cursor:pointer;'>ID</th>
    <th class="<?php if ($colsort == 2) echo 'colsel' ?>"
        onclick="location.href='membership_class-list.php?col=2'" style='cursor:pointer;'>CREATE TIME</th>
    <th class="<?php if ($colsort == 3) echo 'colsel' ?>"
        onclick="location.href='membership_class-list.php?col=3'" style='cursor:pointer;'>CLASS</th>
    <th class="<?php if ($colsort == 4) echo 'colsel' ?>"
        onclick="location.href='membership_class-list.php?col=4'" style='cursor:pointer;'>Display</th>
    <th class="<?php if ($colsort == 5) echo 'colsel' ?>"
        onclick="location.href='membership_class-list.php?col=5'" style='cursor:pointer;'>Dropdown</th>
    <th class="<?php if ($colsort == 6) echo 'colsel' ?>"
        onclick="location.href='membership_class-list.php?col=6'" style='cursor:pointer;'>Allow Email</th>
  </tr>
<?php
$sort_col_name = NULL;
switch ($colsort) {
 case 0:
   $sort_col_name = "id";
break;
 case 1:
   $sort_col_name = "id";
   break;
 case 2:
   $sort_col_name = "create_time";
   break;
 case 3:
   $sort_col_name = "class";
   break;
 case 4:
   $sort_col_name = "disp_message_broadcast";
   break;
 case 5:
   $sort_col_name = "dailysheet_dropdown";
   break;
 case 6:
   $sort_col_name = "email_broadcast";
   break;
}

$classes = App\MembershipClass::where('org', $org)->orderBy($sort_col_name)->get();

$rownum = 0;
$classes->each(function($row)  use (&$rownum){
  $rownum = $rownum + 1;
  $style = "odd";
  if (($rownum % 2) == 0) $style = "even";
?>
  <tr class=''>
    <td class='right'>
      <a href='membership_class.php?id=<?= $row->id?>'><?= $row->id ?></a>
    </td>
    <td><?= $row->create_time?></td>
    <td><?= $row->class?></td>
    <td class='right'><?= $row->disp_message_broadcast?></td>
    <td><?= $row->dailysheet_dropdown?></td>
    <td><?= $row->email_broadcast?></td>
  </tr>
<?php
});
?>
</table>
</div>
</div>
<form id="form1" action='membership_class.php' method='GET'><input type='submit' value = 'Create New'>
</form>
</body>
</html>
