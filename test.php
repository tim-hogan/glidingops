<?php
$doc = new DOMDocument();
$strxml = "<Tag1>One Two & Three Four</Tag1>";
$strxml = str_replace("&","&amp;",$strxml);
if (!$doc->loadXML("<?xml version=\"1.0\"?>" . $strxml))
{
  echo "<p>Error</p>"; 
  echo $strxml;
}
else
{
  echo "<p>OK</p>"; 
  echo $strxml;
}
?>