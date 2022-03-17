<?php session_start(); ?>
<?php
$org=0;
if(isset($_SESSION['org'])) $org=$_SESSION['org'];
if(isset($_SESSION['security'])){
 if (!($_SESSION['security'] & 1)) {die("Secruity level too low for this page");}
}else{
 header('Location: Login.php');
 die("Please logon");
}
?>
<!DOCTYPE HTML>
<html style="height: 100%">
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="initial-scale=1.0">
<head>
<title>Gliding-All Members</title>

<!-- JS Libraries -->
<?php
include 'jsLibraies.php';
?>

<style>
<?php $inc = "./orgs/" . $org . "/heading2.css"; include $inc; ?>
.main-box {
   height: 100%;
   display: flex;
   flex-direction: column;
}

.main-box .content {
   overflow-y: auto;
}

.sticky-header th {
   position: -webkit-sticky; position: sticky;
   top: 0;
}

.role-items li {
   white-space: nowrap;
}

.filters {
   /*display: flex;
   flex-direction: row;*/
}

.filterSection {
   display: flex;
   flex-direction: column;
   margin: 12px;
}

.filterSection.submit {
   display: flex;
   flex-direction: column-reverse;
}

.filterForm {
   display: flex;
   flex-wrap: wrap;
}

</style>
<style>
<?php $inc = "./orgs/" . $org . "/menu1.css"; include $inc; ?></style>
<link rel="stylesheet" type="text/css" href="styletable1.css">

<script>
function goBack() {window.history.back()}

$(document).ready(function () {
   $( "#roles-none" ).change(function() {
      if(this.checked) {
         $("#select-roles option:selected").removeAttr("selected");
         $('#select-roles').prop('disabled', true)
         $('#select-roles').selectpicker('refresh')
      } else {
         $('#select-roles').prop('disabled', false)
         $('#select-roles').selectpicker('refresh')
      }
   })

   $("#select-roles").change(function() {
      $( "#roles-none" ).prop('checked', false);;
   })

   $("#reset-filter").click(function() {
      // $("#filter-form").
   })

   $("[data-sort-id]").click(function(event) {
      const sortId = $(event.target).data('sort-id')
      $("#filter-form #col").val(sortId)
      $("#filter").click()
   })
})
</script>

<body style="height: 100%" class="main-box">
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
$pageid=12;
$pkcol=4;
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

$organisation  = App\Models\Organisation::find($org);
$allRoles      = App\Models\Role::all();
$allClasses    = $organisation->membershipClasses();
$allStatuses   = App\Models\MembershipStatus::all();

// Seleced filters
$filterDisabled = isset($_GET['filter-disabled']) ? true : false;
$filterRoles = isset($_GET['roles']) ? $_GET['roles'] : null;
$filterRolesNone = isset($_GET['roles-none']) ? $_GET['roles-none'] : null;
$filterClasses = isset($_GET['classes']) ? $_GET['classes'] : $organisation->membershipClasses()->where('class', '<>', App\Models\MembershipClass::CLASS_SHORT_TERM)->get()->map(function ($class) {
      return $class->id;
   })->all(); // all but short term
$filterStatuses = isset($_GET['statuses']) ? $_GET['statuses'] : [App\Models\MembershipStatus::activeStatus()->id];

if($filterRolesNone) {
   $filterRoles = null;
}

if ($colsort == 0)
 	$colsort = $pkcol;
?>
<div class="filters">
<form id="filter-form" method="get" action="<?=htmlspecialchars($_SERVER["PHP_SELF"])?>" class="filterForm">
   <input type="hidden" name="col" id="col" value="<?=$colsort?>" />
   <div class="filterSection">
      <div>
         <input type="checkbox" id='roles-none' name='roles-none' <?=($filterRolesNone) ? 'checked' : ''?>/>
         <label for="roles-none">Members with no roles</label>
      </div>
      <select multiple name="roles[]" id="select-roles" class="selectpicker" <?=($filterRolesNone) ? 'disabled' : ''?>>
         <?php
         $allRoles->each(function($role) use ($filterRoles) {
            $selected = ($filterRoles) ? in_array($role->id, $filterRoles) : false;
         ?>
         <option value="<?=$role->id?>" <?=($selected) ? 'selected' : ''?>><?=$role->name?></option>
         <?php
         });
         ?>
      </select>
   </div>
   <div class="filterSection">
      <label for="selectClasses">Classes</label>
      <select multiple name="classes[]" id="selectClasses" class="selectpicker">
         <?php
         $allClasses->each(function($class) use ($filterClasses) {
            $selected = ($filterClasses) ? in_array($class->id, $filterClasses) : false;
         ?>
         <option value="<?=$class->id?>" <?=($selected) ? 'selected' : ''?>><?=$class->class?></option>
         <?php
         });
         ?>
      </select>
   </div>
   <div class="filterSection">
      <label for="selectStatuses">Statuses</label>
      <select multiple name="statuses[]" id="selectStatuses" class="selectpicker">
         <?php
         $allStatuses->each(function($status) use ($filterStatuses) {
            $selected = ($filterStatuses) ? in_array($status->id, $filterStatuses) : false;
         ?>
         <option value="<?=$status->id?>" <?=($selected) ? 'selected' : ''?>><?=$status->status_name?></option>
         <?php
         });
         ?>
      </select>
   </div>
   <div class="filterSection submit">
      <input type="submit" value="Apply Filter" name="filter" id='filter' class="btn btn-primary"/>
   </div>
   <div class="filterSection submit">
      <input type="submit" value="Show All" name="filter-disabled" id='filter-disabled' class="btn btn-primary"/>
   </div>
</form>
</div>

<div id="div1" class='content'>
<table class="sticky-header">
   <thead>
   <tr>
      <th data-sort-id='1' <?=($colsort == 1) ? "class='colsel'" : ''?> style='cursor:pointer'>ID</th>
      <th data-sort-id='2' <?=($colsort == 2) ? "class='colsel'" : ''?> style='cursor:pointer'>MEM NUM</th>
      <th data-sort-id='3' <?=($colsort == 3) ? "class='colsel'" : ''?> style='cursor:pointer'>FIRSTNAME</th>
      <th data-sort-id='4' <?=($colsort == 4) ? "class='colsel'" : ''?> style='cursor:pointer'>SURNAME</th>
      <th data-sort-id='5' <?=($colsort == 5) ? "class='colsel'" : ''?> style='cursor:pointer'>DISPLAY NAME</th>
      <th>ROLES</th>
      <th data-sort-id='23' <?=($colsort == 23) ? "class='colsel'" : ''?> style='cursor:pointer'>CLASS</th>
      <th data-sort-id='24' <?=($colsort == 24) ? "class='colsel'" : ''?> style='cursor:pointer'>STATUS</th>
      <th data-sort-id='25' <?=($colsort == 25) ? "class='colsel'" : ''?> style='cursor:pointer'>HOME PHONE1</th>
      <th data-sort-id='26' <?=($colsort == 26) ? "class='colsel'" : ''?> style='cursor:pointer'>MOBILE PHONE</th>
      <th data-sort-id='28' <?=($colsort == 28) ? "class='colsel'" : ''?> style='cursor:pointer'>EMAIL</th>
      <th data-sort-id='30' <?=($colsort == 30) ? "class='colsel'" : ''?> style='cursor:pointer'>TEXT</th>
      <th data-sort-id='31' <?=($colsort == 31) ? "class='colsel'" : ''?> style='cursor:pointer'>EMIAL</th>
      <th>PHOTO</th>
   </tr>
   </thead>
<tbody>
<?php
$con_params = require('./config/database.php'); $con_params = $con_params['gliding'];
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
{
 echo "<p>Unable to connect to database</p>";
}

$whereRoles = "";
if(!$filterDisabled && $filterRoles) {
   $sqlValues = join(',', $filterRoles);
   $whereRoles .= " WHERE roles.id IN ({$sqlValues}) ";
}
$rolesSql=<<<SQL
   SELECT role_member.member_id AS member_id, GROUP_CONCAT(roles.name) AS role_names
   FROM role_member
   JOIN roles ON roles.id = role_member.role_id
   {$whereRoles}
   GROUP BY role_member.member_id
SQL;
$rolesJoin = (!$filterDisabled && $filterRoles) ? "JOIN ({$rolesSql})" : "LEFT JOIN ({$rolesSql})";

$sql=<<<SQL
SELECT members.id,members.member_id,members.firstname,members.surname,members.displayname,members.date_of_birth,members.mem_addr1,members.mem_addr2,members.mem_addr3,members.mem_addr4,members.mem_city,members.mem_country,members.mem_postcode,members.emerg_addr1,members.emerg_addr2,members.emerg_addr3,members.emerg_addr4,members.emerg_city,members.emerg_country,members.emerg_postcode,members.gnz_number,members.qgp_number,b.class,c.status_name,members.phone_home,members.phone_mobile,members.phone_work,members.email,members.gone_solo,members.enable_text,members.enable_email,members.official_observer,members.first_aider,roles.role_names
FROM members
LEFT JOIN membership_class b ON b.id = members.class
LEFT JOIN membership_status c ON c.id = members.status
{$rolesJoin} roles ON roles.member_id = members.id
SQL;

if ($_SESSION['org'] > 0){$sql .= " WHERE members.org=".$_SESSION['org'];}
if(!$filterDisabled && $filterClasses) {
   $sqlValues = join(',', $filterClasses);
   $sql .= " and b.id IN ({$sqlValues}) ";
}
if(!$filterDisabled && $filterRolesNone) {
   $sql .= " AND roles.member_id IS NULL ";
}
if(!$filterDisabled && $filterStatuses) {
   $sqlValues = join(',', $filterStatuses);
   $sql .= " AND c.id IN ({$sqlValues}) ";
}

$sql.=" ORDER BY ";
switch ($colsort) {
 case 0:
   $sql .= "surname";
break;
 case 1:
   $sql .= "id";
   break;
 case 2:
   $sql .= "member_id";
   break;
 case 3:
   $sql .= "firstname";
   break;
 case 4:
   $sql .= "surname";
   break;
 case 5:
   $sql .= "displayname";
   break;
 case 23:
   $sql .= "b.class";
   break;
 case 24:
   $sql .= "c.status_name";
   break;
 case 25:
   $sql .= "phone_home";
   break;
 case 26:
   $sql .= "phone_mobile";
   break;
 case 28:
   $sql .= "email";
   break;
 case 30:
   $sql .= "enable_text";
   break;
 case 31:
   $sql .= "enable_email";
   break;
}
$sql .= " ASC";
echo($diagtext);
$r = mysqli_query($con,$sql);
$rownum = 0;
while ($row = mysqli_fetch_array($r) )
{
 $rownum = $rownum + 1;
  echo "<tr class='";if (($rownum % 2) == 0)echo "even";else echo "odd";  echo "'>";if (true){echo "<td class='right'>";echo "<a href='Member?id=";echo $row[0];echo "'>";echo $row[0];echo "</a>";echo "</td>";}
if (true){echo "<td class='right'>";echo $row[1];echo "</td>";}
if (true){echo "<td>";echo $row[2];echo "</td>";}
if (true){echo "<td>";echo $row[3];echo "</td>";}
if (true){echo "<td>";echo $row[4];echo "</td>";}
?>
   <td>
      <ul class='role-items'>
      <?php
      if($row[33]) {
         $roles = explode(",", $row[33]);
         foreach ($roles as $role) {
      ?>
         <li><?=$role?></li>
      <?php
         };
      };
      ?>
      </ul>
   </td>
<?php
if (true){echo "<td>";echo $row[22];echo "</td>";}
if (true){echo "<td>";echo $row[23];echo "</td>";}
if (true){echo "<td>";echo $row[24];echo "</td>";}
if (true){echo "<td>";echo $row[25];echo "</td>";}
if (true){echo "<td>";echo $row[27];echo "</td>";}
if (true){echo "<td class='right'>";echo $row[29];echo "</td>";}
if (true){echo "<td class='right'>";echo $row[30];echo "</td>";}
if (true){echo "<td class='right'><a href='img/members/";echo str_replace("'","_",$row[4]);echo ".jpg' target='_blank'><img width='50' src='./img/members/";echo str_replace("'","_",$row[4]);echo ".jpg' alt=''/></a> </td>";}
  echo "</tr>";
}
?>
</tbody>
</table>
</div>
<form id="form1" action='Member' method='GET' style="margin-top: 12px">
   <input type='submit' value = 'Create New' class="btn btn-primary">
</form>
<?php if($DEBUG>0) echo "<p>".$diagtext."</p>";?>
</body>
</html>
