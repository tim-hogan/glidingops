<?php
error_reporting(0);
$appservlang = $_GET['appservlang'];
switch ($appservlang) {
	case "th" :
		$appservlang = "th";
	break;
	default :
		$appservlang = "en";
	break;
}
/************************************************************************/
/* AppServ Open Project                                          */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2001 by Phanupong Panyadee (http://www.appservnetwork.com)         */
/* http://www.appservnetwork.com                                             */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
include("appserv/main.php");

$phpver=phpversion();
print "<html>
<head>
<title>AppServ Open Project "._APPVERSION."</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
<style>
<!-- Hide style for old browsers 
BODY          {font-family: Tahoma;font-size=\"10\"}
.headd { font-family: Tahoma ; font-size: 13pt; text-decoration:  none; }
.app { font-family: Tahoma ; font-size: 13pt; text-decoration:  none; }
.supp { font-family: Tahoma ; font-size: 20pt; text-decoration:  none; }
A:link    {font-family: Tahoma ; text-decoration: none; color: #0000FF}
A:visited {font-family: Tahoma ; text-decoration: none; color: #0000FF}
A:hover   {font-family: Tahoma ; text-decoration: none; color: #FF0000}
A:active  {font-family: Tahoma ; text-decoration: none; color: #FF0000}
-->
</style>
</head>
<body bgcolor=\"#FFFFFF\">

  <table border=\"0\" width=\"900\" align=\"center\" height=\"19\" >
    <tr bgcolor=\"#D2E9FF\"> 
      <td width=\"100%\" height=\"90\" align=\"center\" valign=\"absmiddle\"><font color=\"#000080\">
	  <span class=\"headd\"><strong><big>&nbsp; The AppServ Open Project - "._APPVERSION." "._FOR." "._OS." <br>Now you running on <font color=\"#FF0000\">PHP $phpver</font></big></strong></span></font></td>
    </tr>
  </table>

<div align=\"center\"> 
  <table width=\"800\" border=\"0\">
    <tr bgcolor=\"#F9FBFF\"> 
      <td height=\"344\"> 
        <blockquote> 
          <p><font color=\"#000080\"><span class=\"headd\"><strong><br>
	            <img src=\"appserv/members.gif\" width=\"20\"
    height=\"20\" align=\"absmiddle\"> <span class=\"app\"><a href=\""._LPHPMYADMIN."/\">"._PHPMYADMIN." "._VERSION." "._VPHPMYADMIN."</a><br>
	            <img
    src=\"appserv/PHP-logo.gif\" width=\"40\" height=\"21\" align=\"absmiddle\"> <a href=\"phpinfo.php\">"._PHPINFO." "._VERSION."</a> <br>
   </strong></span></font> </p>
          <p><span class=\"app\"><u>"._ABOUT." "._APPSERV." "._VERSION." "._APPVERSION." "._FOR." "._OS."</u><br>
            "._APPSERV." "._IS." <br><blockquote>
            <li><b><a href=\"http://httpd.apache.org\" target=\"_blank\"> "._APACHE."</b> "._VERSION." <b>"._VAPACHE."</b></a><br>
            <li><b><a href=\"http://www.php.net\" target=\"_blank\">"._PHP."</b> "._VERSION." <b>"._VPHP." & "._VPHP7."</b></a><br>
            <li><b><a href=\"http://www.mysql.com\" target=\"_blank\">"._MYSQL."</b> "._VERSION." <b>"._VMYSQL."</b></a><br>
            <li><b><a href=\"http://www.phpmyadmin.net\" target=\"_blank\">"._PHPMYADMIN."</b> "._VERSION." <b>"._VPHPMYADMIN."</b></a><br>
			</blockquote>
			</span> 
          </p>
        </blockquote>
        <ul>
          <li><a href=\"appserv/ChangeLog.txt\"><span class=\"app\">"._CHANGELOG."</span></a></li>
          <li> <a href=\"appserv/README-$appservlang.php?appservlang=$appservlang\"><span class=\"app\">"._README."</span></a></li>
          <li><a href=\"appserv/AUTHORS.txt\"><span class=\"app\">"._AUTHOR."</span></a></li>
          <li><a href=\"appserv/COPYING.txt\"><span class=\"app\">"._COPYING."</span></a></li>
		 </li> </ul></span>
          <span class=\"supp\"><b>"._OFSITE." : </b> <a href=\"http://www.AppServ.org/?appserv-"._APPVERSION."\" target=\"_blank\">http://www.AppServ.org</a><br></span>
          <span class=\"supp\"><b>"._HSUP." :</b> <a href=\"http://www.AppServHosting.com/?appserv-"._APPVERSION."\" target=\"_blank\">http://www.AppServHosting.com</a>  </span>
<br>
<br>
<span class=\"app\"><b> "._LANG." : </b><a href=\"index.php?appservlang=en\"><img src=\"appserv/flag-english.png\" width=\"30\" height=\"16\" align=\"absmiddle\" border=\"0\"></a>&nbsp; <a href=\"index.php?appservlang=th\"><img src=\"appserv/flag-thai.png\" width=\"30\" height=\"16\" align=\"absmiddle\" border=\"0\"></a>
<br><br>
      </td>
    </tr>
  </table>  

  <table border=\"0\" width=\"900\" align=\"center\" height=\"19\" >
    <tr> 
	  <td width=\"100%\" height=\"60\" align=\"center\" valign=\"absmiddle\" bgcolor=\"#D2E9FF\">
	  <font color=\"#000080\" class=\"headd\">&nbsp;&nbsp;&nbsp;<img src=\"appserv/softicon.gif\" width=\"20\" height=\"20\" align=\"absmiddle\">&nbsp;<b>"._SLOGAN."</b> </font></td>
    </tr>
  </table>


</body>
</html>
";
?>