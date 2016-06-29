<html>
<head></head>
<body>
<?php
function tableDumpChange($db,$tablename,$insertafterfield,$valInsert)
{
 $recdone = false;
 echo "INSERT INTO ".$tablename." VALUES ";
 $q="SELECT * from ".$tablename." order by id";   
 $r = mysqli_query($db,$q);  
 while ($row = mysqli_fetch_array($r) )
 {
     $done1 = false;
     if ($recdone)
         echo ",";
     echo "(";
     $finfo = $r->fetch_fields();
     foreach ($finfo as $val)
     {
        if ($done1)
            echo ",";
        if ($val->type == 3)
        { 
            if (NULL == $row[$val->name])
                echo "NULL";
            else
                echo $row[$val->name];
        }
        else
        {
           echo "'" . str_replace("'", "\'", $row[$val->name]) . "'";
        }
        $done1 = true;
        if (strcmp($val->name,$insertafterfield) == 0)
        {
            echo ",";
            echo $valInsert;
        }
     }
     echo ")";  
     $recdone = true; 
 }
 echo ";";
}
$con_params = require('./config/database.php'); $con_params = $con_params['gliding']; 
$con=mysqli_connect($con_params['hostname'],$con_params['username'],$con_params['password'],$con_params['dbname']);
if (mysqli_connect_errno())
{
 echo "<p>Unable to connect to database</p>";
 exit();
}
tableDumpChange($con,'flights','start','0');
?>
</body>
</html>
