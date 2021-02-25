<?php
session_start();

/*
 * Define the field flags
 *
*/
define('FIELD_NOT_NULL_FLAG',1);
define('FIELD_PRI_KEY_FLAG', 2);
define('FIELD_UNIQUE_KEY_FLAG', 4);
define('FIELD_BLOB_FLAG', 16);
define('FIELD_UNSIGNED_FLAG', 32);
define('FIELD_ZEROFILL_FLAG', 64);
define('FIELD_BINARY_FLAG', 128);
define('FIELD_ENUM_FLAG', 256);
define('FIELD_AUTO_INCREMENT_FLAG', 512);
define('FIELD_TIMESTAMP_FLAG', 1024);
define('FIELD_SET_FLAG', 2048);
define('FIELD_NUM_FLAG', 32768);
define('FIELD_PART_KEY_FLAG', 16384);
define('FIELD_GROUP_FLAG', 32768);
define('FIELD_UNIQUE_FLAG', 65536);


//Field types
define('FIELD_TYPE_TINYINT', 1);
define('FIELD_TYPE_SMALLINT', 2);
define('FIELD_TYPE_INTEGER', 3);
define('FIELD_TYPE_FLOAT', 4);
define('FIELD_TYPE_DOUBLE', 5);
define('FIELD_TYPE_TIMESTAMP', 7);
define('FIELD_TYPE_BIGINT', 8);
define('FIELD_TYPE_MEDIUMINT', 9);
define('FIELD_TYPE_DATE', 10);
define('FIELD_TYPE_TIME', 11);
define('FIELD_TYPE_DATETIME', 12);
define('FIELD_TYPE_YEAR', 13);
define('FIELD_TYPE_BIT', 16);
define('FIELD_TYPE_DECIMAL', 246);
define('FIELD_TYPE_VARCHAR', 253);
define('FIELD_TYPE_CHAR', 254);

function var_error_log( $object=null,$text='')
{
    ob_start();
    var_dump( $object );
    $contents = ob_get_contents();
    ob_end_clean();
    error_log( "{$text} {$contents}" );
}

function strAssociateEntry($n,$v,$l)
{
    $ret = '';
    for($i=0;$i<$l;$i++)
        $ret .= " ";
    $ret .= "\"{$n}\" => ";
    switch (gettype($v))
    {
        case "boolean":
            if ($v)
                $ret .= "true";
            else
                 $ret .= "false";
            break;
        case "integer":
            $ret .= strval($v);
            break;
        case "double":
            $ret .= strval($v);
            break;
        default:
            $ret .= "\"{$v}\"";
            break;
    }

    $ret .= ",\n";
    return $ret;
}

function outputArray($a,$level)
{
    $ret = "";
    $l = $level*4;
    foreach($a as $name => $v)
    {


        if (gettype($v) == 'array')
        {
            for($i=0;$i<$l;$i++)
                $ret .= " ";
            $ret .= "\"{$name}\" => [\n";
            $ret .= outputArray($v,$level+1);
            for($i=0;$i<$l;$i++)
                $ret .= " ";
            $ret .= "],\n";
        }
        else
        {
            $ret .= strAssociateEntry($name,$v,$l);
        }
    }
    //$ret .= "],\n";
    return $ret;
}

require_once "./includes/classSecure.php";
require "./includes/classFormList2.php";
require "./includes/classHTML.php";



if (! isset($_SESSION['csrf_key']))
    $_SESSION['csrf_key'] = base64_encode(openssl_random_pseudo_bytes(32));
?>


<?php

function OutputToFile($t,$f)
{
    $strtext = "<?php\n";
    $strtext .= "return [\n";
    $strtext .= outputArray($t,0);
    $strtext .= "];\n";
    $strtext .= "?>";

    file_put_contents($f,$strtext);
}

function bTF($txt,$fn,$v,$size='')
{
    echo "<tr>";
    echo "<td>{$txt}</td>";
    echo "<td><input type='text' name='{$fn}' value='{$v}'";
    if (strlen($size) > 0)
        echo " size='{$size}'";
    echo " /></td>";
    echo "</tr>";
}

function bTF2($tbl,$txt,$fn,$v,$size='')
{
    $row = new \devt\HTML\htmlRow($tbl);
    new \devt\HTML\htmlCell($row,$txt);
    $cell = new \devt\HTML\htmlCell($row);
    $inp = new \devt\HTML\htmlInput("text",$fn,$cell,$v);
}

function bBF($txt,$fn,$v)
{
    echo "<tr>";
    echo "<td>{$txt}</td>";
    echo "<td><input type='checkbox' name='{$fn}'";
    if ($v)
        echo " checked ";
    echo "/></td>";
    echo "</tr>";
    //echo "<div class='ff'><span>{$txt}</span><input type='checkbox' name='{$fn}'";
    //if ($v)
        //echo " checked ";
    //echo "/></div>";
}

function bDDF($txt,$fn,$v,$list)
{
    echo "<tr>";
    echo "<td>{$txt}</td>";
    echo "<td><select name='{$fn}'>";
    foreach($list as $val)
    {
        echo "<option value='{$val}'";
        if ($v == $val)
            echo " selected ";
        echo ">{$val}</option>";
    }
    echo "</select></td>";
    echo "</tr>";
}



function bIF($txt,$fn,$v)
{
    $vv = intval($v);
    echo "<tr>";
    echo "<td>{$txt}</td>";
    echo "<td><input type='text' name='{$fn}' value='{$vv}' /></td>";
    echo "</tr>";
    //echo "<div class='ff'><span>{$txt}</span><input type='text' name='{$fn}' value='{$vv}' /></div>";
}


function updateTextrec(&$a,$t)
{
    if (isset($_POST[$t]))
    {
        $a = $_POST[$t];
    }
}

function updateBoolanrec(&$a,$t)
{
    if (isset($_POST[$t]))
    {
        $a = FormList::getCheckboxField($t);
    }
}


function updateTextFieldInfo($table,$field,$attribute)
{
    global $g_def;
    if (isset($_POST["{$table}_{$field}_{$attribute}"]) )
        $g_def[$table] ['fields'] [$field] [$attribute] = $_POST["{$table}_{$field}_{$attribute}"];
}

function updateIntegerFieldInfo($table,$field,$attribute)
{
    global $g_def;
    if (isset($_POST["{$table}_{$field}_{$attribute}"]) )
        $g_def[$table] ['fields'] [$field] [$attribute] = intval($_POST["{$table}_{$field}_{$attribute}"]);
}

function updateTextFieldFormInfo($table,$field,$attribute)
{
    global $g_def;
    if (isset($_POST["{$table}_{$field}_form_{$attribute}"]) )
        $g_def[$table] ['fields'] [$field] ['form'] [$attribute] = $_POST["{$table}_{$field}_form_{$attribute}"];
}

function updateBooleanFieldFormInfo($table,$field,$attribute)
{
    global $g_def;
    if (isset($_POST["{$table}_{$field}_form_{$attribute}"]) )
    {
        $b = boolval(FormList::getCheckboxField("{$table}_{$field}_form_{$attribute}"));
        $g_def[$table] ['fields'] [$field] ['form'] [$attribute] = $b;
    }
}

function updateTextFieldListInfo($table,$field,$attribute)
{
    global $g_def;
    if (isset($_POST["{$table}_{$field}_list_{$attribute}"]) )
        $g_def[$table] ['fields'] [$field] ['list'] [$attribute] = $_POST["{$table}_{$field}_list_{$attribute}"];
}

function updateBooleanFieldListInfo($table,$field,$attribute)
{
    global $g_def;
    if (isset($_POST["{$table}_{$field}_list_{$attribute}"]) )
    {
        $b = boolval(FormList::getCheckboxField("{$table}_{$field}_list_{$attribute}"));
        $g_def[$table] ['fields'] [$field] ['list'] [$attribute] = $b;
    }
    else
    {
        $g_def[$table] ['fields'] [$field] ['list'] [$attribute] = false;
    }
}




$mode = null;

$g_def = null;
$g_table = null;
$g_field = null;

if (isset($_SESSION['def']))
    $g_def = $_SESSION['def'];

if (isset($_GET['v']))
{
    if ($_GET['v'] == 'output')
    {
        if ($g_def)
            $mode = "savefile";
    }
    if ($_GET['v'] == 'loadfromfile')
    {
        $mode = "loadfile";
    }
}

if (isset($_GET['t']))
{
    $g_table = $_GET['t'];
}

if (isset($_GET['f']))
{
    $v = $_GET['f'];
    $a = explode (":", $_GET['f']);
    $g_table = $a[0];
    $g_field = $a[1];
}

//Post
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if (isset($_POST['addgroup']) )
    {
        $g_table = $_POST['table'];
        if ($g_table)
        {
            $cnt = 0;
            $newname = "newgroup" . strval($cnt);
            while (isset($g_def[$g_table] ['form'] ['groups'] [$newname]))
            {
                $cnt++;
                $newname = "newgroup" . strval($cnt);
            }

            $g_def[$g_table] ['form'] ['groups'] [$newname] = array();
            $g_def[$g_table] ['form'] ['groups'] [$newname] ["heading"] = "";
            $g_def[$g_table] ['form'] ['groups'] [$newname] ["introduction1"] = "";
            $g_def[$g_table] ['form'] ['groups'] [$newname] ["introduction2"] = "";
            $g_def[$g_table] ['form'] ['groups'] [$newname] ["introduction3"] = "";
        }
    }

    if (isset($_POST['tableupdate']))
    {
        if ($g_def)
        {
            $g_table = $_POST['table'];
            updateTextrec($g_def[$g_table] ['global'] ['primary_key'],'primary_key');
            updateTextrec($g_def[$g_table] ['global'] ['selector_text'],'selector_text');
            updateBoolanrec($g_def[$g_table] ['global'] ['single_record'],'single_record');

            updateTextrec($g_def[$g_table] ['form'] ['heading'],'formheading');
            updateTextrec($g_def[$g_table] ['form'] ['introduction'],'formintroduction');

            updateTextrec($g_def[$g_table] ['form'] ['classes'] ['div'] ['inputtext'],'form_inputtext');
            updateTextrec($g_def[$g_table] ['form'] ['classes'] ['div'] ['emailtext'],'form_emailtext');
            updateTextrec($g_def[$g_table] ['form'] ['classes'] ['div'] ['passwordtext'],'form_passwordtext');
            updateTextrec($g_def[$g_table] ['form'] ['classes'] ['div'] ['textarea'],'form_textarea');
            updateTextrec($g_def[$g_table] ['form'] ['classes'] ['div'] ['checkbox'],'form_checkbox');
            updateTextrec($g_def[$g_table] ['form'] ['classes'] ['div'] ['choice'],'form_choice');
            updateTextrec($g_def[$g_table] ['form'] ['classes'] ['div'] ['dropdown'],'form_dropdown');
            updateTextrec($g_def[$g_table] ['form'] ['classes'] ['div'] ['fk'],'form_fk');

            foreach($g_def[$g_table] ['form'] ['groups'] as $name => $group)
            {
                if (isset($_POST["form_group_{$name}"]) )
                {
                    if ($_POST["form_group_{$name}"] != $name)
                    {
                        //The name has changed
                        $newname = $_POST["form_group_{$name}"];
                        $g_def[$g_table] ['form'] ['groups'] [$newname] = array();
                        updateTextrec($g_def[$g_table] ['form'] ['groups'] [$newname] ['heading'],"form_group_{$name}_heading");
                        updateTextrec($g_def[$g_table] ['form'] ['groups'] [$newname] ['introduction1'],"form_group_{$name}_introduction1");
                        updateTextrec($g_def[$g_table] ['form'] ['groups'] [$newname] ['introduction2'],"form_group_{$name}_introduction2");
                        updateTextrec($g_def[$g_table] ['form'] ['groups'] [$newname] ['introduction3'],"form_group_{$name}_introduction3");
                        unset($g_def[$g_table] ['form'] ['groups'] [$name]);
                    }
                    else
                    {
                        updateTextrec($g_def[$g_table] ['form'] ['groups'] [$name] ['heading'],"form_group_{$name}_heading");
                        updateTextrec($g_def[$g_table] ['form'] ['groups'] [$name] ['introduction1'],"form_group_{$name}_introduction1");
                        updateTextrec($g_def[$g_table] ['form'] ['groups'] [$name] ['introduction2'],"form_group_{$name}_introduction2");
                        updateTextrec($g_def[$g_table] ['form'] ['groups'] [$name] ['introduction3'],"form_group_{$name}_introduction3");
                    }
                }
            }

            updateTextrec($g_def[$g_table] ['list'] ['type'] ,'list_type');
            updateBoolanrec($g_def[$g_table] ['list'] ['single_record'],'list_single_record');
            updateTextrec($g_def[$g_table] ['list'] ['heading'] ,'list_heading');
            updateTextrec($g_def[$g_table] ['list'] ['introduction'] ,'list_introduction');
            updateTextrec($g_def[$g_table] ['list'] ['default_order'] ,'list_default_order');
            updateTextrec($g_def[$g_table] ['list'] ['default_where'] ,'list_default_where');

            $fields = $g_def[$g_table] ['fields'];
            foreach($fields as $name => $field)
            {
                $b = boolval(FormList::getCheckboxField("{$g_table}_{$name}_dispform"));
                $g_def[$g_table] ['fields'] [$name] ['form'] ['display'] = $b;
                $b = boolval(FormList::getCheckboxField("{$g_table}_{$name}_displist"));
                $g_def[$g_table] ['fields'] [$name] ['list'] ['display'] = $b;
            }

        }
    }

    if (isset($_POST["fieldupdate"]))
    {


        $table = $_POST['table'];
        $field = $_POST['field'];

        if ($g_def)
        {
            $g_table = $table;
            $g_field = $field;
            updateTextFieldInfo($table,$field,"type");
            updateTextFieldInfo($table,$field,"tag");
            updateTextFieldInfo($table,$field,"sub-tag");
            updateIntegerFieldInfo($table,$field,"size");
            updateIntegerFieldInfo($table,$field,"maxlength");
            updateIntegerFieldInfo($table,$field,"cols");
            updateIntegerFieldInfo($table,$field,"rows");
            updateTextFieldInfo($table,$field,"errname");
            updateIntegerFieldInfo($table,$field,"decimalplaces");
            updateTextFieldInfo($table,$field,"currency_symbol");
            updateIntegerFieldInfo($table,$field,"security_view");
            updateIntegerFieldInfo($table,$field,"security_edit");
            updateTextFieldInfo($table,$field,"fk_table");
            updateTextFieldInfo($table,$field,"fk_index");
            updateTextFieldInfo($table,$field,"fk_display");
            updateTextFieldInfo($table,$field,"fk_where");
            updateTextFieldInfo($table,$field,"fk_order");


            updateBooleanFieldFormInfo($table,$field,"display");
            updateTextFieldFormInfo($table,$field,"formlabel");
            updateTextFieldFormInfo($table,$field,"title");
            updateBooleanFieldFormInfo($table,$field,"required");
            updateBooleanFieldFormInfo($table,$field,"trim");
            updateTextFieldFormInfo($table,$field,"default");
            updateTextFieldFormInfo($table,$field,"errtext");
            updateTextFieldFormInfo($table,$field,"formlabel");
            updateTextFieldFormInfo($table,$field,"posttext");
            updateTextFieldFormInfo($table,$field,"group");


            updateBooleanFieldListInfo($table,$field,"display");
            updateTextFieldListInfo($table,$field,"heading");
            updateBooleanFieldListInfo($table,$field,"anchor");
            updateTextFieldListInfo($table,$field,"displayoption");


            //Loop here lloking for choice fields
            if (isset($_POST["{$table}_{$field}_type"]) && $_POST["{$table}_{$field}_type"] == "choice")
            {
                $g_def[$table] ['fields'] [$field] ['choice'] = array();

                for ($cnt = 0; $cnt < 50; $cnt++)
                {
                    if (isset($_POST["{$table}_{$field}_form_choice_text{$cnt}"]) )
                    {
                        $ch = array();
                        $ch ['text'] = $_POST["{$table}_{$field}_form_choice_text{$cnt}"];
                        $ch ['value'] = $_POST["{$table}_{$field}_form_choice_value{$cnt}"];
                        $ch ['selected'] = $_POST["{$table}_{$field}_form_choice_selected{$cnt}"];
                        array_push($g_def[$table] ['fields'] [$field] ['choice'] ,$ch);
                    }
                }
            }
        }
    }


    if (isset($_POST["saveform"]))
    {
        if ($g_def)
        {
            if (isset($_POST["filename"]))
            {
                $filename = trim($_POST["filename"]);
                OutputToFile($g_def,$filename);
            }
        }
    }

    if (isset($_POST["loadform"]) )
    {
        $g_def = null;
        if (isset($_POST["filename"]))
        {
            $filename = trim($_POST["filename"]);
            if (file_exists($filename))
            {
                $g_def = require($filename);
                $_SESSION['filename'] = $filename;
            }
        }

    }

    $_SESSION['def'] = $g_def;
}

$fn = "./forms/formparams.php";
if (isset($_SESSION['filename']))
    $fn = $_SESSION['filename'];
?>

<!DOCTYPE HTML>
<html>
<head>
    <meta name="viewport" content="width=device-width" />
    <meta name="viewport" content="initial-scale=1.0" />
    <title>FormBuilder</title>
    <link rel='stylesheet' type='text/css' href='css/scheme.css' />
    <link rel='stylesheet' type='text/css' href='css/framework.css' />
    <link rel='stylesheet' type='text/css' href='css/form.css' />
    <link rel='stylesheet' type='text/css' href='css/list.css' />
    <style>
        body {font-family: Arial, Helvetica, sans-serif;font-size: 10pt;margin: 0;padding: 0;}
        #container {}
        #header {background-color: #666;color: white;padding: 10px;}
        #header p {font-size: 24pt; font-family:'Times New Roman', Times, serif; text-align:center;}
        #menu {padding: 8px;border:solid 1px #777;}
        #menu div {display:inline-block; margin-right: 12px;}
        #menu a {text-decoration: none;}
        #main {padding: 0;}
        #fileload {display: none;padding: 20px;}
        #fileload h1 {color: #777;}
        #fileload input {display: block; font-size: 14pt;}
        #fileload input[type="submit"] {margin-top: 20px; display: block; font-size: 14pt;}
        #filesave {display: none;padding: 20px;}
        #filesave h1 {color: #777;}
        #filesave input {display: block; font-size: 14pt;}
        #filesave input[type="submit"] {margin-top: 20px; display: block; font-size: 14pt;}
        #flex {display: flex;}
        #left {background-color: #ddf;padding: 8px;}
        #left ul {list-style-type: none;padding-left: 8px;}
        #right1 {padding: 20px;border: solid 1px #888;border-top: none;background-color: #ffd;}
        #right2 {padding: 20px; border-right: solid 1px #888;border-bottom: solid 1px #888;background-color: #ffd;}
        #right3 {padding: 20px; border-right: solid 1px #888;border-bottom: solid 1px #888;background-color: #f8f8f8}
        #form1 span {margin-right: 8px;}
        .section {margin-top: 10px;margin-bottom: 16px; border: solid 1px #aaa;padding: 12px;border-radius: 6px;}
        .secheading {margin: 0;position: relative;top: -20px;background-color: #ffd;display: inline-block;}
        .ff {margin-bottom: 16px;}
    </style>
    <script>
                        <?php
        if ($mode && $mode == "loadfile")
        {
            echo "var g_mode = 'loadfile';";
        }
        else
            if ($mode && $mode == "savefile")
            {
                echo "var g_mode = 'savefile';";
            }
            else
        {
            echo "var g_mode = null;";
        }
                        ?>
        function start() {
            if (g_mode == 'loadfile') {
                document.getElementById('fileload').style.display = 'block';
                document.getElementById('flex').style.display = 'none';
            }
             if (g_mode == 'savefile') {
                document.getElementById('filesave').style.display = 'block';
                document.getElementById('flex').style.display = 'none';
            }
        }
    </script>
</head>
<body onload="start()">
    <div id="container">
        <div id="header">
            <p>deVT Form Builder Version 1</p>
        </div>
        <div id="menu">
            <div><a href="FormBuilder.php?v=loadfromfile">LOAD FROM FILE</a></div>
            <div><a href="FormBuilder.php?v=output">SAVE TO FILE</a></div>
        </div>
        <div id="main">
            <div id="filesave">
                <h1>SAVE FILE</h1>
                <form method='POST' action='<?php echo $_SERVER["PHP_SELF"]?>'>
                    <label for="savefilename">ENTER FILE NAME</label>
                    <input id="savefilename" type="text" name="filename" value="<?php echo $fn;?>" size="60" />
                    <input type="submit" value="SAVE FORM DATA" name="saveform" />
                </form>
            </div>
            <div id="fileload">
                <h1>LOAD FROM FILE</h1>
                <form method='POST' action='<?php echo $_SERVER["PHP_SELF"]?>'>
                    <label for="loadfilename">ENTER FILE NAME</label>
                    <input id="loadfilename" type="text" name="filename" value="<?php echo $fn;?>" size="60" />
                    <input type="submit" value="LOAD" name="loadform" />
                </form>
            </div>
            <div id="flex">
                <div id="left"><?php
                        if ($g_def)
                        {
                            echo "<ul>";
                            foreach ($g_def as $name => $table)
                            {
                                echo "<li><a href='FormBuilder.php?t={$name}'>{$name}</a></li>";
                            }
                            echo "</ul>";
                        }
                        ?></div>
                <div id="right1"><?php
                        if ($g_table)
                        {
                            $params = $g_def[$g_table];
                            $global = $params['global'];
                            $form = $params['form'];
                            $list = $params['list'];
                            $fields = $params['fields'];
                            echo "<h1>TABLE {$g_table}</h1>";
                            echo "<div id='form1'>";
                            echo "<form method='POST' action='{$_SERVER["PHP_SELF"]}'>";
                                echo "<div class='section'>";
                                    echo "<p class='secheading'>GLOBAL</p>";
                                    echo "<table>";
                                    bTF('table','table',$global['table']);
                                    $prim_key = '';
                                    if (isset($global['primary_key']))
                                        $prim_key = $global['primary_key'];
                                    bTF('primary_key','primary_key',$prim_key);
                                    bTF('selector_text','selector_text',$global['selector_text']);
                                    bBF('single_record','single_record',$global['single_record']);
                                    echo "</table>";
                                echo "</div>";
                                echo "<div class='section'>";
                                    echo "<p class='secheading'>FORM</p>";
                                    echo "<table>";
                                    bTF('heading','formheading',$form['heading'],40);
                                    bTF('introduction','formintroduction',$form['introduction'],50);
                                    echo "</table>";
                                    echo "<div class='section'>";
                                        echo "<p class='secheading'>CLASSES</p>";
                                        echo "<div class='section'>";
                                            echo "<p class='secheading'>DIV</p>";
                                            echo "<table>";
                                            bTF('inputtext','form_inputtext',$form['classes'] ['div'] ['inputtext']);
                                            bTF('emailtext','form_emailtext',$form['classes'] ['div'] ['emailtext']);
                                            bTF('passwordtext','form_passwordtext',$form['classes'] ['div'] ['passwordtext']);
                                            bTF('checkbox','form_checkbox',$form['classes'] ['div'] ['checkbox']);
                                            bTF('choice','form_choice',$form['classes'] ['div'] ['choice']);
                                            bTF('dropdown','form_dropdown',$form['classes'] ['div'] ['dropdown']);
                                            bTF('fk','form_fk',$form['classes'] ['div'] ['fk']);
                                            echo "</table>";
                                            echo "</div>";
                                    echo "</div>";
                                    echo "<div class='section'>";
                                    echo "<p class='secheading'>GROUPS</p>";
                                    $idx = 0;
                                    foreach($form['groups'] as $name => $group)
                                    {
                                        echo "<table>";
                                        bTF('GroupName',"form_group_{$name}",$name);
                                        echo "</table>";
                                        echo "<div class='section'>";
                                            echo "<p class='secheading'>{$name}</p>";
                                            echo "<table>";
                                            bTF('heading',"form_group_{$name}_heading",$group['heading'],30);
                                            bTF('introduction1',"form_group_{$name}_introduction1",$group['introduction1'],30);
                                            bTF('introduction2',"form_group_{$name}_introduction2",$group['introduction2'],30);
                                            bTF('introduction3',"form_group_{$name}_introduction3",$group['introduction3'],30);
                                            echo "</table>";
                                            echo "</div>";
                                        $idx++;
                                    }
                                    echo "<input type='submit' name='addgroup' value='ADD GROUP' />";
                                    echo "</div>";
                                echo "</div>";
                                echo "<div class='section'>";
                                echo "<p class='secheading'>LIST</p>";
                                echo "<table>";
                                bDDF('type',"list_type",$list['type'],["plain","checkbox"]);
                                bBF('record_selector',"list_record_selector",$list['record_selector']);
                                bTF('heading',"list_heading",$list['heading'],30);
                                bTF('introduction',"list_introduction",$list['introduction'],30);
                                bTF('default_order',"list_default_order",$list['default_order']);
                                bTF('default_where',"list_default_where",$list['default_where']);
                                echo "</table>";
                                echo "</div>";
                                echo "<div class='section'>";
                                echo "<p class='secheading'>FIELDS</p>";
                                echo "<table>";
                                echo "<tr><th></th><th colspan='2'>DISPLAY</th></tr>";
                                echo "<tr><th>NAME</th><th>FORM</th><th>LIST</th></tr>";
                                foreach($fields as $name => $field)
                                {
                                    echo "<tr>";
                                    echo "<td><a href='FormBuilder.php?f={$g_table}:{$name}'>{$name}</a></td>";
                                    echo "<td><input type='checkbox' name='{$g_table}_{$name}_dispform'";
                                    if ($field['form'] ['display'])
                                        echo " checked ";
                                    echo "/></td>";
                                    echo "<td><input type='checkbox' name='{$g_table}_{$name}_displist'";
                                    if ($field['list'] ['display'])
                                        echo " checked ";
                                    echo "/></td>";
                                    echo "</tr>";
                                }
                                echo "</table>";
                                echo "</div>";

                                echo "<input type='hidden' name='table' value='{$g_table}'/>";
                                echo "<input type='submit' name='tableupdate' value='CONFIRM CHANGE' />";
                            echo "</form>";
                            echo "</div>";
                        }
?></div>
                <?php
                    if ($g_field)
                    {
                        echo "<div id='right2'>";
                        echo "<div class='section'>";
                        echo "<p class='secheading'>FIELD DATA FOR {$g_field}</p>";
                        echo "<form method='POST' action='{$_SERVER["PHP_SELF"]}'>";
                        echo "<table>";
                        bDDF('type',"{$g_table}_{$g_field}_type",$fields[$g_field] ['type'],['text','boolean','integer','decimal','percent','currency','date', 'time', 'datetime','button','choice','fk','hidden']);
                        bDDF('tag',"{$g_table}_{$g_field}_tag",$fields[$g_field] ['tag'],['input','textarea']);
                        bTF('sub-tag',"{$g_table}_{$g_field}_sub-tag",$fields[$g_field] ['sub-tag']);
                        bBF('dbfield',"{$g_table}_{$g_field}_dbfield",$fields[$g_field] ['dbfield']);
                        bIF('size',"{$g_table}_{$g_field}_size",$fields[$g_field] ['size']);
                        bIF('maxlength',"{$g_table}_{$g_field}_maxlength",$fields[$g_field] ['maxlength']);
                        bIF('cols',"{$g_table}_{$g_field}_cols",$fields[$g_field] ['cols']);
                        bIF('rows',"{$g_table}_{$g_field}_rows",$fields[$g_field] ['rows']);
                        bTF('errname',"{$g_table}_{$g_field}_errname",$fields[$g_field] ['errname']);
                        bIF('decimalplaces',"{$g_table}_{$g_field}_decimalplaces",$fields[$g_field] ['decimalplaces'],2);
                        bTF('currency_symbol',"{$g_table}_{$g_field}_currency_symbol",$fields[$g_field] ['currency_symbol'],1);
                        bIF('security_view',"{$g_table}_{$g_field}_secuity_view",$fields[$g_field] ['security_view']);
                        bIF('security_edit',"{$g_table}_{$g_field}_security_edit",$fields[$g_field] ['security_edit']);
                        echo "</table>";

                        $div = new \devt\HTML\htmlDiv(null,null,null,'section');
                        new \devt\HTML\htmlP($div,"FOREIGN KEYS",null,'secheading');
                        $tbl = new \devt\HTML\htmlTable($div);

                        bTF2($tbl,"fk_table","{$g_table}_{$g_field}_fk_table",$fields[$g_field] ['fk_table']);
                        bTF2($tbl,"fk_index","{$g_table}_{$g_field}_fk_index",$fields[$g_field] ['fk_index']);
                        bTF2($tbl,"fk_display","{$g_table}_{$g_field}_fk_display",$fields[$g_field] ['fk_display']);
                        bTF2($tbl,"fk_where","{$g_table}_{$g_field}_fk_where",$fields[$g_field] ['fk_where']);
                        bTF2($tbl,"fk_order","{$g_table}_{$g_field}_fk_order",$fields[$g_field] ['fk_order']);

                        echo $div->toString();

                        echo "<div class='section'>";
                        echo "<p class='secheading'>FORM</p>";
                        echo "<table>";
                        bBF('display',"{$g_table}_{$g_field}_form_display",$fields[$g_field] ['form'] ['display']);
                        bTF('formlabel',"{$g_table}_{$g_field}_form_formlabel",$fields[$g_field] ['form'] ['formlabel']);
                        bTF('title',"{$g_table}_{$g_field}_form_title",$fields[$g_field] ['form'] ['title']);
                        bBF('required',"{$g_table}_{$g_field}_form_required",$fields[$g_field] ['form'] ['required']);
                        bTF('default',"{$g_table}_{$g_field}_form_default",$fields[$g_field] ['form'] ['default']);
                        bTF('errtext',"{$g_table}_{$g_field}_form_errtext",$fields[$g_field] ['form'] ['errtext']);
                        bTF('posttext',"{$g_table}_{$g_field}_form_posttext",$fields[$g_field] ['form'] ['posttext']);
                        bBF('trim',"{$g_table}_{$g_field}_form_trim",$fields[$g_field] ['form'] ['trim']);
                        bTF('group',"{$g_table}_{$g_field}_form_group",$fields[$g_field] ['form'] ['group']);
                        echo "</table>";

                        echo "<div class='section'>";
                        echo "<p class='secheading'>CHOICE</p>";
                        $cnt = 0;
                        foreach($fields[$g_field] ['form'] ['choice'] as $ca)
                        {
                            echo "<div class='section'>";
                            echo "<p class='secheading'>CHOICE</p>";
                            echo "<table>";
                            bTF('text',"{$g_table}_{$g_field}_form_choice_text{$cnt}",$ca['text']);
                            bTF('value',"{$g_table}_{$g_field}_form_choice_value{$cnt}",$ca['value']);
                            bTF('selected',"{$g_table}_{$g_field}_form_choice_selected{$cnt}",$ca['selected']);
                            $cnt++;
                            echo "</table>";
                            echo "</div>";
                        }
                        echo "</div>";
                        echo "</div>";

                        echo "<div class='section'>";
                        echo "<p class='secheading'>LIST</p>";
                        echo "<table>";
                        bBF('display',"{$g_table}_{$g_field}_list_display",$fields[$g_field] ['list'] ['display']);
                        bTF('heading',"{$g_table}_{$g_field}_list_heading",$fields[$g_field] ['list'] ['heading']);
                        bBF('anchor',"{$g_table}_{$g_field}_list_anchor",$fields[$g_field] ['list'] ['anchor']);
                        bDDF('displayoption',"{$g_table}_{$g_field}_list_displayoption",$fields[$g_field] ['list'] ['displayoption'],['none','tick']);
                        echo "</table>";
                        echo "</div>";


                        echo "<input type='hidden' name='table' value='{$g_table}'/>";
                        echo "<input type='hidden' name='field' value='{$g_field}'/>";
                        echo "<input type='submit' name='fieldupdate' value='CONFIRM CHANGE' />";
                        echo "</form>";
                        echo "</div>";
                        echo "</div>";
                    }
                ?>
                <?php
                    if ($g_table)
                    {
                        echo "<div id='right3'>";
                        echo "<div class='form'>";
                        echo "<form method='POST' autocomplete='off' action='{$_SERVER["PHP_SELF"]}'>";
                        $FL = new FormList($g_def[$g_table]);
                        $FL->buildFormFields(null,null);
                        echo "<div class='submit'>";
                            $v = FormList::encryptParam("table=server&action=create");
                            echo "<input type='hidden' name='v' value='{$v}' />";
                            echo "<input type='submit' name='_server_new' value='CREATE NEW' />";
                        echo "</div>";
                        echo "</form>";
                        echo "</div>";
                        echo "</div>";
                   }
                ?>
            </div>
        </div>
    </div>
</body>
</html>
