<?php 
  function require_security_level($security_level) {
    if(!isset($_SESSION['security'])){
      header('Location: Login.php');
      die("Please logon");
    }

    if (!($_SESSION['security'] & $security_level)){
      die("Secruity level too low for this page");
    }
  }
?>
