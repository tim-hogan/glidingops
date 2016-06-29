<?php session_start(); 
unset($_SESSION['security']);
header('Location: Login.php');
?>
